<?php

namespace Zotapay;

/**
 * Class OrderStatusData.
 */
class OrderStatusData extends AbstractData
{
    /**
     * orderID
     *
     * @var string
     */
    protected $orderID;

    /**
     * merchantOrderID
     *
     * @var string
     */
    protected $merchantOrderID;


    /**
     * Get the value of orderID
     *
     * @return string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }


    /**
     * Set the value of orderID
     *
     * @param string $orderID
     *
     * @return self
     */
    public function setOrderID($orderID)
    {
        $this->orderID = $orderID;

        return $this;
    }


    /**
     * Get the value of merchantOrderID
     *
     * @return string
     */
    public function getMerchantOrderID()
    {
        return $this->merchantOrderID;
    }


    /**
     * Set the value of merchantOrderID
     *
     * @param string $merchantOrderID
     *
     * @return self
     */
    public function setMerchantOrderID($merchantOrderID)
    {
        $this->merchantOrderID = $merchantOrderID;

        return $this;
    }
}
