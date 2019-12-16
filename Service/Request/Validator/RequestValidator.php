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

namespace Zumo\ZumokitBundle\Service\Request\Validator;

use Zumo\ZumokitBundle\Exception\AuthenticationRequestException;
use Zumo\ZumokitBundle\Exception\TokenException;
use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Service\Token\Extractor\BearerExtractor;
use Zumo\ZumokitBundle\Service\Token\JWTDecoder;
use Lcobucci\JWT\Token;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestValidator
 *
 * @package      Zumo\ZumokitBundle\Service\Token\Validator
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
class RequestValidator
{
    /**
     * @var \Zumo\ZumokitBundle\Service\Token\JWTDecoder
     */
    private $decoder;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var \Zumo\ZumokitBundle\Model\ZumoApp
     */
    private $app;

    /**
     * RequestValidator constructor.
     *
     * @param string                                 $publicKey
     * @param \Zumo\ZumokitBundle\Model\ZumoApp $app
     */
    public function __construct(string $publicKey, ZumoApp $app)
    {
        $this->decoder   = new JWTDecoder();
        $this->publicKey = $publicKey;
        $this->app       = $app;
    }

    /**
     * Validates the request by verifying an authorization token is present as
     * the Authorization request header. If a token is successfully extracted
     * from the request, an attempt is made to decode it using the application's
     * public key.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Lcobucci\JWT\Token
     * @throws \Zumo\ZumokitBundle\Exception\TokenException
     * @throws \Zumo\ZumokitBundle\Exception\AuthenticationRequestException
     */
    public function validate(Request $request): Token
    {
        if (!$request->headers->get('authorization')) {
            throw new AuthenticationRequestException('Missing authentication token.');
        }

        if (($tokenString = BearerExtractor::extract($request)) === null) {
            throw new TokenException('Token is empty.');
        }

        return $this->decoder->decode($tokenString, $this->publicKey);
    }
}
