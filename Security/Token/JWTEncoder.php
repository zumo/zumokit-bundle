<?php

/**
 * This file is part of the zumo/zumokit-bundle package.
 *
 * (c) DLabs / Zumo 2019
 * Author Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zumo\ZumokitBundle\Security\Token;

use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Model\ZumoUser;
use Zumo\ZumokitBundle\Security\Claim\APP;
use Zumo\ZumokitBundle\Security\Claim\BaseClaim;
use Zumo\ZumokitBundle\Security\Claim\OTT;
use Zumo\ZumokitBundle\Security\Claim\PAT;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Keychain;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Psr\Log\LoggerInterface;

/**
 * Class JWTEncoder
 *
 * @package Zumo\ZumokitBundle\Service\Token
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class JWTEncoder
{
    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $passPhrase;

    /**
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * JWTEncoder constructor.
     *
     * @param string                                 $privateKey
     * @param string                                 $passPhrase
     * @param \Zumo\ZumokitBundle\Model\ZumoApp $app
     * @param \Psr\Log\LoggerInterface               $logger
     */
    public function __construct(string $privateKey, string $passPhrase, ZumoApp $app, LoggerInterface $logger)
    {
        $this->privateKey = $privateKey;
        $this->passPhrase = $passPhrase;
        $this->app        = $app;
        $this->logger     = $logger;
    }

    /**
     * Creates a signed JSON web token from the specified combination of ZumoKit
     * user and app.
     *
     * @param \Zumo\ZumokitBundle\Model\ZumoUser           $user
     * @param \Zumo\ZumokitBundle\Security\Claim\BaseClaim $claims
     *
     * @return \Lcobucci\JWT\Token
     */
    public function encode(ZumoUser $user, BaseClaim $claims): ?Token
    {
        $signer   = new Sha256();
        $keychain = new Keychain();
        $token    = new Builder();

        // todo test parse_url on non-FQ URL string (without scheme)
        $token =
            $token->setIssuer('https://' . $this->app->getPrimaryDomain())
            ->setAudience($this->app->getId())
            ->setIssuedAt(time())
            ->setExpiration(time() + $claims->getExp());

        $token = $this->setClaims($token, $user, $claims);

        $token =
            $token->sign($signer, $keychain->getPrivateKey('file://' . $this->privateKey, $this->passPhrase))
            ->getToken();

        return $token;
    }

    /**
     * @param \Lcobucci\JWT\Builder                             $builder
     * @param \Zumo\ZumokitBundle\Model\ZumoUser           $user
     * @param \Zumo\ZumokitBundle\Security\Claim\BaseClaim $claims
     *
     * @return \Lcobucci\JWT\Builder
     */
    private function setClaims(Builder $builder, ZumoUser $user, BaseClaim $claims): ?Builder
    {
        // todo Either use or remove!
        switch (get_class($claims)) {
            case OTT::class:
                // do OTT specific things here
                break;
            case PAT::class:
                // do PAT specific things here
                break;
            case APP::class:
                // do APP specific things here
                break;

            default:
                // log
        }

        return $claims->build($builder, $user, $this->app);
    }

    /**
     * @return \Zumo\ZumokitBundle\Model\ZumoApp
     */
    public function getApp(): ZumoApp
    {
        return $this->app;
    }
}
