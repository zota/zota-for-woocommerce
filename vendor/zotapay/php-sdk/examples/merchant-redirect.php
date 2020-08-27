<?php

require 'config.php';

/**
 * Redirect ----------------------------------------
 */

// mock data
$data = [
    'approved' => [
        'billingDescriptor'   => 'sandbox-payment',
        'errorMessage'   => null,
        'merchantOrderID'   => '1',
        'orderID'   => '12345678',
        'signature'   => '6a4f1ad55ee636e65b8aece10b1025f28566c2896b23d623a42e101b905d043c',
        'status'   => 'APPROVED',
    ],
    'declined' => [
        'billingDescriptor'   => 'sandbox-payment',
        'errorMessage'   => 'dummy+sandbox+declined',
        'merchantOrderID'   => '1',
        'orderID'   => '12345678',
        'signature'   => 'dd95963f4a1f8a393e710f878f7a17bba235ac6d76b82f8907daa26bf792959f',
        'status'   => 'DECLINED',
    ],
    'filtered' => [
        'billingDescriptor'   => 'sandbox-payment',
        'errorMessage'   => 'dummy+sandbox+filtered',
        'merchantOrderID'   => '1',
        'orderID'   => '12345678',
        'signature'   => 'e3311081be54dc25dad8209eb9e2ae70e70cfbab1c7a2b78367539aad5245fba',
        'status'   => 'FILTERED',
    ],
    'pending' => [
        'billingDescriptor'   => 'sandbox-payment',
        'errorMessage'   => null,
        'merchantOrderID'   => '1',
        'orderID'   => '12345678',
        'signature'   => '78c0babd52fa86187ac3699913eb3bc98e44eca56587bb094293500b5a87a692',
        'status'   => 'PENDING',
    ],
    'unknown' => [
        'billingDescriptor'   => 'sandbox-payment',
        'errorMessage'   => 'dummy+sandbox+unknown',
        'merchantOrderID'   => '1',
        'orderID'   => '12345678',
        'signature'   => 'a2f74162c155f12832e7e2c62e5a45a87045f6debe47f6ec7555f0efc650fbab',
        'status'   => 'UNKNOWN',
    ],
    'error' => [
        'billingDescriptor'   => 'sandbox-payment',
        'errorMessage'   => 'dummy+sandbox+error',
        'merchantOrderID'   => '1',
        'orderID'   => '12345678',
        'signature'   => '18995477838a39b739768510bc5afb3158a7d175e03d2b4e630410a562306eb0',
        'status'   => 'ERROR',
    ],
];

// simulate redirect case
foreach ($data['error'] as $key => $value) {
    if (empty($value)) {
        continue;
    }
    $_GET[$key] = $value;
}

try {
    \Zotapay\Zotapay::setMerchantSecretKey('MERCHANT-SECRET-KEY');
    $redirect = new \Zotapay\MerchantRedirect();

    // result
    echo '<pre>';
    echo 'billingDescriptor: ' . $redirect->getBillingDescriptor() . '<br>';
    echo 'errorMessage: ' . $redirect->getErrorMessage() . '<br>';
    echo 'merchantOrderID: ' . $redirect->getMerchantOrderID() . '<br>';
    echo 'orderID: ' . $redirect->getOrderID() . '<br>';
    echo 'signature: ' . $redirect->getSignature() . '<br>';
    echo 'status: ' . $redirect->getStatus() . '<br>';
    echo '</pre>';
} catch (\Zotapay\Exception\InvalidSignatureException $e) {
    echo $e->getMessage();
}
