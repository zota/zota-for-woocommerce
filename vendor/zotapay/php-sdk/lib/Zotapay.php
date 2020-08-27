<?php

namespace Zotapay;

/**
 * Class Zotapay.
 */
class Zotapay
{
    /**
     * SDK Version
     */
    public const VERSION = 'v1.0.20';

    /**
     * A merchant unique identifier, used for identification
     * @var string
     */
    public static $merchantId;

    /**
     * A secret key, used for authentication
     * @var string
     */
    public static $merchantSecretKey;

    /**
     * Zotapay API endpoint
     * @var string
     */
    public static $endpoint;

    /**
     * The base URL for the Zotapay API
     * @var string
     */
    public static $apiBase = 'https://api.zotapay.com';

    /**
     * The version of the Zotapay API to use for requests
     * @var string
     */
    public static $apiVersion = 'v1';

    /**
     * The URL for the Zotapay API
     * @var string
     */
    public static $apiUrl = null;

    /**
     * The logger used to produce messages
     * @var null|\Zotapay\Log\LoggerInterface
     */
    public static $logger = null;

    /**
     * The log threshold, options: emergency|alert|critical|error|warning|notice|info|debug.
     * @var string
     */
    public static $logThreshold = 'error';

    /**
     * The log destination. Path to log file
     * @var string
     */
    public static $logDestination;

    /**
     * Data, used for mocking API requests.
     * @var array
     */
    public static $mockResponse;

    /**
     * Get the value of merchantId
     *
     * @return string
     */
    public static function getMerchantId()
    {
        return self::$merchantId;
    }

    /**
     * Set the value of merchantId
     *
     * @param string $merchantId
     */
    public static function setMerchantId($merchantId)
    {
        self::$merchantId = $merchantId;
    }


    /**
     * Get the value of merchantSecretKey
     *
     * @return string
     */
    public static function getMerchantSecretKey()
    {
        return self::$merchantSecretKey;
    }


    /**
     * Set the value of merchantSecretKey
     *
     * @param string $merchantSecretKey
     */
    public static function setMerchantSecretKey($merchantSecretKey)
    {
        self::$merchantSecretKey = $merchantSecretKey;
    }


    /**
     * Get the endpoint
     *
     * @return string
     */
    public static function getEndpoint()
    {
        return self::$endpoint;
    }


    /**
     * Set the value of endpoint
     *
     * @param string $endpoint
     */
    public static function setEndpoint($endpoint)
    {
        self::$endpoint = $endpoint;
    }


    /**
     * Get the value of The base URL for the Zotapay API
     *
     * @return string
     */
    public static function getApiBase()
    {
        return self::$apiBase;
    }


    /**
     * Set the value of The base URL for the Zotapay API
     *
     * @param string $apiBase
     */
    public static function setApiBase($apiBase)
    {
        self::$apiBase = $apiBase;
    }


    /**
     * Get the value of The version of the Zotapay API to use for requests
     *
     * @return string
     */
    public static function getApiVersion()
    {
        return self::$apiVersion;
    }


    /**
     * Set the value of The version of the Zotapay API to use for requests
     *
     * @param string $apiVersion
     */
    public static function setApiVersion($apiVersion)
    {
        self::$apiVersion = $apiVersion;
    }


    /**
     * Get the url for the Zotapay API
     *
     * @return string
     */
    public static function getApiUrl()
    {
        if (null !== self::$apiUrl) {
            return self::$apiUrl;
        }
        return self::$apiBase . '/api/' . self::$apiVersion;
    }


    /**
     * Set the url for the Zotapay API
     *
     * @param string $apiUrl
     *
     * @return self
     */
    public static function setApiUrl($apiUrl)
    {
        self::$apiUrl = $apiUrl;
    }


    /**
     * Get the value of The logger used to produce messages
     *
     * @return \Zotapay\Log\LoggerInterface
     */
    public static function getLogger()
    {
        if (null === self::$logger) {
            return new \Zotapay\Log\DefaultLogger(self::getLogThreshold());
        }

        return self::$logger;
    }


    /**
     * Set the value of The logger used to produce messages
     *
     * @param \Zotapay\Log\LoggerInterface $logger
     *
     * @return self
     */
    public static function setLogger($logger)
    {
        self::$logger = $logger;
    }


    /**
     * Get the value of log threshold
     *
     * @return string
     */
    public static function getLogThreshold()
    {
        return self::$logThreshold;
    }


    /**
     * Set the value of log threshold
     *
     * @param string options: emergency|alert|critical|error|warning|notice|info|debug.
     *
     * @return self
     */
    public static function setLogThreshold($logThreshold)
    {
        self::$logThreshold = $logThreshold;
    }

    /**
     * Get the value of The log destination
     *
     * @return string
     */
    public static function getLogDestination()
    {
        return self::$logDestination;
    }

    /**
     * Set the value of The log destination
     *
     * @param string $logDestination
     *
     * @return self
     */
    public static function setLogDestination($logDestination)
    {
        self::$logDestination = $logDestination;
    }

    /**
     * Get the mocke response array and reset the value.
     *
     * @return array
     */
    public static function getMockResponse()
    {
        return self::$mockResponse;
    }

    /**
     * Set the mock response data.
     *
     * @param array $mockResponse
     */
    public static function setMockResponse($mockResponse)
    {
        self::$mockResponse = $mockResponse;
    }
}
