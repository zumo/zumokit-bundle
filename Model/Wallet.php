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
 * Wallet represents a model of a wallet.
 *
 * @package      Blockstar\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class Wallet implements WalletInterface
{
    /**
     * Identity of the Wallet as recorded in the client app.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Identity of the Wallet as recorded in the ZumoKit service.
     *
     * @var string
     */
    protected $serviceId;

    /**
     * The blockchain address of this wallet.
     *
     * @var string The blockchain address.
     */
    protected $address;

    /**
     * Time of last sync with remote service.
     *
     * @var \DateTimeImmutable Last sync time
     */
    protected $lastSyncAt;

    /**
     * @var \Blockstar\ZumokitBundle\Model\UserInterface
     */
    protected $user;

    /**
     * Wallet constructor.
     *
     * @param string                                       $address
     * @param \Blockstar\ZumokitBundle\Model\UserInterface $user
     */
    public function __construct(string $address, UserInterface $user)
    {
        $this->address = $address;
        $this->user    = $user;
    }

    /**
     * @inheritDoc
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param \Blockstar\ZumokitBundle\Model\UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getServiceId()
    {
        return $this->serviceId;
    }

    /**
     * @param string $serviceId
     *
     * @return $this
     */
    public function setServiceId(?string $serviceId = null)
    {
        $this->serviceId = $serviceId;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastSyncAt(): ?\DateTimeImmutable
    {
        return $this->lastSyncAt;
    }

    /**
     * @inheritDoc
     */
    public function setLastSyncAt(\DateTimeImmutable $time): WalletInterface
    {
        $this->lastSyncAt = $time;
        return $this;
    }

    /**
     * Returns an array representation of Wallet's identity and address.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'     => (string) $this->getId(),
            'wallet' => $this->getAddress(),
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     *
     * @return $this
     */
    public function setAddress(?string $address = null)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Returns the address.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getAddress();
    }
}
