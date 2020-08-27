<?php

namespace Zotapay;

/**
 * Class OrdersReportApiResponse.
 */
class OrdersReportApiResponse extends ApiResponse
{
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
        /**
         * If the response is valid JSON there must be an error.
         * The valid response contains CSV data.
         */
        if (\json_last_error() === JSON_ERROR_NONE) {
            $this->json = $this->body;
            $this->code = !empty($json['code']) ? (int)$json['code'] : null;
            $this->data = !empty($json['data']) ? $json['data'] : null;
            $this->message = !empty($json['message']) ? (string)$json['message'] : null;
        } else {
            $this->data = $this->body;
            $this->code = $this->httpCode;
        }
    }
}
