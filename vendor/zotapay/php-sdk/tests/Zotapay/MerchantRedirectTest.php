<?php

namespace Zotapay;

/**
 * @internal
 */
final class MerchantRedirectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            // approved
            [
                [
                    'billingDescriptor'   => 'sandbox-payment',
                    'errorMessage'   => null,
                    'merchantOrderID'   => '1',
                    'orderID'   => '12345678',
                    'signature'   => '6a4f1ad55ee636e65b8aece10b1025f28566c2896b23d623a42e101b905d043c',
                    'status'   => 'APPROVED',
                ],
            ],

            // declined
            [
                [
                    'billingDescriptor'   => 'sandbox-payment',
                    'errorMessage'   => 'dummy+sandbox+declined',
                    'merchantOrderID'   => '1',
                    'orderID'   => '12345678',
                    'signature'   => 'dd95963f4a1f8a393e710f878f7a17bba235ac6d76b82f8907daa26bf792959f',
                    'status'   => 'DECLINED',
                ],
            ],

            // filtered
            [
                [
                    'billingDescriptor'   => 'sandbox-payment',
                    'errorMessage'   => 'dummy+sandbox+filtered',
                    'merchantOrderID'   => '1',
                    'orderID'   => '12345678',
                    'signature'   => 'e3311081be54dc25dad8209eb9e2ae70e70cfbab1c7a2b78367539aad5245fba',
                    'status'   => 'FILTERED',
                ],
            ],

            // pending
            [
                [
                    'billingDescriptor'   => 'sandbox-payment',
                    'errorMessage'   => null,
                    'merchantOrderID'   => '1',
                    'orderID'   => '12345678',
                    'signature'   => '78c0babd52fa86187ac3699913eb3bc98e44eca56587bb094293500b5a87a692',
                    'status'   => 'PENDING',
                ],
            ],

            // unknown
            [
                [
                    'billingDescriptor'   => 'sandbox-payment',
                    'errorMessage'   => 'dummy+sandbox+unknown',
                    'merchantOrderID'   => '1',
                    'orderID'   => '12345678',
                    'signature'   => 'a2f74162c155f12832e7e2c62e5a45a87045f6debe47f6ec7555f0efc650fbab',
                    'status'   => 'UNKNOWN',
                ],
            ],

            // error
            [
                [
                    'billingDescriptor'   => 'sandbox-payment',
                    'errorMessage'   => 'dummy+sandbox+error',
                    'merchantOrderID'   => '1',
                    'orderID'   => '12345678',
                    'signature'   => '18995477838a39b739768510bc5afb3158a7d175e03d2b4e630410a562306eb0',
                    'status'   => 'ERROR',
                ],
            ],
        ];
    }


    /**
     * Merchant Redirect
     *
     * @runInSeparateProcess
     * @dataProvider getData
     */
    public function testMerchantRedirect($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $_GET[$key] = $value;
        }

        \Zotapay\Zotapay::setMerchantSecretKey('MERCHANT-SECRET-KEY');
        $merchantRedirect = new \Zotapay\MerchantRedirect();

        // test getters
        static::assertSame($data['billingDescriptor'], $merchantRedirect->getBillingDescriptor());
        static::assertSame($data['errorMessage'], $merchantRedirect->getErrorMessage());
        static::assertSame($data['merchantOrderID'], $merchantRedirect->getMerchantOrderID());
        static::assertSame($data['orderID'], $merchantRedirect->getOrderID());
        static::assertSame($data['signature'], $merchantRedirect->getSignature());
        static::assertSame($data['status'], $merchantRedirect->getStatus());
    }


    /**
     * Signature Verify with wrong Merchant Secret Key
     *
     * @runInSeparateProcess
     * @dataProvider getData
     */
    public function testWithWrongSignature($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $_GET[$key] = $value;
        }

        \Zotapay\Zotapay::setLogThreshold('critical');
        \Zotapay\Zotapay::setMerchantSecretKey('WRONG-SECRET-KEY');
        $this->expectException(\Zotapay\Exception\InvalidSignatureException::class);
        $merchantRedirect = new \Zotapay\MerchantRedirect();
    }


    /**
     * Signature Verify without signature
     *
     * @dataProvider getData
     */
    public function testSignatureVerifyWithoutSignature($data)
    {
        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }
            $_GET[$key] = $value;
        }

        \Zotapay\Zotapay::setMerchantSecretKey('MERCHANT-SECRET-KEY');
        $merchantRedirect = new \Zotapay\MerchantRedirect();
        $merchantRedirectReflector = new \ReflectionClass('\Zotapay\MerchantRedirect');

        $signatureVerify = $merchantRedirectReflector->getMethod('signatureVerify');
        $signatureVerify->setAccessible(true);

        unset($_GET['signature']);
        $signature = $signatureVerify->invoke($merchantRedirect, $data, '', '');

        $this->assertFalse($signature);
    }
}
