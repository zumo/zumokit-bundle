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
 * ZumoUserTrait represents a model of a user account.
 *
 * @package      Zumo\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
trait UserTrait
{
    /**
     * @return bool
     */
    public function hasWallet(): bool
    {
        return $this->wallet !== null;
    }

    /**
     * @inheritDoc
     */
    public function getIdentity()
    {
        return (string) $this->getId();
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
     * @return \Zumo\ZumokitBundle\Model\WalletInterface|null
     */
    public function getWallet(): ?WalletInterface
    {
        return $this->wallet;
    }

    /**
     * @param \Zumo\ZumokitBundle\Model\WalletInterface $wallet
     *
     * @return \Zumo\ZumokitBundle\Model\User|\Zumo\ZumokitBundle\Model\UserInterface|\Zumo\ZumokitBundle\Model\UserTrait
     */
    public function setWallet(WalletInterface $wallet)
    {
        $this->wallet = $wallet;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return \Zumo\ZumokitBundle\Model\User|\Zumo\ZumokitBundle\Model\UserTrait
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
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
