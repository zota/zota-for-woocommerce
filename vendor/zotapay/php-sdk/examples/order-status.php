<?php

require 'config.php';

/**
 * Orders Status ----------------------------------------
 */

// setup data
$data = new \Zotapay\OrderStatusData();

$data->setOrderID('24043625');
$data->setMerchantOrderID('170');

// request
$operation = new \Zotapay\OrderStatus();
$response = $operation->request($data);

// result
echo '<pre>';
echo 'code: ' . $response->getCode() . '<br>';
echo 'message: ' . $response->getMessage() . '<br>';
echo 'data: ' . print_r($response->getData(), true) . '<br>';
echo 'type: ' . $response->getType() . '<br>';
echo 'status: ' . $response->getStatus() . '<br>';
echo 'errorMessage: ' . $response->getErrorMessage() . '<br>';
echo 'endpointID: ' . $response->getEndpointID() . '<br>';
echo 'processorTransactionID: ' . $response->getProcessorTransactionID() . '<br>';
echo 'orderID: ' . $response->getOrderID() . '<br>';
echo 'merchantOrderID: ' . $response->getMerchantOrderID() . '<br>';
echo 'amount: ' . $response->getAmount() . '<br>';
echo 'currency: ' . $response->getCurrency() . '<br>';
echo 'customerEmail: ' . $response->getCustomerEmail() . '<br>';
echo 'customParam: ' . $response->getCustomParam() . '<br>';
echo 'extraData: ' . print_r($response->getExtraData(), true) . '<br>';
echo 'requestMerchantID: ' . $response->getRequestMerchantID() . '<br>';
echo 'requestMerchantOrderID: ' . $response->getRequestMerchantOrderID() . '<br>';
echo 'requestOrderID: ' . $response->getRequestOrderID() . '<br>';
echo 'requestTimestamp: ' . $response->getRequestTimestamp() . '<br>';
echo '</pre>';
