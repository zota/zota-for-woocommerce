<?php

require 'config.php';

/**
 * Payout ----------------------------------------
 */
$order = new \Zotapay\PayoutOrder();

$order->setMerchantOrderID('312');
$order->setMerchantOrderDesc('Test order description');
$order->setOrderAmount('100.00');
$order->setOrderCurrency('USD');
$order->setCustomerEmail('testing@zotapay-php-sdk.com');
$order->setCustomerFirstName('John');
$order->setCustomerLastName('Lock');
$order->setCustomerPhone('+1 420-100-1000');
$order->setCustomerIP('134.201.250.130');
$order->setCustomerBankCode('BBL');
$order->setCustomerBankAccountNumber('1234567890');
$order->setCustomerBankAccountName('John Doe');
$order->setCustomerBankBranch('Bank Branch');
$order->setCustomerBankAddress('Thong Nai Pan Noi Beach, Baan Tai, Koh Phangan');
$order->setCustomerBankZipCode('84280');
$order->setCustomerBankRoutingNumber('000');
$order->setCustomerBankProvince('Bank Province');
$order->setCustomerBankArea('Bank Area / City');
$order->setCallbackUrl(ZOTAPAY_EXAMPLES_URL . '/callback.php');
$order->setCustomParam(json_encode([ 'TestCustomParam' => '123' ]));
$order->setLanguage('EN');

// request
$operation = new \Zotapay\Payout();
$response = $operation->request($order);

// result
echo '<pre>';
echo 'code: ' . $response->getCode() . '<br>';
echo 'message: ' . $response->getMessage() . '<br>';
echo 'data: ' . print_r($response->getData(), true) . '<br>';
echo 'httpCode: ' . $response->getHttpCode() . '<br>';
echo 'merchantOrderID: ' . $response->getMerchantOrderID() . '<br>';
echo 'orderID: ' . $response->getOrderID() . '<br>';
echo '</pre>';
