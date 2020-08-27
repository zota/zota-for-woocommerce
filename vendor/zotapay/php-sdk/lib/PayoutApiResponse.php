<?php

namespace Zotapay;

/**
 * Class PayoutApiResponse.
 */
class PayoutApiResponse extends ApiResponse
{
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
