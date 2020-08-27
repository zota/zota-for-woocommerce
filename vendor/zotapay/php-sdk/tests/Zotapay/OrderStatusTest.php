<?php

namespace Zotapay;

/**
 * @internal
 */
final class OrderStatusTest extends \PHPUnit\Framework\TestCase
{
    use \Zotapay\TestHelper;

    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            [
                // data
                [
                    'merchantOrderID' => '1',
                    'orderID'         => '1234',
                ],

                // ref
                [
                    'code' => 200,
                    'message' => null,
                    'data' => [
                        'merchantOrderID' => '1',
                        'orderID' => '1234',
                    ],
                    'httpCode' => 200,
                    'merchantOrderID' => '1',
                    'orderID' => '1234',
                    'request_signature' => '0aa2b86f7a6acd98e2e4d97c732cb093c99029ae993a56b7ddaa5b60999407af',
                ]
            ],
        ];
    }

    /**
     * Order Status Request
     *
     * @dataProvider getData
     */
    public function testRequest($data, $ref)
    {
        if (!empty(getenv('API_INTEGRATION_TESTS'))) {
            $merchantOrderID = \Zotapay\Helper\Helper::generateUuid();
            $order = [
                'merchantOrderID'   => $merchantOrderID,
                'merchantOrderDesc' => 'Test order description',
                'orderAmount'       => '100.00',
                'orderCurrency'     => 'USD',
                'customerEmail'     => 'testing@zotapay-api.com',
                'customerFirstName' => 'John',
                'customerLastName'  => 'Lock',
                'customerAddress'   => 'The Swan, Jungle St. 108',
                'customerCountryCode' =>  'US',
                'customerCity'      => 'Los Angeles',
                'customerState'     => 'CA',
                'customerZipCode'   => '90015',
                'customerPhone'     => '+1 420-100-1000',
                'customerIP'        => '134.201.250.130',
                'customerBankCode'  => 'BBL',
                'redirectUrl'       => 'http:://localhost/redirect',
                'callbackUrl'       => 'http:://localhost/callback',
                'checkoutUrl'       => 'http:://localhost/checkout',
                'customParam'       => json_encode([ 'TestCustomParam' => '123' ]),
                'language'          => 'EN',
            ];

            $depositOrder = new \Zotapay\DepositOrder($order);
            $deposit = new \Zotapay\Deposit();
            $response = $deposit->request($depositOrder);

            $data['merchantOrderID'] = $merchantOrderID;
            $data['orderID'] = $response->getOrderID();

            $ref['data'] = [
                'merchantOrderID' => $merchantOrderID,
                'orderID' => $response->getOrderID(),
            ];
        }

        $orderStatusData = new \Zotapay\OrderStatusData($data);
        $orderStatus = new \Zotapay\OrderStatus();
        if (!empty($this->apiClientStub)) {
            $orderStatus->setApiRequest($this->apiClientStub);
        }
        $response = $orderStatus->request($orderStatusData);

        static::assertNotFalse($orderStatus);
        static::assertInstanceOf(\Zotapay\OrderStatusApiResponse::class, $response);

        static::assertSame($ref['code'], $response->getCode());
        static::assertSame($ref['httpCode'], $response->getHttpCode());

        static::assertSame($ref['data']['orderID'], $response->getOrderID());
    }

    /**
     * Request prepare
     *
     * @dataProvider getData
     */
    public function testPrepare($data, $ref)
    {
        $OrderStatusData = new \Zotapay\OrderStatusData($data);
        $OrderStatus = new \Zotapay\OrderStatus();

        $reflection = new \ReflectionClass(get_class($OrderStatus));
        $method = $reflection->getMethod('prepare');
        $method->setAccessible(true);
        $prepare = $method->invokeArgs($OrderStatus, array($OrderStatusData));

        $this->assertArrayHasKey('merchantID', $prepare);
        $this->assertArrayHasKey('merchantOrderID', $prepare);
        $this->assertArrayHasKey('orderID', $prepare);
        $this->assertArrayHasKey('timestamp', $prepare);
    }

    /**
     * Request signing
     *
     * @dataProvider getData
     */
    public function testSign($data, $ref)
    {
        if (!empty(getenv('API_INTEGRATION_TESTS'))) {
            $this->markTestSkipped('Not aplicable in integration tests.');
        }

        // Set static timestamp so that we can test time-based code.
        $timestamp = '1594223794';
        $data['timestamp'] = $timestamp;
        $data['merchantID'] = \Zotapay\Zotapay::getMerchantId();

        $orderStatus = new \Zotapay\OrderStatus();

        $reflection = new \ReflectionClass(get_class($orderStatus));
        $method = $reflection->getMethod('sign');
        $method->setAccessible(true);
        $signed = $method->invokeArgs($orderStatus, [$data]);

        $this->assertEquals($ref['request_signature'], $signed['signature']);
    }

    /**
     * Test mocking respons.
     *
     * @dataProvider getMockData
     */
    public function testMockResponse($mockResponse, $ref)
    {
        \Zotapay\Zotapay::setMockResponse($mockResponse);

        $orderStatusData = new \Zotapay\OrderStatusData();
        $orderStatus = new \Zotapay\OrderStatus();

        $response = $orderStatus->request($orderStatusData);

        static::assertInstanceOf(\Zotapay\OrderStatusApiResponse::class, $response);

        static::assertSame($ref['code'], $response->getCode());
        static::assertSame($ref['httpCode'], $response->getHttpCode());
        static::assertSame($ref['message'], $response->getMessage());

        static::assertNull($orderStatus->getMockResponse());
    }
}
