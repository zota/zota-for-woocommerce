<?php

namespace Zotapay;

/**
 * @internal
 */
final class DepositApiResponseTest extends \PHPUnit\Framework\TestCase
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
                    '{"code":"200","message":"","data":{"depositUrl":"https://example.com","merchantOrderID":"100","orderID":"1234"}}',
                    200
                ],

                // ref
                [
                    'depositUrl' => 'https://example.com',
                    'merchantOrderID' => '100',
                    'orderID' => '1234',
                ]
            ],
            [
                // httpClientRequest
                [
                    '{"code":"400","message":"Error.","data":{}',
                    400
                ],

                // ref
                [
                    'depositUrl' => null,
                    'merchantOrderID' => null,
                    'orderID' => null,
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
        $apiResponse = new \Zotapay\DepositApiResponse($httpClientRequest);

        static::assertSame($ref['depositUrl'], $apiResponse->getDepositUrl());
        static::assertSame($ref['merchantOrderID'], $apiResponse->getmerchantOrderID());
        static::assertSame($ref['orderID'], $apiResponse->getorderID());
    }
}
