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

namespace Zumo\ZumokitBundle\Security\Token\Builder;

use Zumo\ZumokitBundle\Security\Token\JWTEncoder;
use Zumo\ZumokitBundle\Security\Claim\BaseClaim;
use Lcobucci\JWT;

/**
 * Class TokenBuilder
 *
 * @package Zumo\ZumokitBundle\Security\Token\Builder
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class TokenBuilder
{
    /**
     * @var JWTEncoder
     */
    private $encoder;

    /**
     * @var ClaimBuilder
     */
    private $claimBuilder;

    /**
     * TokenBuilder constructor.
     *
     * @param JWTEncoder     $encoder
     * @param ClaimBuilder                                      $builder
     */
    public function __construct(JWTEncoder $encoder, ClaimBuilder $builder) {
        $this->encoder      = $encoder;
        $this->claimBuilder = $builder;
    }

    /**
     * @param                                                         $user
     *
     * @param \Zumo\ZumokitBundle\Security\Claim\BaseClaim       $claims
     *
     * @return \Lcobucci\JWT\Token
     * @throws \Exception
     */
    public function build($user, BaseClaim $claims): JWT\Token
    {
        return $this->encoder->encode($user, $this->claimBuilder->build($claims));
    }
}
