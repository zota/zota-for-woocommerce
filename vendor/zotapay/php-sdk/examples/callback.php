<?php

require 'config.php';

/**
 * Callback ----------------------------------------
 */

// mock data
$data = '{
    "type":"SALE",
    "status":"FILTERED",
    "errorMessage":"dummy sandbox filter",
    "endpointID":"503364",
    "processorTransactionID":"ee7c9d1b-5e68-4408-9794-5d40aedead6c",
    "orderID":"24043630",
    "merchantOrderID":"172",
    "amount":"100.00",
    "currency":"USD",
    "customerEmail":"testing@api-requests.com",
    "customParam":"",
    "extraData":{
        "billingDescriptor":"sandbox-payment",
        "card":{
            "cvv":"***",
            "expiration":"03/2023",
            "holder":"A A",
            "number":"000000***1111"
        },
        "cardData":{
            "bank":{},
            "brand":"",
            "country":{}
        },
        "dcc":false,
        "paymentMethod":"CREDITCARD"
    },
    "originalRequest":{
        "merchantOrderID":"172",
        "merchantOrderDesc":"Test order description",
        "orderAmount":"100.00","orderCurrency":"USD",
        "customerEmail":"testing@api-requests.com",
        "customerFirstName":"John",
        "customerLastName":"Lock",
        "customerAddress":"The Swan, Jungle St. 108",
        "customerCountryCode":"US","customerCity":"Los Angeles",
        "customerState":"CA",
        "customerZipCode":"90015",
        "customerPhone":"1-420-100-1000",
        "customerIP":"134.201.250.130",
        "redirectUrl":"https://example.com/redirect.php",
        "callbackUrl":"https://example.com/callback.php",
        "checkoutUrl":"https://example.com/checkout.php",
        "signature":"4b92c1b81807a302e4db98028b6fe9bfb94d802df0d0582798ae416119184e5a",
        "requestedAt":"0001-01-01T00:00:00Z"
    },
    "signature":"e89454f30a99349084ab2581a526c3b7d4f5a5ab7c55015f819c74e24012670f"
}';
$filename = dirname(__FILE__) . '/' . uniqid() . '.json';

// simulate callback
try {
    file_put_contents($filename, $data);
    $callback = new \Zotapay\ApiCallback($filename);

    // result
    echo '<pre>';
    echo 'type: ' . $callback->getType() . '<br>';
    echo 'status: ' . $callback->getStatus() . '<br>';
    echo 'errorMessage: ' . $callback->getErrorMessage() . '<br>';
    echo 'endpointID: ' . $callback->getEndpointID() . '<br>';
    echo 'processorTransactionID: ' . $callback->getProcessorTransactionID() . '<br>';
    echo 'orderID: ' . $callback->getOrderID() . '<br>';
    echo 'merchantOrderID: ' . $callback->getMerchantOrderID() . '<br>';
    echo 'amount: ' . $callback->getAmount() . '<br>';
    echo 'currency: ' . $callback->getCurrency() . '<br>';
    echo 'customerEmail: ' . $callback->getCustomerEmail() . '<br>';
    echo 'customParam: ' . $callback->getCustomParam() . '<br>';
    echo 'extraData: ' . print_r($callback->getExtraData(), true) . '<br>';
    echo 'originalRequest: ' . print_r($callback->getOriginalRequest(), true) . '<br>';
    echo 'signature: ' . $callback->getSignature() . '<br>';
    echo '</pre>';
} catch (\Zotapay\Exception\ApiCallbackException $e) {
    echo $e->getMessage();
} catch (\Zotapay\Exception\InvalidSignatureException $e) {
    echo $e->getMessage();
} finally {
    unlink($filename);
}
