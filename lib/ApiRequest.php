<?php

namespace Zotapay;

/**
 * Class ApiRequest.
 */
class ApiRequest
{
    /**
     * HTTP client used for the requests
     *
     * @var \Zotapay\HttpClient\HttpClientInterface
     */
    protected $httpClient;

    /**
     * Set the $httpClient
     *
     * @param null|\Zotapay\HttpClient\HttpClientInterface
     */
    public function __construct($httpClient = null)
    {
        $this->setHttpClient($httpClient);
    }


    /**
     * Get the value of $httpClient
     *
     * @return \Zotapay\HttpClient\HttpClientInterface
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }


    /**
     * Set the value of $httpClient
     *
     * @param null|\Zotapay\HttpClient\HttpClientInterface
     *
     * @return self
     */
    public function setHttpClient($httpClient = null)
    {
        $this->httpClient = $httpClient;

        if ($this->httpClient === null) {
            $this->httpClient = new \Zotapay\HttpClient\CurlClient();
        }

        return $this;
    }

    /**
     * Make a request to the Zotapay API
     *
     * @param string $method method used for the request
     * @param string $url full representation of the requested url
     * @param array $params the data passed to the request
     *
     * @return array|false
     */
    public function request($method, $url, $params)
    {
        $httpClient = $this->getHttpClient();
        return $httpClient->request($method, $url, $params);
    }
}
