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

use Lcobucci\JWT\Token;

/**
 * Class ZumoUser represents a model of a user account with
 * all properties required to authenticate to the RIP service.
 *
 * @package      Blockstar\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class ZumoUser extends User
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var bool
     */
    private $authenticated;

    /**
     * @var \Blockstar\ZumokitBundle\Model\AppCredentials
     */
    private $appCredentials;

    /**
     * @var \Blockstar\ZumokitBundle\Model\ClientCredentials
     */
    private $clientCredentials;

    /**
     * ZumoUser constructor.
     *
     * @param string|object            $user               Can be a UserInterface instance, an object
     *                                                     implementing a
     *                                                     __toString method, or the username as a regular string
     * @param string                   $appId
     * @param string                   $apiKey
     * @param \Lcobucci\JWT\Token|null $userToken
     */
    public function __construct($user, string $appId, string $apiKey, ?Token $userToken = null)
    {
        if (!($user instanceof UserInterface)) {
            $this->username = is_object($user) && method_exists($user, '__toString') ? $user->__toString() : $user;
            return;
        }

        $this->username          = $user->getUsername();
        $this->email             = $user->getUsername();
        $this->appCredentials    = new AppCredentials($appId, $apiKey);
        $this->clientCredentials = new ClientCredentials($userToken);
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return ZumoUser
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \Lcobucci\JWT\Token
     */
    public function getClientTokenString(): ?string
    {
        return $this->clientCredentials->getTokenString();
    }

    /**
     * @param \Lcobucci\JWT\Token $clientToken
     *
     * @return ZumoUser
     */
    public function setClientToken(Token $clientToken): self
    {
        $this->clientCredentials = new ClientCredentials($clientToken);

        return $this;
    }

    /**
     * @return \Blockstar\ZumokitBundle\Model\ClientCredentials
     */
    public function getClientCredentials(): ClientCredentials
    {
        return $this->clientCredentials;
    }

    /**
     * @return \Blockstar\ZumokitBundle\Model\AppCredentials
     */
    public function getAppCredentials(): AppCredentials
    {
        return $this->appCredentials;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return ZumoUser
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    /**
     * @param bool $authenticated
     *
     * @return ZumoUser
     */
    public function setAuthenticated($authenticated = false): self
    {
        $this->authenticated = $authenticated;
        return $this;
    }

    /**
     * @return array
     */
    public function serialize(): array
    {
        return [
            'id'            => $this->id,
            'username'      => $this->username,
            'email'         => $this->email,
            'authenticated' => $this->authenticated,
            'app_id'        => $this->getAppId(),
            'api_key'       => $this->getApiKey(),
            'client_token'  => (string) $this->getClientToken(),
        ];
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appCredentials->getAppId();
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->appCredentials->getApiKey();
    }

    /**
     * @return \Lcobucci\JWT\Token
     */
    public function getClientToken(): ?Token
    {
        return $this->clientCredentials->getToken();
    }

    /**
     * @return string
     */
    public function getIdentityFieldName(): string
    {
        // TODO: Implement getIdentityFieldName() method.
    }

    /**
     * @inheritDoc
     */
    public function getIdentity()
    {
        // TODO: Implement getIdentity() method.
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): ?string
    {
        // TODO: Implement getDisplayName() method.
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        // TODO: Implement __toString() method.
    }
}
