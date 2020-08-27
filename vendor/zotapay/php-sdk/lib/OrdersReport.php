<?php

namespace Zotapay;

/**
 * Class OrdersReport.
 */
class OrdersReport extends AbstractApiClient
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
     * @param \Zotapay\OrdersReportData $data
     *
     * @return \Zotapay\OrdersReportApiResponse
     */
    public function request($data)
    {
        // return directly mock response if available.
        $mockResponse = $this->getMockResponse();
        if (!empty($mockResponse)) {
            Zotapay::getLogger()->debug('Using mocked response for orders report request.', []);
            $response = new \Zotapay\OrdersReportApiResponse($mockResponse);
            return $response;
        }
        // setup url
        $url =  \Zotapay\Zotapay::getApiUrl() . '/query/orders-report/csv/';

        // setup data
        Zotapay::getLogger()->debug('Orders Report prepare post data.');
        $prepared = $this->prepare($data);
        $signed = $this->sign($prepared);

        // make the request
        Zotapay::getLogger()->info('Orders Report request.');
        $request = $this->apiRequest->request('get', $url, $signed);

        // set the response
        Zotapay::getLogger()->debug('Orders Report response.');
        $response = new \Zotapay\OrdersReportApiResponse($request);

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
            'merchantID'    => \Zotapay\Zotapay::getMerchantId(),
            'dateType'      => $data->getDateType(),
            'endpointIds'   => $data->getEndpointIds(),
            'fromDate'      => $data->getFromDate(),
            'requestID'     => \Zotapay\Helper\Helper::generateUuid(),
            'statuses'      => $data->getStatuses(),
            'timestamp'     => $this->timestamp,
            'toDate'        => $data->getToDate(),
            'types'         => $data->getTypes(),
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
            \Zotapay\Zotapay::getMerchantId(),
            $data['dateType'],
            $data['endpointIds'],
            $data['fromDate'],
            $data['requestID'],
            $data['statuses'],
            $data['timestamp'],
            $data['toDate'],
            $data['types'],
            \Zotapay\Zotapay::getMerchantSecretKey(),
        ];

        $stringToSign = implode($dataToSign);

        $data['signature'] = hash('sha256', $stringToSign);

        return $data;
    }
}
