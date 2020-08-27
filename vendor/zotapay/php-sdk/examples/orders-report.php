<?php

require 'config.php';

/**
 * Orders Report ----------------------------------------
 */

// setup data
$data = new \Zotapay\OrdersReportData();

$data->setDateType('created');
$data->setEndpointIds('503364,503365');
$data->setFromDate('2020-06-01');
$data->setStatuses('APPROVED,DECLINED');
$data->setToDate('2020-06-30');
$data->setTypes('SALE,PAYOUT');

// request
$operation = new \Zotapay\OrdersReport();
$response = $operation->request($data);

// result
echo '<pre>';
echo 'code: ' . $response->getCode() . '<br>';
echo 'message: ' . $response->getMessage() . '<br>';
echo 'data: ' . $response->getData() . '<br>';
echo 'httpCode: ' . $response->getHttpCode() . '<br>';
echo '</pre>';
