<?php

/**
 * This file is part of the blockstar/zumokit-bundle package.
 *
 * (c) DLabs / Blockstar 2019
 * Author Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blockstar\ZumokitBundle\Service\Token;

use Blockstar\ZumokitBundle\Exception\TokenException;
use Lcobucci\JWT;

/**
 * Class JWTDecoder
 *
 * @package Blockstar\ZumokitBundle\Service\Token
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class JWTDecoder
{
    /**
     * @var string|null
     */
    private $publicKey;

    /**
     * @var string|null
     */
    private $privateKey;

    /**
     * @var string|null
     */
    private $secret;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param string $token The token string.
     * @param string $publicKey
     *
     * @return \Lcobucci\JWT\Token|null
     * @throws \Blockstar\ZumokitBundle\Exception\TokenException
     */
    public function decode(string $token, string $publicKey): ?JWT\Token
    {
        $parser = new JWT\Parser();
        $parsed = $parser->parse($token);
        $this->verifyParsedToken($parsed, $publicKey);
        $this->validateParsedToken($parsed);

        return $parsed;
    }

    /**
     * @param \Lcobucci\JWT\Token $parsed
     * @param string              $publicKey
     *
     * @throws \Blockstar\ZumokitBundle\Exception\TokenException
     */
    private function verifyParsedToken(JWT\Token $parsed, string $publicKey): void
    {
        $signer   = new JWT\Signer\Rsa\Sha256();
        $key = new JWT\Signer\Key('file://' . $publicKey);

        if ($parsed->verify($signer, $key)) {
            return;
        }

        throw new TokenException('Token signature is not valid.');
    }

    /**
     * @param \Lcobucci\JWT\Token $parsed
     *
     * @throws \Blockstar\ZumokitBundle\Exception\TokenException
     */
    private function validateParsedToken(JWT\Token $parsed): void
    {
        $validation = new JWT\ValidationData();
        $validation->setCurrentTime(time());

        if ($parsed->validate($validation)) {
            return;
        }
        throw new TokenException('Token is expired or otherwise invalid.');
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }

    /**
     * @param string|null $publicKey
     *
     * @return JWTDecoder
     */
    public function setPublicKey(?string $publicKey): ?JWTDecoder
    {
        $this->publicKey = $publicKey;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * @param string|null $privateKey
     *
     * @return JWTDecoder
     */
    public function setPrivateKey(?string $privateKey): ?JWTDecoder
    {
        $this->privateKey = $privateKey;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @param string|null $secret
     *
     * @return JWTDecoder
     */
    public function setSecret(?string $secret): ?JWTDecoder
    {
        $this->secret = $secret;
        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): ?\Psr\Log\LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return JWTDecoder
     */
    public function setLogger(\Psr\Log\LoggerInterface $logger): self
    {
        $this->logger = $logger;
        return $this;
    }
}
