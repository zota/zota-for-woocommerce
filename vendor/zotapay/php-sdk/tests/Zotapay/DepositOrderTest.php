<?php

namespace Zotapay;

/**
 * @internal
 */
final class DepositOrderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        $order = [
            'merchantOrderID'   => '1',
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
            'customerBankCode'  => '',
            'redirectUrl'       => 'http:://localhost/redirect',
            'callbackUrl'       => 'http:://localhost/callback',
            'checkoutUrl'       => 'http:://localhost/checkout',
            'customParam'       => json_encode([ 'TestCustomParam' => '123' ]),
            'language'          => 'EN',
        ];

        $cardData = [
            'cardHolderName' => 'UNIT TEST',
            'cardNumber'     => '4222222222222222',
            'cardExpirationMonth' => '12',
            'cardExpirationYear' => date('y'),
            'cardCvv'        => '123',
        ];

        return [
            [
                $order
            ],
            [
                array_merge($order, $cardData)
            ],
        ];
    }


    /**
     * Gets Object Instance without Data
     */
    public function testDepositOrderWithoutData()
    {
        $depositOrder = new \Zotapay\DepositOrder();

        $this->assertNull($depositOrder->getCustomerAddress());
        $this->assertNull($depositOrder->getCustomerCountryCode());
        $this->assertNull($depositOrder->getCustomerCity());
        $this->assertNull($depositOrder->getCustomerState());
        $this->assertNull($depositOrder->getCustomerZipCode());
        $this->assertNull($depositOrder->getRedirectUrl());
        $this->assertNull($depositOrder->getCheckoutUrl());
        $this->assertNull($depositOrder->getCardHolderName());
        $this->assertNull($depositOrder->getCardNumber());
        $this->assertNull($depositOrder->getCardExpirationMonth());
        $this->assertNull($depositOrder->getCardExpirationYear());
        $this->assertNull($depositOrder->getCardCvv());
    }


    /**
     * Gets Object Instance with Data in Constructor
     *
     * @dataProvider getData
     */
    public function testDepositOrderWithData($data)
    {
        $zotapayDepositOrder = new \Zotapay\DepositOrder($data);

        $this->assertInstanceOf(\Zotapay\DepositOrder::class, $zotapayDepositOrder);
    }


    /**
     * Test getters
     *
     * @dataProvider getData
     */
    public function testGetters($data)
    {
        $zotapayDepositOrder = new \Zotapay\DepositOrder($data);

        foreach ($data as $key => $value) {
            $getter = 'get' . \ucwords($key);

            $this->assertEquals($zotapayDepositOrder->$getter(), $value);
        }
    }
}
