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

use Doctrine\Common\Collections\Collection;
use Zumo\ZumokitBundle\Model\WalletInterface;

/**
 * UserInterface represents a model of a user account.
 *
 * @package      Zumo\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
interface UserInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return Collection|WalletInterface[]
     */
    public function getWallets(): Collection;

    /**
     * @param WalletInterface $wallet
     * @return self
     */
    public function addWallet(WalletInterface $wallet);

    /**
     * Remove wallet
     *
     * @param WalletInterface $wallet
     * @return self
     */
    public function removeWallet(WalletInterface $wallet);
}
