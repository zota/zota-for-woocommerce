<?php

$sdkAbsPath = dirname(__FILE__, 2);

require $sdkAbsPath . '/vendor/autoload.php';

// tests
require $sdkAbsPath . '/tests/TestHelper.php';

// lib
require $sdkAbsPath . '/lib/AbstractApiClient.php';
require $sdkAbsPath . '/lib/AbstractData.php';
require $sdkAbsPath . '/lib/AbstractOrder.php';
