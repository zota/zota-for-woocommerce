<?php

namespace Zotapay;

/**
 * @internal
 */
final class OrdersReportTest extends \PHPUnit\Framework\TestCase
{
    use \Zotapay\TestHelper;

    /**
     * Data Array
     * @return array
     */
    public function getData()
    {

        return [
            [
                // data
                [
                    'merchantID'    => \Zotapay\Zotapay::getMerchantId(),
                    'dateType'      => 'created',
                    'endpointIds'   => '503364,503365',
                    'fromDate'      => '2020-06-01',
                    'statuses'      => 'APPROVED,DECLINED',
                    'toDate'        => '2020-06-30',
                    'types'         => 'SALE,PAYOUT',
                ],

                // ref
                [
                    'code' => 200,
                    'message' => null,
                    'data' =>
                        'id,parent_id,order_type,status,merchant_id,batch_id,endpoint_id,endpoint_group_id,order_currency,order_amount,original_amount,amount_changed,merchant_order_id,payment_method_id,external_transaction_id,client_error_message,status_changed,created_at,ended_at,ended_with_status,last_update_at,is_refunded,is_fully_refunded,refunded_amount,refunded_at,request_customer_ip,entered_deposit_url,entered_bank_selection_page,bank_selected,selected_bank_code,selected_bank_name,callback_sent_to_merchant,callback_received_by_merchant,customer_email,customer_first_name,customer_last_name,customer_address,customer_country_code,customer_city,customer_state,customer_zip_code,customer_phone,customer_bank_code,customer_bank_account_number,customer_bank_account_name,customer_bank_branch,customer_bank_address,customer_bank_zip_code,customer_bank_routing_number,customer_bank_province,customer_bank_area
                        24036374,,SALE,APPROVED,SDKEXPLORER,,503365,,EUR,1.00000000,1.00000000,false,91,VISA,cce01bf1-8f72-4ebf-b32f-45966141851f,,false,2020-06-04 11:25:46 +0000 UTC,2020-06-04 11:26:11 +0000 UTC,APPROVED,2020-06-04 11:26:11 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036377,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,1.00000000,1.00000000,false,92,VISA,5064982c-9276-4c69-a902-ea39549442e8,,false,2020-06-04 11:45:53 +0000 UTC,2020-06-04 11:46:32 +0000 UTC,APPROVED,2020-06-04 11:46:32 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036383,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,1.00000000,1.00000000,false,93,VISA,5d79d4a9-5e98-426d-a801-edb1fbe1e2dc,,false,2020-06-04 11:51:44 +0000 UTC,2020-06-04 11:52:08 +0000 UTC,APPROVED,2020-06-04 11:52:08 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036391,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,1.00000000,1.00000000,false,94,VISA,38b50d28-8159-4925-9406-8abdc955dfd1,,false,2020-06-04 11:58:39 +0000 UTC,2020-06-04 11:59:05 +0000 UTC,APPROVED,2020-06-04 11:59:05 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036393,,SALE,APPROVED,SDKEXPLORER,,503365,,EUR,1.00000000,1.00000000,false,95,CREDITCARD,2a04886e-6a3a-4f92-bab8-c51e20b1a05b,,false,2020-06-04 12:01:01 +0000 UTC,2020-06-04 12:03:25 +0000 UTC,APPROVED,2020-06-04 12:03:25 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036433,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,96,VISA,453d543d-ed9d-4cd8-9cfe-ff7eab37f107,,false,2020-06-04 13:22:57 +0000 UTC,2020-06-04 13:23:20 +0000 UTC,APPROVED,2020-06-04 13:23:20 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036435,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,97,VISA,54f87a17-b00d-4ab3-9608-78a086bfb075,,false,2020-06-04 13:24:27 +0000 UTC,2020-06-04 13:24:29 +0000 UTC,APPROVED,2020-06-04 13:24:29 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,,true,true,testing@api-requests.com,TEST,USER,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036436,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,98,CREDITCARD,9b376b06-5d9e-4f94-9788-df44685edb74,,false,2020-06-04 13:25:32 +0000 UTC,2020-06-04 13:26:03 +0000 UTC,APPROVED,2020-06-04 13:26:03 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24036439,,PAYOUT,DECLINED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,102,PAYOUT,,insufficient funds,false,2020-06-04 13:28:54 +0000 UTC,2020-06-04 13:28:54 +0000 UTC,DECLINED,2020-06-04 13:28:54 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,Bangkok Bank,true,true,testing@api-requests.com,John,Lock,,,,,,1-420-100-1000,BBL,1234567890,John Doe,Bank Branch,"Thong Nai Pan Noi Beach, Baan Tai, Koh Phangan",84280,000,Bank Province,Bank Area / City
                        24039416,,SALE,DECLINED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,15463,CREDITCARD,2f7d1284-7484-4823-b72a-85ceffb7d037,declined,false,2020-06-24 14:54:28 +0000 UTC,2020-06-24 14:54:50 +0000 UTC,DECLINED,2020-06-24 14:54:50 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039419,,PAYOUT,DECLINED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,300,PAYOUT,,insufficient funds,false,2020-06-24 15:14:23 +0000 UTC,2020-06-24 15:14:23 +0000 UTC,DECLINED,2020-06-24 15:14:23 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,Bangkok Bank,true,false,testing@zotapay-php-sdk.com,John,Lock,,,,,,1-420-100-1000,BBL,1234567890,John Doe,Bank Branch,"Thong Nai Pan Noi Beach, Baan Tai, Koh Phangan",84280,000,Bank Province,Bank Area / City
                        24039581,,SALE,DECLINED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,132453,CREDITCARD,7f03bf3e-74f3-49b8-a19a-6dc478d96c82,declined,false,2020-06-25 11:13:17 +0000 UTC,2020-06-25 11:13:31 +0000 UTC,DECLINED,2020-06-25 11:13:31 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039584,,SALE,DECLINED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,1454645dfgg,CREDITCARD,5a583e43-fdae-44e8-b361-7d24d4d92847,declined,false,2020-06-25 11:37:37 +0000 UTC,2020-06-25 11:37:55 +0000 UTC,DECLINED,2020-06-25 11:37:55 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039721,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,120,VISA,19a0ba81-b0ac-4117-ac40-f3dfcb5a85e8,,false,2020-06-26 10:03:19 +0000 UTC,2020-06-26 10:03:21 +0000 UTC,APPROVED,2020-06-26 10:03:21 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,,true,true,testing@api-requests.com,TEST,USER,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039722,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,121,VISA,c90cd65a-a303-4609-a08f-c87b9ceed225,,false,2020-06-26 10:05:16 +0000 UTC,2020-06-26 10:05:17 +0000 UTC,APPROVED,2020-06-26 10:05:17 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,,true,true,testing@api-requests.com,TEST,USER,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039728,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,123,CREDITCARD,a2a96e68-7f66-4e6b-964f-9d1305f3b1ae,,false,2020-06-26 10:16:44 +0000 UTC,2020-06-26 10:18:48 +0000 UTC,APPROVED,2020-06-26 10:18:48 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039729,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,124,VISA,fee9ad5b-93ce-41b7-a1d2-2c882085cce9,,false,2020-06-26 10:24:25 +0000 UTC,2020-06-26 10:24:55 +0000 UTC,APPROVED,2020-06-26 10:24:55 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039730,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,125,VISA,c548393a-f63f-47f0-a5a3-7154fd6e0039,,false,2020-06-26 10:27:34 +0000 UTC,2020-06-26 10:30:42 +0000 UTC,APPROVED,2020-06-26 10:30:42 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039731,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,126,VISA,b8902134-b22f-42dc-9a03-20aaf5e4d851,,false,2020-06-26 10:35:50 +0000 UTC,2020-06-26 10:36:10 +0000 UTC,APPROVED,2020-06-26 10:36:10 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039732,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,127,VISA,c78b1e49-3655-42b3-a166-d343fd8e29e4,,false,2020-06-26 10:39:24 +0000 UTC,2020-06-26 10:40:01 +0000 UTC,APPROVED,2020-06-26 10:40:01 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039734,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,128,CREDITCARD,057bb6b9-f3a0-42cd-9409-41d00c3e237e,,false,2020-06-26 10:41:59 +0000 UTC,2020-06-26 10:42:54 +0000 UTC,APPROVED,2020-06-26 10:42:54 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,true,false,false,,,true,true,testing@api-requests.com,John,Lock,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039738,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,129,VISA,e251417e-e5fb-4eeb-a708-7ccb3ac2d0d8,,false,2020-06-26 10:44:58 +0000 UTC,2020-06-26 10:44:59 +0000 UTC,APPROVED,2020-06-26 10:44:59 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,,true,true,testing@api-requests.com,TEST,USER,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039739,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,130,VISA,8a1be599-5413-4c48-a635-958a7394c01e,,false,2020-06-26 10:46:48 +0000 UTC,2020-06-26 10:46:49 +0000 UTC,APPROVED,2020-06-26 10:46:49 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,,true,true,testing@api-requests.com,TEST,USER,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        24039740,,SALE,APPROVED,SDKEXPLORER,,503364,,USD,100.00000000,100.00000000,false,131,VISA,87c2f2b9-bd50-4dfc-8b82-beda3fda2668,,false,2020-06-26 10:50:01 +0000 UTC,2020-06-26 10:50:03 +0000 UTC,APPROVED,2020-06-26 10:50:03 +0000 UTC,false,false,0.00000000,0001-01-01 00:00:00 +0000 UTC,134.201.250.130,false,false,false,,,true,true,testing@api-requests.com,TEST,USER,"The Swan, Jungle St. 108",US,Los Angeles,CA,90015,1-420-100-1000,,,,,,,,,
                        ',
                    'httpCode' => 200,
                    'request_signature' => '56caa9be04b627e92e2dc8c12e33699d06ec7c970733b4d778011abec9b96e40',
                ]
            ],
        ];
    }

    /**
     * Order Status Request
     *
     * @dataProvider getData
     */
    public function testRequest($data, $ref)
    {
        $ordersReportData = new \Zotapay\OrdersReportData($data);
        $ordersReport = new \Zotapay\OrdersReport();
        if (!empty($this->apiClientStub)) {
            $ordersReport->setApiRequest($this->apiClientStub);
        }
        $response = $ordersReport->request($ordersReportData);

        static::assertNotFalse($response);
        static::assertInstanceOf(\Zotapay\OrdersReportApiResponse::class, $response);

        static::assertSame($ref['code'], $response->getCode());
        static::assertSame($ref['message'], $response->getMessage());
        static::assertSame($ref['httpCode'], $response->getHttpCode());
    }

    /**
     * Request prepare
     *
     * @dataProvider getData
     */
    public function testPrepare($data, $ref)
    {
        $ordersReportData = new \Zotapay\OrdersReportData($data);
        $ordersReport = new \Zotapay\OrdersReport();

        $reflection = new \ReflectionClass(get_class($ordersReport));
        $method = $reflection->getMethod('prepare');
        $method->setAccessible(true);
        $prepare = $method->invokeArgs($ordersReport, array($ordersReportData));

        $this->assertArrayHasKey('merchantID', $prepare);
        $this->assertArrayHasKey('dateType', $prepare);
        $this->assertArrayHasKey('endpointIds', $prepare);
        $this->assertArrayHasKey('fromDate', $prepare);
        $this->assertArrayHasKey('requestID', $prepare);
        $this->assertArrayHasKey('statuses', $prepare);
        $this->assertArrayHasKey('timestamp', $prepare);
        $this->assertArrayHasKey('toDate', $prepare);
        $this->assertArrayHasKey('types', $prepare);
    }

    /**
     * Request signing
     *
     * @dataProvider getData
     */
    public function testSign($data, $ref)
    {
        if (!empty(getenv('API_INTEGRATION_TESTS'))) {
            $this->markTestSkipped('Not aplicable in integration tests.');
        }

        // Set static timestamp so that we can test time-based code.
        $timestamp  = '1594223794';
        $uuid       = '3fc03cc6-8d65-4345-a7be-503f2797136a';

        $data['timestamp'] = $timestamp;
        $data['requestID'] = $uuid;

        $ordersReport = new \Zotapay\OrdersReport();

        $reflection = new \ReflectionClass(get_class($ordersReport));
        $method = $reflection->getMethod('sign');
        $method->setAccessible(true);
        $signed = $method->invokeArgs($ordersReport, array($data));

        $this->assertEquals($ref['request_signature'], $signed['signature']);
    }

    /**
     * Test mocking respons.
     *
     * @dataProvider getMockData
     */
    public function testMockResponse($mockResponse, $ref)
    {
        \Zotapay\Zotapay::setMockResponse($mockResponse);

        $ordersReportData = new \Zotapay\OrdersReportData();
        $ordersReport = new \Zotapay\OrdersReport();

        $response = $ordersReport->request($ordersReportData);

        static::assertInstanceOf(\Zotapay\OrdersReportApiResponse::class, $response);

        static::assertSame($ref['code'], $response->getCode());
        static::assertSame($ref['httpCode'], $response->getHttpCode());
        static::assertSame($ref['message'], $response->getMessage());

        static::assertNull($ordersReport->getMockResponse());
    }
}
