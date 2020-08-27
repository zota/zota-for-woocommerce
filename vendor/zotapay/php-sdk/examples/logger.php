<?php

use Zotapay\Zotapay;

// error reporting
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);

require 'autoload.php';

/**
 * Logger ----------------------------------------
 */
$logDestination = dirname(__FILE__) . '/test.log';
\Zotapay\Zotapay::setLogDestination($logDestination);

$message = 'Test {test}';
$context = [ 'test' => '1' ];

\Zotapay\Zotapay::getLogger()->critical($message, $context);
