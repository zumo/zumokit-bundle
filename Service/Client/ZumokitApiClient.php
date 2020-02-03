<?php

namespace Zumo\ZumokitBundle\Service\Client;

use Zumo\ZumokitBundle\Model\ZumoApp;
use Zumo\ZumokitBundle\Model\UserInterface;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

/**
 * Clinet for Zumokit API.
 */

class ZumokitApiClient
{
    const API_VERSION = '1.0.0';

    const PATH__HEALTHCHECK = '/api/v1/client-api/healthcheck';
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

    /**
     * @param ResponseInterface $response
     * @return stdClass|int
     */
    protected function processResponseData(ResponseInterface $response)
    {
        $status_code = $response->getStatusCode();
        $json_data = $response->getBody()->getContents();

        if ($status_code !== Response::HTTP_OK) {
            $this->logger->info(sprintf('Zumokit API client | %s', $json_data));
            return $status_code;
        }

        $data = json_decode($json_data, true);
        return $data;
    }

    public function integrationHealthcheck() {
        try {
            $headers = array_merge($this->headers, ['Accept' => 'application/health+json']);
            $response = $this->client->get(self::PATH__HEALTHCHECK, ['headers' => $headers, 'query' => []]);
        } catch (ClientException $e) {
            $this->logger->info(sprintf('Zumokit API client request failed | %s | %s', Psr7\str($e->getRequest()), Psr7\str($e->getResponse())));
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return $this->processResponseData($response);
    }

    /**
     * @param UserInterface $user
     */
    public function getToken(UserInterface $user) {
        try {
            $headers = array_merge($this->headers, ['account-id' => (string)$user->getId()]);
            $headers = array_merge($this->headers, ['api-key' => 'ffa48c3d8f19a2205b9f14a3b4c71a116594a56ac0d283a27facaf4fcb301930', 'account-id' => '2e7644c5-2971-493c-9c13-173ccead66f4']); // Temporary
            $response = $this->client->post(self::PATH__GET_TOKEN, ['headers' => $headers, 'query' => []]);
        } catch (ClientException $e) {
            $this->logger->info(sprintf('Zumokit API client request failed | %s | %s', Psr7\str($e->getRequest()), Psr7\str($e->getResponse())));
            return Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $status_code = $response->getStatusCode();
        if ($status_code !== Response::HTTP_OK) {
            $this->logger->info(sprintf('Zumokit API client | %s', $json_data));
            return $status_code;
        }

        return $response->getBody();
    }
}
