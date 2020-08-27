<?php

namespace Zotapay;

/**
 * @internal
 */
final class ApiResponseTest extends \PHPUnit\Framework\TestCase
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
                    '{"code":"409","message":"order already created"}',
                    409
                ],

                // ref
                [
                    'body' => '{"code":"409","message":"order already created"}',
                    'httpCode' => 409,
                    'json' => '{"code":"409","message":"order already created"}',
                    'code' => 409,
                    'data' => null,
                    'message' => 'order already created',
                ]
            ],
            [
                // httpClientRequest
                false,

                // ref
                [
                    'body' => null,
                    'httpCode' => null,
                    'json' => null,
                    'code' => null,
                    'data' => null,
                    'message' => 'Zotapay API Response not valid.',
                ]
            ],
            [
                // httpClientRequest
                [
                    '',
                    500
                ],

                // ref
                [
                    'body' => '',
                    'httpCode' => 500,
                    'json' => false,
                    'code' => null,
                    'data' => null,
                    'message' => 'Zotapay API Response JSON error: Syntax error',
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
        $apiResponse = new \Zotapay\ApiResponse($httpClientRequest, $ref);

        static::assertSame($ref['body'], $apiResponse->getBody());
        static::assertSame($ref['httpCode'], $apiResponse->getHttpCode());
        static::assertSame($ref['json'], $apiResponse->getJson());
        static::assertSame($ref['code'], $apiResponse->getCode());
        static::assertSame($ref['data'], $apiResponse->getData());
        static::assertSame($ref['message'], $apiResponse->getMessage());
    }
}
