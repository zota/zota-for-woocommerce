<?php

namespace Zotapay;

/**
 * @internal
 */
final class OrdersReportApiResponseTest extends \PHPUnit\Framework\TestCase
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
                    'raw csv goes here',
                    200
                ],

                // ref
                [
                    'data' => 'raw csv goes here',
                    'message' => null,
                    'code' => 200,
                ],
            ],
            [
                // httpClientRequest
                [
                    '{"code":"400","message":"Error.","data":{}}',
                    400
                ],

                // ref
                [
                    'data' => null,
                    'message' => 'Error.',
                    'code' => 400,
                ],
            ],
            [
                // httpClientRequest
                false,

                // ref
                [
                    'data' => null,
                    'message' => 'Zotapay API Response not valid.',
                    'code' => null,
                ],
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
        $apiResponse = new \Zotapay\OrdersReportApiResponse($httpClientRequest);

        static::assertSame($ref['data'], $apiResponse->getData());
        static::assertSame($ref['message'], $apiResponse->getMessage());
        static::assertSame($ref['code'], $apiResponse->getCode());
    }
}
