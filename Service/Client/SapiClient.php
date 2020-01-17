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

namespace Zumo\ZumokitBundle\Service\Client;

use Zumo\ZumokitBundle\Exception\SapiResponseException;
use Zumo\ZumokitBundle\Service\Request\SAPI\AccountCheckRequest;
use Zumo\ZumokitBundle\Service\Request\SAPI\AccountPushRequest;
use Zumo\ZumokitBundle\Service\Request\SAPI\AccessTokenRequest;
use Zumo\ZumokitBundle\Service\Request\SAPI\SapiRequest;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Psr7\str;
use function GuzzleHttp\Psr7\stream_for;

/**
 * Class SapiClient dispatches requests to the ZumoKit SAPI endpoints, using
 *
 * @package Zumo\ZumokitBundle\Service\Client
 * @author  Vladimir Strackovski <vladimir.strackovski@dlabs.si>
 */
class SapiClient
{
    /**
     * @var string
     */
    private $version = '1.0';

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var \Zumo\ZumokitBundle\Model\ClientCredentials
     */
    private $clientCredentials;

    /**
     * @var \Zumo\ZumokitBundle\Model\AppCredentials
     */
    private $appCredentials;

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * SapiClient constructor.
     *
     * @param string                                           $baseUri
     * @param \Zumo\ZumokitBundle\Model\ClientCredentials $clientCredentials
     * @param \Zumo\ZumokitBundle\Model\AppCredentials    $appCredentials
     * @param \Psr\Log\LoggerInterface                         $logger
     */
    public function __construct(
        string $baseUri,
        \Zumo\ZumokitBundle\Model\ClientCredentials $clientCredentials,
        \Zumo\ZumokitBundle\Model\AppCredentials $appCredentials,
        \Psr\Log\LoggerInterface $logger
    ) {
        // Require https:// URL prefix
        preg_match('/^https:\/\//', $baseUri, $matches);
        if (empty($matches)) {
            $baseUri = "https://" . $baseUri;
        }

        $this->baseUri = $baseUri;

        $this->clientCredentials = $clientCredentials;
        $this->appCredentials = $appCredentials;
        $this->logger = $logger;
        $this->httpClient = new \GuzzleHttp\Client(
            [
                'base_uri' => $this->baseUri,
                'verify'   => false,
            ]
        );
    }

    /**
     * Invokes a call to SAPI's access token endpoint.
     *
     * @param null $accountId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws SapiResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAccessToken($accountId = null): ResponseInterface
    {
        return $this->sendRequest(new AccessTokenRequest($this->baseUri, $this->appCredentials->getApiKey()), $accountId);
    }

    /**
     * Invokes a call to SAPI's checkAccount endpoint.
     *
     * @param null $accountId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws SapiResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkAccount($accountId = null): ResponseInterface
    {
        return $this->sendRequest(new AccountCheckRequest($this->baseUri, $this->appCredentials->getApiKey()), $accountId);
    }

    /**
     * Invokes a call to SAPI's pushAccount endpoint.
     *
     * @param null $accountId
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws SapiResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function pushAccount($accountId = null): ResponseInterface
    {
        return $this->sendRequest(new AccountPushRequest($this->baseUri, $this->appCredentials->getApiKey(), $accountId));
    }

    /**
     * Sends the request using the http client.
     *
     * @param \Zumo\ZumokitBundle\Service\Request\SAPI\SapiRequest
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws SapiResponseException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendRequest(SapiRequest $request): ResponseInterface
    {
        try {
            $this->logger->critical(sprintf('Calling %s', $request->getUri()));
            $response = $this->httpClient->send($request);
            $this->decodeResponse($response);
        } catch (RequestException $exception) {
            $this->logger->critical(sprintf('%s', $exception->getMessage()));
            $guzzleErr = $this->parseGuzzleException($exception);
            throw new SapiResponseException(sprintf("%s %s", $guzzleErr['error'], $guzzleErr['dev_message']));
        } catch (SapiResponseException $exception) {
            // The SAPI exception's message can be used directly.
            $this->logger->critical(sprintf('%s', $exception->getMessage()));
            throw $exception;
        }

        $this->logger->critical(
            sprintf(
                'Request to SAPI completed without errors, %s, %s',
                $response->getBody()->getContents(),
                $response->getStatusCode()
            )
        );

        return $response;
    }

    /**
     * Decodes the body of the specified response. Supports JSON only,
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @throws Exception
     * @return array
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        $responseDecoded = json_decode(
            $response->getBody()->getContents(),
            1
        );
        $errorFmt = 'JSON decode error: %s.';

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $responseDecoded;
            case JSON_ERROR_DEPTH:
                throw new SapiResponseException(sprintf($errorFmt, 'Maximum stack depth exceeded.'));
                break;
            case JSON_ERROR_STATE_MISMATCH:
                throw new SapiResponseException(sprintf($errorFmt, 'Underflow or the modes mismatch.'));
                break;
            case JSON_ERROR_CTRL_CHAR:
                throw new SapiResponseException(sprintf($errorFmt, 'Unexpected control character found.'));
                break;
            case JSON_ERROR_SYNTAX:
                throw new SapiResponseException(sprintf($errorFmt, 'Syntax error, malformed JSON.'));
                break;
            case JSON_ERROR_UTF8:
                throw new SapiResponseException(sprintf($errorFmt, 'Malformed UTF-8 characters.'));
                break;
            default:
                throw new SapiResponseException(sprintf($errorFmt, 'Unknown error.'));
                break;
        }
    }

    /**
     * Creates an associative array and populates it with exception data, with
     * values specific to the type of the specified RequestException. Used for
     * logging and response,
     *
     * @param \GuzzleHttp\Exception\RequestException $exception
     *
     * @return array
     */
    private function parseGuzzleException(RequestException $exception): array
    {
        $code = $exception->hasResponse() ? str($exception->getResponse()) : false;
        $response = $exception->hasResponse() ? str($exception->getResponse()) : false;

        switch (get_class($exception)) {
            case ConnectException::class:
                return [
                    'error'       => 'Origin is unreachable: a network connection error occurred.',
                    'code'        => $code ?? 523,
                    'response'    => $response ?? null,
                    'request'     => str($exception->getRequest()),
                    'dev_message' => $exception->getMessage(),
                ];
                break;

            case ServerException::class:
                return [
                    'error'       => 'The server encountered an error and is unable to process the request.',
                    'code'        => $code ?? 500,
                    'response'    => $response ?? null,
                    'request'     => str($exception->getRequest()),
                    'dev_message' => $exception->getMessage(),
                ];
                break;

            case ClientException::class:
                return [
                    'error'       => 'Bad request.',
                    'code'        => $code ?? 400,
                    'response'    => $response ?? null,
                    'request'     => str($exception->getRequest()),
                    'dev_message' => $exception->getMessage(),
                ];
                break;

            case TooManyRedirectsException::class:
                return [
                    'error'       => 'Too many redirects.',
                    'code'        => $code ?? 400,
                    'request'     => str($exception->getRequest()),
                    'response'    => $response ?? null,
                    'dev_message' => $exception->getMessage(),
                ];
                break;

            default:
                return [
                    'error'       => 'An unknown error has occurred.',
                    'code'        => $code ?? 520,
                    'request'     => '',
                    'response'    => $response ?? null,
                    'dev_message' => $exception->getMessage(),
                ];
                break;
        }
    }

    /**
     * Invokes a call to SAPI's checkAccount endpoint.
     *
     * @param array $users
     * @param string $accId
     *
     * @return \GuzzleHttp\Psr7\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    //public function sync($users, $accId): \GuzzleHttp\Psr7\Response
    public function sync($users, $accId, $ua = null): \GuzzleHttp\Psr7\Response
    {
        return $this->httpClient->request('POST', $this->baseUri . '/sapi/vault/sync', ['body' => stream_for(json_encode($users)), 'headers' => [
            'api-key' => $this->appCredentials->getApiKey(),
            'acc-id' => $accId
        ]]);
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @return \Zumo\ZumokitBundle\Model\ClientCredentials
     */
    public function getClientCredentials(): \Zumo\ZumokitBundle\Model\ClientCredentials
    {
        return $this->clientCredentials;
    }

    /**
     * @return \Zumo\ZumokitBundle\Model\AppCredentials
     */
    public function getAppCredentials(): \Zumo\ZumokitBundle\Model\AppCredentials
    {
        return $this->appCredentials;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger(): \Psr\Log\LoggerInterface
    {
        return $this->logger;
    }
}
