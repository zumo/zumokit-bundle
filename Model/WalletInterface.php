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
 * WalletInterface represents a model of a wallet.
 *
 * @package      Zumo\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
interface WalletInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @param mixed $id
     * @return self
     */
    public function setId($id);

    /**
     * @return UserInterface
     */
    public function getUser(): ?UserInterface;

    /**
     * @param UserInterface|null $user
     * @return self
     */
    public function setUser(?UserInterface $user = null);

    /**
     * @return string|null
     */
    public function getAddress(): ?string;

    /**
     * @param string $address
     * @return self
     */
    public function setAddress(string $address);

    /**
     * @return string|null
     */
    public function getCoin(): ?string;

    /**
     * @param string $coin
     * @return self
     */
    public function setCoin(string $coin);

    /**
     * @return string|null
     */
    public function getSymbol(): ?string;

    /**
     * @param string $symbol
     * @return self
     */
    public function setSymbol(string $symbol);

    /**
     * @return string|null
     */
    public function getNetwork(): ?string;

    /**
     * @param string $network
     * @return self
     */
    public function setNetwork(string $network);

    /**
     * @return int|null
     */
    public function getChainId(): ?int;

    /**
     * @param int|null $chainId
     * @return self
     */
    public function setChainId(?int $chainId = null);

    /**
     * @return string|null
     */
    public function getPath(): ?string;

    /**
     * @param string $path
     * @return self
     */
    public function setPath(string $path);

    /**
     * @return int|null
     */
    public function getVersion(): ?int;

    /**
     * @param integer $version
     * @return self
     */
    public function setVersion(int $version);
}
