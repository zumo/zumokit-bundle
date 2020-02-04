<?php

namespace Zumo\ZumokitBundle\Service\Client;

use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Model\UserInterface;

use Exception;
use Symfony\Component\HttpFoundation\Response as SymResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Clinet for Zumokit API.
 */

class ZumokitApiClient
{
    const API_VERSION = '1.0.0';
    const LOG_TAG = 'Zumokit API client';

    const PATH__HEALTHCHECK = '/api/v1/client-api/healthcheck';
    const PATH__CHECK_ACCOUNT = '/sapi/accounts/check';
    const PATH__GET_TOKEN = '/sapi/authentication/token';

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ZumoApp $zumoApp
     * @var LoggerInterface $logger
     */
    protected $zumoApp, $logger;

    /**
     * @var array
     */
    protected $headers;

    /**
     * @param ZumoApp $zumoApp
     * @param LoggerInterface $logger
     */
    public function __construct(ZumoApp $zumoApp, LoggerInterface $logger)
    {
        $this->zumoApp = $zumoApp;
        $this->logger = $logger;

        $zumokitApiBaseUrl = $this->zumoApp->getApiUrl();

        // Fix base URL if it lachs protocol definition.
        // This is temporary approach due to the legacy support and will be removed in the future.
        preg_match('/^https:\/\//', $zumokitApiBaseUrl, $matches);
        if (empty($matches)) {
            $zumokitApiBaseUrl = "https://" . $zumokitApiBaseUrl;
        }

        if(!preg_match('/https\:\/\//i', $zumokitApiBaseUrl)) {
            throw new \Exception('Zumo API base URL does NOT contain https://');
        }

        $config = [
            'base_uri' => $zumokitApiBaseUrl,
            'allow_redirects' => false,
            'http_errors' => false
        ];

        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept-Version' => self::API_VERSION,
            'app-id' => $this->zumoApp->getId(),
            'api-key' => $this->zumoApp->getApiKey()
        ];

        $this->client = new Client($config);
    }

    protected function get(string $path, array $headers, array $parameters = []): Response
    {
        $merged_headers = array_merge($this->headers, $headers);
        $response = $this->client->get($path, ['headers' => $merged_headers, 'query' => $parameters]);

        return $this->preprocessResponse($response);
    }

    protected function post(string $path, array $headers, array $data = []): Response
    {
        $merged_headers = array_merge($this->headers, $headers);
        $response = $this->client->post($path, ['headers' => $merged_headers, RequestOptions::JSON => $data]);

        return $this->preprocessResponse($response);
    }

    /**
     * @param Response $response
     * @return mixed
     */
    protected function preprocessResponse(Response $response): Response
    {
        $content_types = $response->getHeader('content-type');

        // Check if the content type is acceptable.
        if (empty(array_intersect($content_types, ['application/json', 'application/healthcheck+json']))) {
            $body = $response->getBody();
            $this->throwException($response);
        }

        return $response;
    }

    protected function throwException(Response $response)
    {
        $message = sprintf('%s | Request failed with code %s. %s.', self::LOG_TAG, $response->getStatusCode(), $response->getBody());
        $this->logger->critical($message);

        throw new Exception($message);
    }

    /**
     * Integration health check
     *
     * @return string JSON data
     */
    public function integrationHealthcheck(): string
    {
        $response = $this->get(self::PATH__HEALTHCHECK, ['Accept' => 'application/health+json']);

        return $response->getBody();
    }

    /**
     * Check if user account exists.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function checkIfUserAccountExists(UserInterface $user): bool
    {
        $response = $this->get(self::PATH__CHECK_ACCOUNT, ['account-id' => (string)$user->getId()]);

        $status_code = $response->getStatusCode();
        if ($status_code === SymResponse::HTTP_OK) {
            return true;
        } else if ($status_code === SymResponse::HTTP_FOUND) {
            return true;
        } else if ($status_code === SymResponse::HTTP_NOT_FOUND) {
            return false;
        }

        $this->throwException($response);
    }

    /**
     * Get tokens
     *
     * @param UserInterface $user
     * @return string JSON data
     */
    public function getTokens(UserInterface $user): string
    {
        $response = $this->post(self::PATH__GET_TOKEN, ['account-id' => (string)$user->getId()]);
        $status_code = $response->getStatusCode();
        if ($status_code !== SymResponse::HTTP_OK) {
            $this->throwException($response);
        }

        return $response->getBody();
    }
}
