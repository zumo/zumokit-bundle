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
     *
     * @return WalletInterface
     */
    public function setId($id);

    /**
     * @return \Zumo\ZumokitBundle\Model\UserInterface|null
     */
    public function getUser(): ?UserInterface;

    /**
     * @return string|null
     */
    public function getAddress();

    /**
     * @param string $address
     *
     * @return \Zumo\ZumokitBundle\Model\WalletInterface
     */
    public function setAddress(?string $address = null);
}
