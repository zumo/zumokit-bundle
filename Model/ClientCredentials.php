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

namespace Blockstar\ZumokitBundle\Model;

/**
 *
 * @package      Blockstar\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class ClientCredentials
{
    /**
     * @var \Lcobucci\JWT\Token|null
     */
    private $token;

    /**
     * ClientCredentials constructor.
     *
     * @param \Lcobucci\JWT\Token $token
     */
    public function __construct(?\Lcobucci\JWT\Token $token = null)
    {
        $this->token = $token;
    }

    /**
     * @return \Lcobucci\JWT\Token|null
     */
    public function getToken(): ?\Lcobucci\JWT\Token
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getTokenString(): ?string
    {
        return (string) $this->token;
    }
}
