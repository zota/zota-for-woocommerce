<?php

namespace Zotapay;

/**
 * Class ApiResponse.
 */
class ApiResponse
{
    /**
     * Body of the response
     *
     * @var null|string
     */
    protected $body;

    /**
     * HTTP code of the response
     *
     * @var null|int
     */
    protected $httpCode;

    /**
     * Response JSON
     *
     * @var null|string
     */
    protected $json;

    /**
     * Response code
     *
     * @var null|int
     */
    protected $code;

    /**
     * Response data
     *
     * @var null|array
     */
    protected $data;

    /**
     * Response message
     *
     * @var null|string
     */
    protected $message;

    /**
     * Class constructor, sets various properties from HTTP response data.
     *
     * @param bool|array $httpClientRequest Array with first element body of the response
     *                                      and second optional element the HTTP response code
     */
    public function __construct($httpClientRequest)
    {
        if (false === $httpClientRequest) {
            $this->message = 'Zotapay API Response not valid.';
            return;
        }

        $this->body = $httpClientRequest[0]; // response body
        $this->httpCode = $httpClientRequest[1]; // http response code

        $json = \json_decode($this->body, JSON_OBJECT_AS_ARRAY);
        if (\json_last_error() !== JSON_ERROR_NONE) {
            $this->json = false;
            $this->message = 'Zotapay API Response JSON error: ' . \json_last_error_msg();
            return;
        }

        $this->json = $this->body;
        $this->code = !empty($json['code']) ? (int)$json['code'] : null;
        $this->data = !empty($json['data']) ? $json['data'] : null;
        $this->message = !empty($json['message']) ? (string)$json['message'] : null;
    }


    /**
     * Get the value of Body of the response
     *
     * @return null|string
     */
    public function getBody()
    {
        return $this->body;
    }


    /**
     * Get the value of HTTP code of the response
     *
     * @return null|int
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }


    /**
     * Get the value of Response JSON
     *
     * @return null|string
     */
    public function getJson()
    {
        return $this->json;
    }


    /**
     * Get the value of Response code
     *
     * @return null|int
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Get the value of Response message
     *
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }


    /**
     * Get the value of Response data
     *
     * @return null|array
     */
    public function getData()
    {
        return $this->data;
    }
}
