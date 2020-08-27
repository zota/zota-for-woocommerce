<?php

require 'config.php';

/**
 * Deposit ----------------------------------------
 */

// setup order
$order = new \Zotapay\DepositOrder();

$order->setMerchantOrderID('180');
$order->setMerchantOrderDesc('Test order description');
$order->setOrderAmount('100.00');
$order->setOrderCurrency('USD');
$order->setCustomerEmail('testing@zotapay-php-sdk.com');
$order->setCustomerFirstName('John');
$order->setCustomerLastName('Lock');
$order->setCustomerAddress('The Swan, Jungle St. 108');
$order->setCustomerCountryCode('US');
$order->setCustomerCity('Los Angeles');
$order->setCustomerState('CA');
$order->setCustomerZipCode('90015');
$order->setCustomerPhone('+1 420-100-1000');
$order->setCustomerIP('134.201.250.130');
$order->setCustomerBankCode('');
$order->setRedirectUrl(ZOTAPAY_EXAMPLES_URL . '/redirect.php');
$order->setCallbackUrl(ZOTAPAY_EXAMPLES_URL . '/callback.php');
$order->setCheckoutUrl(ZOTAPAY_EXAMPLES_URL . '/checkout.php');
$order->setCustomParam(json_encode([ 'TestCustomParam' => '123' ]));
$order->setLanguage('EN');

// request
$operation = new \Zotapay\Deposit();
$response = $operation->request($order);

// result
echo '<pre>';
echo 'code: ' . $response->getCode() . '<br>';
echo 'message: ' . $response->getMessage() . '<br>';
echo 'data: ' . print_r($response->getData(), true) . '<br>';
echo 'httpCode: ' . $response->getHttpCode() . '<br>';
echo 'depositUrl: ' . $response->getDepositUrl() . '<br>';
echo 'merchantOrderID: ' . $response->getMerchantOrderID() . '<br>';
echo 'orderID: ' . $response->getOrderID() . '<br>';
echo '</pre>';
