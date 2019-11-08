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

namespace Blockstar\ZumokitBundle\Service\Request\SAPI;

/**
 * Class AccountPushRequest
 *
 * This class represents the request that pushes to the ZumoKit API a user who
 * is logged in to the client app, but does not yet exists on the ZumoKit backend.
 * The query payload is passed via request headers:
 *
 *  - Authorization: Bearer <CLIENT_USER_TOKEN> - User's JWT token.
 *  - Api-Key: <KEY> - The app's API key.
 *  - App-Id: <ID> - The app's ID.
 *
 * ZumoKit API will verify the integrity of the Authorization token to ensure it
 * has not been changed since it was issued, and if valid, will trust the issuer.
 *
 * To trust the user, the ZumoKit API will perform a reverse-query to bundle's
 * /query endpoint to ensure the claims about the user in the token match the
 * state of the user in the realm of the app.
 *
 * @package Blockstar\ZumokitBundle\Service\Request
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class AccountPushRequest extends SapiRequest
{
    /**
     * @inheritDoc
     */
    public function getEndpointUri(): string
    {
        return 'sapi/accounts/push';
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function getRealm(): string
    {
        return 'https://k.it/sapi/accounts/push';
    }
}
