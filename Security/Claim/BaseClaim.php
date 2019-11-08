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

namespace Blockstar\ZumokitBundle\Security\Claim;

use Blockstar\ZumokitBundle\Model\UserInterface;
use Blockstar\ZumokitBundle\Model\ZumoApp;
use Lcobucci\JWT;

/**
 * Class BaseClaim
 *
 * @package      Blockstar\ZumokitBundle\Security\Claim
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
abstract class BaseClaim
{
    /**
     * All Claims
     */
    public const EXP = 'exp'; // Token expiration ts
    public const TTL = 'ttl'; // Token TTL since time of issue
    public const AID = 'aid'; // App ID
    public const ACI = 'aci'; // Account ID
    public const AKY = 'aky'; // API Key
    public const LVL = 'lvl'; // Level

    /**
     * @param \Lcobucci\JWT\Builder                        $builder
     * @param \Blockstar\ZumokitBundle\Model\UserInterface $user
     * @param \Blockstar\ZumokitBundle\Model\ZumoApp       $app
     *
     * @return JWT\Builder
     */
    abstract public function build(JWT\Builder $builder, UserInterface $user, ZumoApp $app): JWT\Builder;

    /**
     * @return array
     */
    abstract public function supportsClaims(): array;
}
