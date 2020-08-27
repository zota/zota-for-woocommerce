<?php

namespace Zotapay;

/**
 * @internal
 */
final class DepositCCApiResponseTest extends \PHPUnit\Framework\TestCase
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
                    '{"code":"200","message":"","data":{"status":"success","merchantOrderID":"100","orderID":"1234"}}',
                    200
                ],

                // ref
                [
                    'status' => 'success',
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
                    'status' => null,
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
        $apiResponse = new \Zotapay\DepositCCApiResponse($httpClientRequest);

        static::assertSame($ref['status'], $apiResponse->getStatus());
        static::assertSame($ref['merchantOrderID'], $apiResponse->getmerchantOrderID());
        static::assertSame($ref['orderID'], $apiResponse->getorderID());
    }
}
