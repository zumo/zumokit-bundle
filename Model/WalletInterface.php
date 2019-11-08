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
 * WalletInterface represents a model of a wallet.
 *
 * @package      Blockstar\ZumokitBundle\Model
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
     *
     * @return WalletInterface
     */
    public function setId($id);

    /**
     * @return \Blockstar\ZumokitBundle\Model\UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @return string
     */
    public function getServiceId();

    /**
     * @param string $serviceId
     *
     * @return \Blockstar\ZumokitBundle\Model\WalletInterface
     */
    public function setServiceId(?string $serviceId = null);

    /**
     * @return string|null
     */
    public function getAddress();

    /**
     * @param string $address
     *
     * @return \Blockstar\ZumokitBundle\Model\WalletInterface
     */
    public function setAddress(?string $address = null);

    /**
     * @return \DateTimeImmutable|null
     */
    public function getLastSyncAt(): ?\DateTimeImmutable;

    /**
     * @param \DateTimeImmutable $time
     *
     * @return \Blockstar\ZumokitBundle\Model\WalletInterface
     */
    public function setLastSyncAt(\DateTimeImmutable $time);

    /**
     * @return mixed
     */
    public function __toString();

    /**
     * @return array
     */
    public function toArray(): array;
}
