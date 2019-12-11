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

use Zumo\ZumokitBundle\Service\Request\Parameters\Body;
use Zumo\ZumokitBundle\Service\Request\Parameters\Header;
use Zumo\ZumokitBundle\Service\Request\Parameters\Parameters;

/**
 * Class ZumoUser represents a model of a user account with
 * all properties required to authenticate to ZumoKit service.
 *
 * @package      Zumo\ZumokitBundle\Model
 * @author       Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 * @copyright    2018 DLabs (https://www.dlabs.si)
 */
class ZumoApp
{
    /**
     * @var static
     */
    public const ZUMOKIT_URL_DESCRIPTOR = 'https://*.kit.zumopay.com';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var array
     */
    private $domains;

    /**
     * @var string
     */
    private $primaryDomain;

    /**
     * @var array
     */
    private $machineMetadata;

    /**
     * @var string
     */
    private $userClass;

    /**
     * @var string
     */
    private $repositoryClass;

    /**
     * ZumoApp constructor.
     *
     * @param string $id
     * @param string $name
     * @param string $apiKey
     * @param string $apiUrl
     * @param array  $domains
     * @param string $primaryDomain
     * @param string $userClass
     * @param string $repositoryClass
     */
    public function __construct(
        string $id,
        string $name,
        string $apiKey,
        string $apiUrl,
        array $domains,
        string $primaryDomain,
        string $userClass,
        string $repositoryClass
    ) {
        $this->id              = $id;
        $this->name            = $name;
        $this->apiKey          = $apiKey;
        $this->apiUrl          = $apiUrl;
        $this->domains         = $domains;
        $this->primaryDomain   = $primaryDomain;
        $this->userClass       = $userClass;
        $this->repositoryClass = $repositoryClass;
    }

    /**
     * Get the request parameters for the given firewall realm.
     *
     * - Check authentication realm exists.
     *
     * - Extract the first two needle-delimited parts of the haystack, starting
     *   at last occurence of the needle.
     *
     * - Discard the substring before the first occurence of the needle in the
     *   haystack.
     *
     * @param string $realm
     *
     * @return array|null
     */
    public function getParameters(string $realm): ?array
    {
        $realmParsed = $realm;
        $parts       = array_slice(explode('/', parse_url($realmParsed, PHP_URL_PATH)), 2, 2, false);
        $path        = count($parts) !== 2 ? null : '/' . $parts[0] . '/' . $parts[1];
        return $this->getFirewall()[$path];
    }

    /**
     * Get all firewall realms.
     *
     * @return array
     */
    private function getFirewall(): array
    {
        return [
            '/accounts/check'         => [
                Parameters::PLC_HEADER => [
                    Parameters::OTT     => [
                        'name'  => Header::getName(Parameters::OTT),
                        'value' => 'OTT',
                    ],
                    Parameters::APP_ID  => [
                        'name'  => Header::getName(Parameters::APP_ID),
                        'value' => $this->getId(),
                    ],
                    Parameters::API_KEY => [
                        'name'  => Header::getName(Parameters::API_KEY),
                        'value' => $this->getApiKey(),
                    ],
                ],
                Parameters::PLC_BODY   => [],
            ],
            '/accounts/sync'          => [
                Parameters::PLC_HEADER => [
                    Parameters::OTT     => [
                        'name'  => Header::getName(Parameters::OTT),
                        'value' => 'OTT',
                    ],
                    Parameters::APP_ID  => [
                        'name'  => Header::getName(Parameters::APP_ID),
                        'value' => $this->getId(),
                    ],
                    Parameters::API_KEY => [
                        'name'  => Header::getName(Parameters::API_KEY),
                        'value' => $this->getApiKey(),
                    ],
                ],
                Parameters::PLC_BODY   => [],
            ],
            '/authentication/request' => [
                Parameters::PLC_HEADER => [
                    Parameters::OTT     => [
                        'name'  => Header::getName(Parameters::OTT),
                        'value' => '$this->getOtt()',
                    ],
                    Parameters::API_KEY => [
                        'name'  => Header::getName(Parameters::API_KEY),
                        'value' => $this->getApiKey(),
                    ],
                    Parameters::APP_ID  => [
                        'name'  => Header::getName(Parameters::APP_ID),
                        'value' => $this->getId(),
                    ],
                ],
                Parameters::PLC_BODY   => [
                    Body::getName(Parameters::OTT) => [
                        'value' => '$this->getOtt(),',
                    ],
                ],
            ],
            '/authentication/token'   => [
                Parameters::PLC_HEADER => [
                    Parameters::PAT     => [
                        'name'  => Header::getName(Parameters::PAT),
                        'value' => '',
                    ],
                    Parameters::API_KEY => [
                        'name'  => Header::getName(Parameters::API_KEY),
                        'value' => $this->getApiKey(),
                    ],
                ],
                Parameters::PLC_BODY   => [],
            ],
            '/sapi/vault/*'           => [
                Parameters::PLC_HEADER => [
                    Parameters::API_KEY => [
                        'name'  => Header::getName(Parameters::API_KEY),
                        'value' => $this->getApiKey(),
                    ],
                ],
                Parameters::PLC_BODY   => [],
            ],
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @return string
     */
    public function getUserClass(): string
    {
        return $this->userClass;
    }

    /**
     * @param string $userClass
     *
     * @return ZumoApp
     */
    public function setUserClass(?string $userClass = null): self
    {
        $this->userClass = $userClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getRepositoryClass(): string
    {
        return $this->repositoryClass;
    }

    /**
     * @param string $repositoryClass
     *
     * @return ZumoApp
     */
    public function setRepositoryClass(?string $repositoryClass = null): self
    {
        $this->repositoryClass = $repositoryClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDomains(): array
    {
        return $this->domains;
    }

    /**
     * @return string
     */
    public function getPrimaryDomain(): string
    {
        return $this->primaryDomain;
    }

    /**
     * @return array
     */
    public function getMachineMetadata(): array
    {
        return $this->machineMetadata;
    }

    /**
     * @return string|null
     */
    public function getApiUrl(): ?string
    {
        return $this->apiUrl;
    }
}
