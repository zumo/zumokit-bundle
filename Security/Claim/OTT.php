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

namespace Zumo\ZumokitBundle\Security\Claim;

use Zumo\ZumokitBundle\Model\UserInterface;
use Zumo\ZumokitBundle\Model\ZumoApp;
use Lcobucci\JWT;

/**
 * Class OTT
 *
 * @package      Zumo\ZumokitBundle\Security\Claim
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2019 DLabs (https://www.dlabs.si)
 */
class OTT extends BaseClaim
{
    /**
     * @inheritDoc
     */
    public function build(JWT\Builder $builder, UserInterface $user, ZumoApp $app): JWT\Builder
    {
        return $builder->set(self::ACI, (string) $user->getId())
            ->set(self::AID, $app->getId())
            ->set(self::AKY, $app->getApiKey());
    }

    /**
     * @inheritDoc
     */
    public function supportsClaims(): array
    {
        return [
            self::AKY,
            self::ACI,
            self::AKY,
        ];
    }
}
