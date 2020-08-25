<?php

namespace nl\rabobank\gict\payments_savings\omnikassa_sdk\connector\http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Guzzle implementation of the RESTTemplate.
 */
class GuzzleRESTTemplate implements RESTTemplate
{
    /** @var Client */
    private $client;
    /** @var string */
    private $token;

    /**
     * GuzzleRESTTemplate constructor.
     *
     * @param string $baseUrl
     */
    public function __construct($baseUrl)
    {
        $this->client = new Client([
            'base_uri' => $this->parse($baseUrl),
        ]);
    }

    /**
     * @param string $baseUrl
     *
     * @return string
     */
    private function parse($baseUrl)
    {
        if ('/' !== substr($baseUrl, -1)) {
            return $baseUrl.'/';
        }

        return $baseUrl;
    }

    /**
     * Set the token to be used for the upcoming requests.
     *
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Perform a GET call to the given path.
     *
     * @param string $path
     * @param array  $parameters
     *
     * @return string Response body
     */
    public function get($path, array $parameters = [])
    {
        try {
            $response = $this->client->get($path, [
                'headers' => $this->makeRequestHeaders(),
                'query' => $parameters,
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $message = sprintf('%s [body] %s', $e->getMessage(), $response);
            throw new ClientException($message, $e->getRequest(), $e->getResponse());
        }

        return $response->getBody()->getContents();
    }

    /**
     * Perform a POST call to the given path.
     *
     * @param string            $path
     * @param \JsonSerializable $body
     *
     * @return string Response body
     */
    public function post($path, \JsonSerializable $body = null)
    {
        try {
            $response = $this->client->post($path, [
                'headers' => $this->makeRequestHeaders(),
                'json' => $body,
            ]);
        } catch (ClientException $e) {
            $response = $e->getResponse()->getBody()->getContents();
            $message = sprintf('%s [body] %s', $e->getMessage(), $response);
            throw new ClientException($message, $e->getRequest(), $e->getResponse());
        }

        return $response->getBody()->getContents();
    }

    /**
     * @return array
     */
    private function makeRequestHeaders()
    {
        return ['Authorization' => 'Bearer '.$this->token];
    }
}
