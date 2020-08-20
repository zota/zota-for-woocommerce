<?php

namespace Zotapay;

/**
 * Class DepositApiResponse.
 */
class DepositApiResponse extends ApiResponse
{

    /**
     * Get the value of depositUrl field from reponse data.
     *
     * @return null|string
     */
    public function getDepositUrl()
    {
        return $this->data['depositUrl'] ?? null;
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
