<?php

namespace Zotapay;

/**
 * Abstarct Class AbstractApiClient.
 */
abstract class AbstractApiClient implements ApiClientInterface
{
    /**
     * ApiRequest instance
     *
     * @var \Zotapay\ApiRequest
     */
    protected $apiRequest;


    /**
     * @param \Zotapay\Data $data
     */
    public function __construct($data = null)
    {
        // Get the ApiRequest object
        $this->apiRequest = new \Zotapay\ApiRequest();
    }


    /**
     * Zotapay API request.
     *
     * @codeCoverageIgnore
     *
     * @param \Zotapay\Data $data
     *
     * @return self
     */
    public function request($data)
    {
    }


    /**
     * Prepare data for the request.
     *
     * @codeCoverageIgnore
     *
     * @param \Zotapay\ZotapayOrder $order
     *
     * @return array
     */
    private function prepare($order)
    {
    }


    /**
     * Generate signature and add it to the data.
     *
     * @param array $data
     *
     * @return array
     *
     * @codeCoverageIgnore
     *
     */
    private function sign($data)
    {
    }


    /**
     * Set ApiResponse onject
     *
     * @return self
     */
    public function setApiRequest($apiRequest)
    {
        $this->apiRequest = $apiRequest;
        return $this;
    }
}
