<?php

namespace Zotapay;

/**
 * Class OrderStatus.
 */
class OrderStatus extends AbstractApiClient
{
    /**
     * request timestamp
     *
     * @var null|string
     */
    protected $timestamp;


    /**
     * Class constructor.
     *
     * @param \Zotapay\Data $data
     */
    public function __construct($data = null)
    {
        parent::__construct();

        $this->timestamp = \time();
    }


    /**
     * Make an order status request to Zotapay API.
     *
     * @param \Zotapay\OrderStatusData $data
     *
     * @return \Zotapay\OrderStatusApiResponse
     */
    public function request($data)
    {
        // return directly mock response if available.
        $mockResponse = $this->getMockResponse();
        if (!empty($mockResponse)) {
            Zotapay::getLogger()->debug('Using mocked response for order status request.', []);
            $response = new \Zotapay\OrderStatusApiResponse($mockResponse);
            return $response;
        }
        // setup url
        $url =  \Zotapay\Zotapay::getApiUrl() . '/query/order-status/';

        // setup data
        Zotapay::getLogger()->debug('merchantOrderID #{merchantOrderID} Order Status prepare post data.', ['merchantOrderID' => $data->getMerchantOrderID()]);
        $prepared = $this->prepare($data);
        $signed = $this->sign($prepared);

        // make the request
        Zotapay::getLogger()->info('Order Status request.');
        $request = $this->apiRequest->request('get', $url, $signed);

        // set the response
        Zotapay::getLogger()->debug('merchantOrderID #{merchantOrderID} Order Status response.', ['merchantOrderID' => $data->getMerchantOrderID()]);
        $response = new \Zotapay\OrderStatusApiResponse($request);

        return $response;
    }


    /**
     * @param \Zotapay\OrderStatusData $data
     *
     * @return array
     */
    private function prepare($data)
    {
        return [
            'merchantID'        => \Zotapay\Zotapay::getMerchantId(),
            'merchantOrderID'   => $data->getMerchantOrderID(),
            'orderID'           => $data->getOrderID(),
            'timestamp'         => $this->timestamp,
        ];
    }


    /**
     * @param array $data
     *
     * @return array
     */
    private function sign($data)
    {
        $dataToSign = [
            $data['merchantID'],
            $data['merchantOrderID'],
            $data['orderID'],
            $data['timestamp'],
            \Zotapay\Zotapay::getMerchantSecretKey(),
        ];

        $stringToSign = implode($dataToSign);

        $data['signature'] = hash('sha256', $stringToSign);

        return $data;
    }
}
