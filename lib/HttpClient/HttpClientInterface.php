<?php

namespace Zotapay\HttpClient;

/**
 * HttpClientInterface interface.
 */
interface HttpClientInterface
{
    /**
     * Pattern to be used in request method to set user agent.
     *
     * %1$s is for SDK version, %2$s is for implementation class and version.
     */
    public const USERAGENT = 'Zotapay PHP SDK %1$s (%2$s)';

    /**
     * @param string $method method used for the request
     * @param string $url full representation of the requested url
     * @param array $params the data passed to the request
     *
     * @throws \Zotapay\Exception\InvalidArgumentException
     *
     * @return array|false
     */
    public function request($method, $url, $params);
}
