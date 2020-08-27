<?php

namespace Zotapay;

/**
 * Class DepositCCApiResponse.
 */
class DepositCCApiResponse extends ApiResponse
{

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
}
