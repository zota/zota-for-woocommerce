<?php

// error reporting
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require 'autoload.php';

// Endpoints URL
define('ZOTAPAY_EXAMPLES_URL', 'http://zotapay.local/php-sdk/examples');

// Settings
\Zotapay\Zotapay::setMerchantId(getenv('API_MERCHANT_ID'));
\Zotapay\Zotapay::setMerchantSecretKey(getenv('API_MERCHANT_SECRET_KEY'));
\Zotapay\Zotapay::setEndpoint('503364');  // USD Sandbox environment
\Zotapay\Zotapay::setApiBase('https://api.zotapay-sandbox.com'); // Sandbox environment

// Logging
\Zotapay\Zotapay::setLogThreshold('debug');
\Zotapay\Zotapay::setLogDestination(dirname(__FILE__) . '/test.log');
