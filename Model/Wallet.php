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
 * Wallet represents a model of a wallet.
 *
 * @package      Zumo\ZumokitBundle\Model
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
     * The blockchain address.
     *
     * @var string The blockchain address.
     */
    protected $address;

    /**
     * The name of crypto coin.
     *
     * @var string The blockchain address.
     */
    protected $coin;

    /**
     * The symbol of crypto coin.
     *
     * @var string The blockchain address.
     */
    protected $symbol;

    /**
     * The network name of crypto coin.
     *
     * @var string The blockchain address.
     */
    protected $network;

    /**
     * The path of crypto coin.
     *
     * @var string The blockchain address.
     */
    protected $path;

    /**
     * The version of crypto coin parameter values (used for synchronization with Zumokit API).
     *
     * @var string The blockchain address.
     */
    protected $version;

    /**
     * @var \Zumo\ZumokitBundle\Model\UserInterface
     */
    protected $user;

    /**
     * Wallet constructor.
     *
     * @param string                                       $address
     * @param \Zumo\ZumokitBundle\Model\UserInterface $user
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
     * @param \Zumo\ZumokitBundle\Model\UserInterface $user
     *
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;
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
     * @return string|null
     */
    public function getCoin()
    {
        return $this->coin;
    }

    /**
     * @param string $coin
     *
     * @return $this
     */
    public function setCoin(?string $coin = null)
    {
        $this->coin = $coin;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param string $symbol
     *
     * @return $this
     */
    public function setSymbol(?string $symbol = null)
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(?string $path = null)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @param string $network
     *
     * @return $this
     */
    public function setNetwork(?string $network = null)
    {
        $this->network = $network;
        return $network;
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return $this
     */
    public function setVersion(?string $version = null)
    {
        $this->version = $version;
        return $version;
    }
}
