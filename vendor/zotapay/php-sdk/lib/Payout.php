<?php

namespace Zotapay;

/**
 * Class Payout.
 */
class Payout extends AbstractApiClient
{
    /**
     * Make a payout request to Zotapay API.
     *
     * @param PayoutOrder $order
     *
     * @return \Zotapay\PayoutApiResponse
     */
    public function request($order)
    {
        // return directly mock response if available.
        $mockResponse = $this->getMockResponse();
        if (!empty($mockResponse)) {
            Zotapay::getLogger()->debug('Using mocked response for payout request.', []);
            $response = new \Zotapay\PayoutApiResponse($mockResponse);
            return $response;
        }

        // setup url
        $url =  \Zotapay\Zotapay::getApiUrl() .
                '/payout/request/' .
                \Zotapay\Zotapay::getEndpoint();

        // setup data
        Zotapay::getLogger()->debug('merchantOrderID #{merchantOrderID} Payout prepare post data.', ['merchantOrderID' => $order->getMerchantOrderID()]);
        $data = $this->prepare($order);
        $signed = $this->sign($data);

        // make the request
        Zotapay::getLogger()->info('Payout request.');
        $request = $this->apiRequest->request('post', $url, $signed);

        // set the response
        Zotapay::getLogger()->debug('merchantOrderID #{merchantOrderID} Payout response.', ['merchantOrderID' => $order->getMerchantOrderID()]);
        $response = new \Zotapay\PayoutApiResponse($request);

        return $response;
    }


     /**
      * @param PayoutOrder $order
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
           'customerPhone'     => $order->getCustomerPhone(),
           'customerIP'        => $order->getCustomerIP(),
           'customerBankCode'  => $order->getCustomerBankCode(),
           'customerBankAccountNumber'  => $order->getCustomerBankAccountNumber(),
           'customerBankAccountName'  => $order->getCustomerBankAccountName(),
           'customerBankBranch'  => $order->getCustomerBankBranch(),
           'customerBankAddress'  => $order->getCustomerBankAddress(),
           'customerBankZipCode'  => $order->getCustomerBankZipCode(),
           'customerBankRoutingNumber'  => $order->getCustomerBankRoutingNumber(),
           'customerBankProvince'  => $order->getCustomerBankProvince(),
           'customerBankArea'  => $order->getCustomerBankArea(),
           'callbackUrl'       => $order->getCallbackUrl(),
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
            $data['customerBankAccountNumber'],
            \Zotapay\Zotapay::getMerchantSecretKey(),
        ];

        $stringToSign = implode($dataToSign);

        $data['signature'] = hash('sha256', $stringToSign);

        return $data;
    }
}
