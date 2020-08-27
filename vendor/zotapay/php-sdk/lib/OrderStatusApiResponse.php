<?php

namespace Zotapay;

/**
 * Class OrderStatusApiResponse.
 */
class OrderStatusApiResponse extends ApiResponse
{
    /**
     * Get the value of type field from reponse data.
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->data['type'] ?? null;
    }


    /**
     * Get the value of status field from reponse data.
     *
     * @return null|string
     */
    public function getStatus()
    {
        return $this->data['status'] ?? null;
    }


    /**
     * Get the value of errorMessage field from reponse data.
     *
     * @return null|string
     */
    public function getErrorMessage()
    {
        return $this->data['errorMessage'] ?? null;
    }


    /**
     * Get the value of errorMessage field from reponse data.
     *
     * @return null|string
     */
    public function getEndpointID()
    {
        return $this->data['endpointID'] ?? null;
    }


    /**
     * Get the value of processorTransactionID field from reponse data.
     *
     * @return null|string
     */
    public function getProcessorTransactionID()
    {
        return $this->data['processorTransactionID'] ?? null;
    }


    /**
     * Get the value of merchantOrderID field from reponse data.
     *
     * @return null|string
     */
    public function getMerchantOrderID()
    {
        return $this->data['merchantOrderID'] ?? null;
    }


    /**
     * Get the value of orderID field from reponse data.
     *
     * @return null|string
     */
    public function getOrderID()
    {
        return $this->data['orderID'] ?? null;
    }


    /**
     * Get the value of amount field from reponse data.
     *
     * @return null|string
     */
    public function getAmount()
    {
        return $this->data['amount'] ?? null;
    }


    /**
     * Get the value of currency field from reponse data.
     *
     * @return null|string
     */
    public function getCurrency()
    {
        return $this->data['currency'] ?? null;
    }


    /**
     * Get the value of customerEmail field from reponse data.
     *
     * @return null|string
     */
    public function getCustomerEmail()
    {
        return $this->data['customerEmail'] ?? null;
    }


    /**
     * Get the value of customParam field from reponse data.
     *
     * @return null|string
     */
    public function getCustomParam()
    {
        return $this->data['customParam'] ?? null;
    }


    /**
     * Get the value of extraData field from reponse data.
     *
     * @return null|string
     */
    public function getExtraData()
    {
        return $this->data['extraData'] ?? null;
    }


    /**
     * Get the value of request merchantID field from reponse data.
     *
     * @return null|string
     */
    public function getRequestMerchantID()
    {
        return $this->data['request']['merchantID'] ?? null;
    }


    /**
     * Get the value of request merchantOrderID field from reponse data.
     *
     * @return null|string
     */
    public function getRequestMerchantOrderID()
    {
        return $this->data['request']['merchantOrderID'] ?? null;
    }


    /**
     * Get the value of request orderID field from reponse data.
     *
     * @return null|string
     */
    public function getRequestOrderID()
    {
        return $this->data['request']['orderID'] ?? null;
    }


    /**
     * Get the value of request timestamp field from reponse data.
     *
     * @return null|string
     */
    public function getRequestTimestamp()
    {
        return $this->data['request']['timestamp'] ?? null;
    }
}
