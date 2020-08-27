<?php

namespace Zotapay;

/**
 * @internal
 */
final class PayoutTest extends \PHPUnit\Framework\TestCase
{
    use \Zotapay\TestHelper;

    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        $merchantOrderId = !empty(getenv('API_INTEGRATION_TESTS')) ? \Zotapay\Helper\Helper::generateUuid() : '1';

        return [
            [
                // data
                [
                    'merchantOrderID'   => $merchantOrderId,
                    'merchantOrderDesc' => 'Test order description',
                    'orderAmount'       => '100.00',
                    'orderCurrency'     => 'USD',
                    'customerEmail'     => 'customer@just-for-test.com',
                    'customerFirstName' => 'John',
                    'customerLastName'  => 'Lock',
                    'customerPhone'     => '+1 420-100-1000',
                    'customerIP'        => '134.201.250.130',
                    'customerBankCode'  => 'BBL',
                    'customerBankAccountNumber' => '1234567890', // other will be DECLINED, 0000000000 for ERROR
                    'customerBankAccountName' => 'John Doe',
                    'customerBankBranch' =>  'Bank Branch',
                    'customerBankAddress' => 'Thong Nai Pan Noi Beach, Baan Tai, Koh Phangan',
                    'customerBankZipCode' => '84280',
                    'customerBankRoutingNumber' => '000',
                    'customerBankProvince' => 'Bank Province',
                    'customerBankArea'  => 'Bank Area / City',
                    'callbackUrl'       => 'http:://localhost/callback',
                    'customParam'       => json_encode([ 'TestCustomParam' => '123' ]),
                    'language'          => 'EN',
                ],

                // ref
                [
                    'code' => 200,
                    'message' => null,
                    'data' => [
                        'merchantOrderID' => $merchantOrderId,
                        'orderID' => '1234',
                    ],
                    'httpCode' => 200,
                    'merchantOrderID' => $merchantOrderId,
                    'orderID' => '1234',
                    'request_signature' => '776bfa7f73a619d7c2cb3946bd8a70b67f56b4c9b3f55dd35f1468d83a45f4d1',
                ],
            ],
        ];
    }


    /**
     * Payout Request
     *
     * @dataProvider getData
     */
    public function testRequest($data, $ref)
    {
        $payoutOrder = new \Zotapay\PayoutOrder($data);
        $payout = new \Zotapay\Payout();
        if (!empty($this->apiClientStub)) {
            $payout->setApiRequest($this->apiClientStub);
        }
        $response = $payout->request($payoutOrder);

        static::assertNotFalse($payout);
        static::assertInstanceOf(\Zotapay\PayoutApiResponse::class, $response);

        static::assertSame($ref['httpCode'], $response->getHttpCode());
        static::assertSame($ref['code'], $response->getCode());
        static::assertSame($ref['message'], $response->getMessage());
        if (empty(getenv('API_INTEGRATION_TESTS'))) {
            static::assertSame($ref['data'], $response->getData());
            static::assertSame($ref['merchantOrderID'], $response->getMerchantOrderID());
            static::assertSame($ref['orderID'], $response->getOrderID());
        }
    }


    /**
     * Request prepare
     *
     * @dataProvider getData
     */
    public function testPrepare($order, $ref)
    {
        $payoutOrder = new \Zotapay\PayoutOrder($order);
        $payout = new \Zotapay\Payout();

        $reflection = new \ReflectionClass(get_class($payout));
        $method = $reflection->getMethod('prepare');
        $method->setAccessible(true);
        $prepare = $method->invokeArgs($payout, array($payoutOrder));

        $this->assertArrayHasKey('merchantOrderID', $prepare);
        $this->assertArrayHasKey('merchantOrderDesc', $prepare);
        $this->assertArrayHasKey('orderAmount', $prepare);
        $this->assertArrayHasKey('orderCurrency', $prepare);
        $this->assertArrayHasKey('customerEmail', $prepare);
        $this->assertArrayHasKey('customerFirstName', $prepare);
        $this->assertArrayHasKey('customerLastName', $prepare);
        $this->assertArrayHasKey('customerPhone', $prepare);
        $this->assertArrayHasKey('customerIP', $prepare);
        $this->assertArrayHasKey('customerBankCode', $prepare);
        $this->assertArrayHasKey('customerBankAccountNumber', $prepare);
        $this->assertArrayHasKey('customerBankAccountName', $prepare);
        $this->assertArrayHasKey('customerBankBranch', $prepare);
        $this->assertArrayHasKey('customerBankAddress', $prepare);
        $this->assertArrayHasKey('customerBankZipCode', $prepare);
        $this->assertArrayHasKey('customerBankRoutingNumber', $prepare);
        $this->assertArrayHasKey('customerBankProvince', $prepare);
        $this->assertArrayHasKey('customerBankArea', $prepare);
        $this->assertArrayHasKey('callbackUrl', $prepare);
        $this->assertArrayHasKey('customParam', $prepare);
        $this->assertArrayHasKey('language', $prepare);
    }


    /**
     * Request signing
     *
     * @dataProvider getData
     */
    public function testSign($order, $ref)
    {
        if (!empty(getenv('API_INTEGRATION_TESTS'))) {
            $this->markTestSkipped('Not aplicable in integration tests.');
        }

        $payout = new \Zotapay\Payout();

        $reflection = new \ReflectionClass(get_class($payout));
        $method = $reflection->getMethod('sign');
        $method->setAccessible(true);
        $signed = $method->invokeArgs($payout, array($order));

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

        $payoutOrder = new \Zotapay\PayoutOrder();
        $payout = new \Zotapay\Payout();

        $response = $payout->request($payoutOrder);

        static::assertInstanceOf(\Zotapay\PayoutApiResponse::class, $response);

        static::assertSame($ref['code'], $response->getCode());
        static::assertSame($ref['httpCode'], $response->getHttpCode());
        static::assertSame($ref['message'], $response->getMessage());

        static::assertNull($payout->getMockResponse());
    }
}
