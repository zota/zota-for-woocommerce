<?php

use Zotapay\Zotapay;
use Zotapay\Deposit;
use Zotapay\DepositOrder;

// error reporting
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require 'autoload.php';

/**
 * Settings ----------------------------------------
 */
Zotapay::setMerchantId(getenv('API_MERCHANT_ID'));
Zotapay::setMerchantSecretKey(getenv('API_MERCHANT_SECRET_KEY'));
Zotapay::setEndpoint('503364');  // USD Sandbox environment
Zotapay::setApiBase('https://api.zotapay-sandbox.com'); // Sandbox environment
