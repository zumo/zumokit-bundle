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

namespace Zumo\ZumokitBundle\Model;

/**
 * User represents a model of a user account.
 *
 * @package      Zumo\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
abstract class User implements UserInterface
{
    /**
     * @var \Zumo\ZumokitBundle\Model\WalletInterface
     */
    protected $wallet;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $id;

    /**
     * @return bool
     */
    public function hasWallet(): bool
    {
        return $this->wallet !== null;
    }

    /**
     * Returns an array representation of User's identity, display name and
     * wallet address fields.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'id'     => (string) $this->getId(),
            'wallet' => $this->getWallet(),
            'email'  => $this->getEmail(),
        ];
    }

    /**
     * @return string|int|null
     */
    public function getIdentity()
    {
        return (string) $this->getId();
    }

    /**
     * @return \Zumo\ZumokitBundle\Model\WalletInterface|null
     */
    public function getWallet(): ?WalletInterface
    {
        return $this->wallet;
    }

    /**
     * @param \Zumo\ZumokitBundle\Model\WalletInterface $wallet
     *
     * @return User
     */
    public function setWallet(WalletInterface $wallet): UserInterface
    {
        $this->wallet = $wallet;
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
     * @param string $email
     *
     * @return User
     */
    public function setEmail(?string $email = null): UserInterface
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns base64 encoded concatenation of identity.
     *
     * @return string
     */
    public function __toString()
    {
        return base64_encode($this->getId());
    }
}
