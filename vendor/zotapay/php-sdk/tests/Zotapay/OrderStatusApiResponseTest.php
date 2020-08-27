<?php

namespace Zotapay;

/**
 * @internal
 */
final class OrderStatusApiResponseTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            [
                // httpClientRequest
                [
                    '{
                        "code": "200",
                        "data": {
                            "type": "SALE",
                            "status": "PROCESSING",
                            "errorMessage": "",
                            "endpointID": "1050",
                            "processorTransactionID": "",
                            "orderID": "8b3a6b89697e8ac8f45d964bcc90c7ba41764acd",
                            "merchantOrderID": "QvE8dZshpKhaOmHY",
                            "amount": "500.00",
                            "currency": "THB",
                            "customerEmail": "customer@email-address.com",
                            "customParam": "{\"UserId\": \"e139b447\"}",
                            "extraData": "",
                            "request": {
                                "merchantID": "EXAMPLE-MERCHANT-ID",
                                "orderID": "8b3a6b89697e8ac8f45d964bcc90c7ba41764acd",
                                "merchantOrderID": "QvE8dZshpKhaOmHY",
                                "timestamp": "1564617600"
                            }
                        }
                    }',
                    200
                ],

                // ref
                [
                    "type" => "SALE",
                    "status" => "PROCESSING",
                    "errorMessage" => "",
                    "endpointID" => "1050",
                    "processorTransactionID" => "",
                    "orderID" => "8b3a6b89697e8ac8f45d964bcc90c7ba41764acd",
                    "merchantOrderID" => "QvE8dZshpKhaOmHY",
                    "amount" => "500.00",
                    "currency" => "THB",
                    "customerEmail" => "customer@email-address.com",
                    "customParam" => "{\"UserId\": \"e139b447\"}",
                    "extraData" => "",
                    "requestMerchantID" => "EXAMPLE-MERCHANT-ID",
                    "requestOrderID" => "8b3a6b89697e8ac8f45d964bcc90c7ba41764acd",
                    "requestMerchantOrderID" => "QvE8dZshpKhaOmHY",
                    "timestamp" => "1564617600",
                ]
            ],
        ];
    }


    /**
     * ApiResponse getters
     *
     * @dataProvider getData
     */
    public function testGetters($httpClientRequest, $ref)
    {
        $apiResponse = new \Zotapay\OrderStatusApiResponse($httpClientRequest);

        static::assertSame($ref['type'], $apiResponse->getType());
        static::assertSame($ref['status'], $apiResponse->getStatus());
        static::assertSame($ref['errorMessage'], $apiResponse->getErrorMessage());
        static::assertSame($ref['endpointID'], $apiResponse->getEndpointID());
        static::assertSame($ref['processorTransactionID'], $apiResponse->getProcessorTransactionID());
        static::assertSame($ref['orderID'], $apiResponse->getOrderID());
        static::assertSame($ref['merchantOrderID'], $apiResponse->getMerchantOrderID());
        static::assertSame($ref['amount'], $apiResponse->getAmount());
        static::assertSame($ref['currency'], $apiResponse->getCurrency());
        static::assertSame($ref['customerEmail'], $apiResponse->getCustomerEmail());
        static::assertSame($ref['customParam'], $apiResponse->getCustomParam());
        static::assertSame($ref['extraData'], $apiResponse->getExtraData());

        static::assertSame($ref['requestMerchantID'], $apiResponse->getRequestMerchantID());
        static::assertSame($ref['requestMerchantOrderID'], $apiResponse->getRequestMerchantOrderID());
        static::assertSame($ref['requestOrderID'], $apiResponse->getRequestOrderID());
        static::assertSame($ref['timestamp'], $apiResponse->getRequestTimestamp());
    }
}
