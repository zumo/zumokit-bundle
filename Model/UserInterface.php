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
 * UserInterface represents a model of a user account.
 *
 * @package      Blockstar\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
interface UserInterface
{
    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return bool
     */
    public function hasWallet(): bool;

    /**
     * @return mixed
     */
    public function getWallet(): ?WalletInterface;

    /**
     * @return string|int|null
     */
    public function getIdentity();

    /**
     * @return string|null
     */
    public function getDisplayName(): ?string;

    /**
     * Returns an array representation of User's identity, display name and
     * wallet address fields.
     *
     * @return array
     */
    public function toArray();

    /**
     * Returns base64 encoded concatenation of identity and display name.
     *
     * @return string
     */
    public function __toString();
}
