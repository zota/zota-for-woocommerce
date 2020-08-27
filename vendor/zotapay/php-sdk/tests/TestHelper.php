<?php

namespace Zotapay;

/**
 * Helper trait.
 */
trait TestHelper
{

    /** @var null|string original merchant unique identifier */
    protected $origMerchantId;

    /** @var null|string original secret key */
    protected $origMerchantSecretKey;

    /** @var null|string original version of the Zotapay API */
    protected $origEndpoint;

    /** @var null|string original base URL */
    protected $origApiBase;

    /** @var null|\Zotapay\ApiRequest ApiRequest mock object */
    private $apiClientStub;

    /** @before */
    protected function setUpConfig()
    {
        // Save original values so that we can restore them after running tests
        $this->origMerchantId = \Zotapay\Zotapay::getMerchantId();
        $this->origMerchantSecretKey = \Zotapay\Zotapay::getMerchantSecretKey();
        $this->origEndpoint = \Zotapay\Zotapay::getEndpoint();
        $this->origApiBase = \Zotapay\Zotapay::getApiBase();

        // Set up host and credentials
        \Zotapay\Zotapay::setMerchantId('api_test_merchant_id');
        \Zotapay\Zotapay::setMerchantSecretKey('api_test_merchant_secret_key');
        \Zotapay\Zotapay::setEndpoint('api_test_endpoint');  // USD Sandbox environment
        \Zotapay\Zotapay::setApiBase('https://api.zotapay-sandbox.com'); // Sandbox environment

        if (!empty(getenv('API_INTEGRATION_TESTS'))) {
            \Zotapay\Zotapay::setMerchantId(getenv('API_MERCHANT_ID'));
            \Zotapay\Zotapay::setMerchantSecretKey(getenv('API_MERCHANT_SECRET_KEY'));
            \Zotapay\Zotapay::setEndpoint('503364');  // USD Sandbox environment
            return;
        }

        $providedData = $this->getProvidedData();

        if (empty($providedData)) {
            return;
        }

        list($input, $expected) = $providedData;

        $responseData = [
            'code'    => $expected['code'],
            'message' => $expected['message'],
            'data'    => [
                'merchantOrderID' => $expected['merchantOrderID'] ?? null,
                'orderID'         => $expected['orderID'] ?? null,
            ],
        ];
        if (!empty($expected['status'])) {
            $responseData['data']['status'] = $expected['status'];
        }
        if (!empty($expected['depositUrl'])) {
            $responseData['data']['depositUrl'] = $expected['depositUrl'];
        }
        $response = [
            \json_encode($responseData),
            $expected['code'],
        ];

        $this->apiClientStub = $this->createMock(\Zotapay\ApiRequest::class);
        $this->apiClientStub->method('request')->willReturn($response);
    }

    /**
     * Data for testing mocked responses.
     *
     * @return array
     */
    public function getMockData()
    {
        return [
            [
                // httpClientRequest
                [
                    '{"code":"200","message":"","data":{}}',
                    200
                ],

                // ref
                [
                    'httpCode' => 200,
                    'code' => 200,
                    'data' => null,
                    'message' => null,
                ]
            ],
            [
                // httpClientRequest
                [
                    '{"code":"400","message":"An error message","data":{}}',
                    400
                ],

                // ref
                [
                    'httpCode' => 400,
                    'code' => 400,
                    'data' => null,
                    'message' => 'An error message',
                ]
            ],
        ];
    }

    /** @after */
    protected function tearDownConfig()
    {
        // Restore original values
        \Zotapay\Zotapay::setMerchantId($this->origMerchantId);
        \Zotapay\Zotapay::setMerchantSecretKey($this->origMerchantSecretKey);
        \Zotapay\Zotapay::setEndpoint($this->origEndpoint);
        \Zotapay\Zotapay::setApiBase($this->origApiBase);
    }
}
