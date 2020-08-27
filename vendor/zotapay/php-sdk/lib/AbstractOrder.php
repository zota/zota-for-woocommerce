<?php

namespace Zotapay;

/**
 * Class ZotapayOrder.
 */
abstract class AbstractOrder extends AbstractData
{
    /**
     * merchantOrderID
     * @var string
     */
    protected $merchantOrderID;

    /**
     * merchantOrderDesc
     * @var string
     */
    protected $merchantOrderDesc;

    /**
     * orderAmount
     * @var float
     */
    protected $orderAmount;

    /**
     * orderCurrency
     * @var string
     */
    protected $orderCurrency;

    /**
     * customerEmail
     * @var string
     */
    protected $customerEmail;

    /**
     * customerFirstName
     * @var string
     */
    protected $customerFirstName;

    /**
     * customerLastName
     * @var string
     */
    protected $customerLastName;

    /**
     * customerPhone
     * @var string
     */
    protected $customerPhone;

    /**
     * customerBankCode
     * @var string
     */
    protected $customerBankCode;

    /**
     * customerIP
     * @var string
     */
    protected $customerIP;

    /**
     * callbackUrl
     * @var string
     */
    protected $callbackUrl;

    /**
     * customParam
     * @var string
     */
    protected $customParam;

    /**
     * language
     * @var string
     */
    protected $language;


    public function __construct($data = null)
    {
        if (empty($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $setter = 'set' . \ucwords($key);

            if (\method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
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


    /**
     * Get the value of merchantOrderDesc
     *
     * @return string
     */
    public function getMerchantOrderDesc()
    {
        return $this->merchantOrderDesc;
    }


    /**
     * Set the value of merchantOrderDesc
     *
     * @param string $merchantOrderDesc
     *
     * @return self
     */
    public function setMerchantOrderDesc($merchantOrderDesc)
    {
        $this->merchantOrderDesc = $merchantOrderDesc;

        return $this;
    }


    /**
     * Get the value of orderAmount
     *
     * @return float
     */
    public function getOrderAmount()
    {
        return $this->orderAmount;
    }


    /**
     * Set the value of orderAmount
     *
     * @param float $orderAmount
     *
     * @return self
     */
    public function setOrderAmount($orderAmount)
    {
        $this->orderAmount = $orderAmount;

        return $this;
    }


    /**
     * Get the value of orderCurrency
     *
     * @return string
     */
    public function getOrderCurrency()
    {
        return $this->orderCurrency;
    }


    /**
     * Set the value of orderCurrency
     *
     * @param string $orderCurrency
     *
     * @return self
     */
    public function setOrderCurrency($orderCurrency)
    {
        $this->orderCurrency = $orderCurrency;

        return $this;
    }


    /**
     * Get the value of customerEmail
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }


    /**
     * Set the value of customerEmail
     *
     * @param string $customerEmail
     *
     * @return self
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customerEmail = $customerEmail;

        return $this;
    }


    /**
     * Get the value of customerFirstName
     *
     * @return string
     */
    public function getCustomerFirstName()
    {
        return $this->customerFirstName;
    }


    /**
     * Set the value of customerFirstName
     *
     * @param string $customerFirstName
     *
     * @return self
     */
    public function setCustomerFirstName($customerFirstName)
    {
        $this->customerFirstName = $customerFirstName;

        return $this;
    }


    /**
     * Get the value of customerLastName
     *
     * @return string
     */
    public function getCustomerLastName()
    {
        return $this->customerLastName;
    }


    /**
     * Set the value of customerLastName
     *
     * @param string $customerLastName
     *
     * @return self
     */
    public function setCustomerLastName($customerLastName)
    {
        $this->customerLastName = $customerLastName;

        return $this;
    }


    /**
     * Get the value of customerPhone
     *
     * @return string
     */
    public function getCustomerPhone()
    {
        return $this->customerPhone;
    }


    /**
     * Set the value of customerPhone
     *
     * @param string $customerPhone
     *
     * @return self
     */
    public function setCustomerPhone($customerPhone)
    {
        $this->customerPhone = $customerPhone;

        return $this;
    }


    /**
     * Get the value of customerBankCode
     *
     * @return string
     */
    public function getCustomerBankCode()
    {
        return $this->customerBankCode;
    }


    /**
     * Set the value of customerBankCode
     *
     * @param string $customerBankCode
     *
     * @return self
     */
    public function setCustomerBankCode($customerBankCode)
    {
        $this->customerBankCode = $customerBankCode;

        return $this;
    }


    /**
     * Get the value of customerIP
     *
     * @return string
     */
    public function getCustomerIP()
    {
        return $this->customerIP;
    }


    /**
     * Set the value of customerIP
     *
     * @param string $customerIP
     *
     * @return self
     */
    public function setCustomerIP($customerIP)
    {
        $this->customerIP = $customerIP;

        return $this;
    }


    /**
     * Get the value of callbackUrl
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }


    /**
     * Set the value of callbackUrl
     *
     * @param string $callbackUrl
     *
     * @return self
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }


    /**
     * Get the value of customParam
     *
     * @return string
     */
    public function getCustomParam()
    {
        return $this->customParam;
    }


    /**
     * Set the value of customParam
     *
     * @param string $customParam
     *
     * @return self
     */
    public function setCustomParam($customParam)
    {
        $this->customParam = $customParam;

        return $this;
    }


    /**
     * Get the value of language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }


    /**
     * Set the value of language
     *
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }
}
