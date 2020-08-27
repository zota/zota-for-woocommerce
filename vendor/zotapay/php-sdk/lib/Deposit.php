<?php

namespace Zotapay;

/**
 * Class Deposit.
 */
class Deposit extends AbstractApiClient
{

    /**
     * Make a deposit request to Zotapay API.
     *
     * @param \Zotapay\DepositOrder $order
     *
     * @return \Zotapay\DepositApiResponse
     */
    public function request($order)
    {
        // return directly mock response if available.
        $mockResponse = $this->getMockResponse();
        if (!empty($mockResponse)) {
            Zotapay::getLogger()->debug('Using mocked response for deposit request.', []);
            $response = new \Zotapay\DepositApiResponse($mockResponse);
            return $response;
        }

        // setup url
        $url =  \Zotapay\Zotapay::getApiUrl() .
                '/deposit/request/' .
                \Zotapay\Zotapay::getEndpoint();

        // setup data
        Zotapay::getLogger()->debug('merchantOrderID #{merchantOrderID} Deposit prepare post data.', ['merchantOrderID' => $order->getMerchantOrderID()]);
        $data = $this->prepare($order);
        $signed = $this->sign($data);

        // make the request
        Zotapay::getLogger()->info('Deposit request.');
        $request = $this->apiRequest->request('post', $url, $signed);

        // set the response
        Zotapay::getLogger()->debug('merchantOrderID #{merchantOrderID} Deposit response.', ['merchantOrderID' => $order->getMerchantOrderID()]);
        $response = new \Zotapay\DepositApiResponse($request);

        return $response;
    }


    /**
     * @param \Zotapay\DepositOrder $order
     *
     * @return array
     */
    private function prepare($order)
    {
        return [
            'merchantOrderID'   => $order->getMerchantOrderID(),
            'merchantOrderDesc' => $order->getMerchantOrderDesc(),
            'orderAmount'       => $order->getOrderAmount(),
            'orderCurrency'     => $order->getOrderCurrency(),
            'customerEmail'     => $order->getCustomerEmail(),
            'customerFirstName' => $order->getCustomerFirstName(),
            'customerLastName'  => $order->getCustomerLastName(),
            'customerAddress'   => $order->getCustomerAddress(),
            'customerCountryCode' => $order->getCustomerCountryCode(),
            'customerCity'      => $order->getCustomerCity(),
            'customerState'     => $order->getCustomerState(),
            'customerZipCode'   => $order->getCustomerZipCode(),
            'customerPhone'     => $order->getCustomerPhone(),
            'customerIP'        => $order->getCustomerIP(),
            'customerBankCode'  => $order->getCustomerBankCode(),
            'redirectUrl'       => $order->getRedirectUrl(),
            'callbackUrl'       => $order->getCallbackUrl(),
            'checkoutUrl'       => $order->getCheckoutUrl(),
            'customParam'       => $order->getCustomParam(),
            'language'          => $order->getLanguage(),
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
            \Zotapay\Zotapay::getEndpoint(),
            $data['merchantOrderID'],
            $data['orderAmount'],
            $data['customerEmail'],
            \Zotapay\Zotapay::getMerchantSecretKey(),
        ];

        $stringToSign = implode($dataToSign);

        $data['signature'] = hash('sha256', $stringToSign);

        return $data;
    }
}
