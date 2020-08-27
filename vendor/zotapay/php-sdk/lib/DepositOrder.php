<?php

namespace Zotapay;

/**
 * Class DepositOrder.
 */
class DepositOrder extends AbstractOrder
{
    /**
     * customerAddress
     * @var string
     */
    protected $customerAddress;

    /**
     * customerCountryCode
     * @var string
     */
    protected $customerCountryCode;

    /**
     * customerCity
     * @var string
     */
    protected $customerCity;

    /**
     * customerState
     * @var string
     */
    protected $customerState;

    /**
     * customerZipCode
     * @var string
     */
    protected $customerZipCode;

    /**
     * redirectUrl
     * @var string
     */
    protected $redirectUrl;

    /**
     * checkoutUrl
     * @var string
     */
    protected $checkoutUrl;

    /**
     * cardHolderName
     * @var string
     */
    protected $cardHolderName;

    /**
     * cardNumber
     * @var string
     */
    protected $cardNumber;

    /**
     * cardExpirationMonth
     * @var string
     */
    protected $cardExpirationMonth;

    /**
     * cardExpirationYear
     * @var string
     */
    protected $cardExpirationYear;

    /**
     * cardCvv
     * @var string
     */
    protected $cardCvv;


    /**
     * Get the value of customerAddress
     *
     * @return string
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }


    /**
     * Set the value of customerAddress
     *
     * @param string $customerAddress
     *
     * @return self
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }


    /**
     * Get the value of customerCountryCode
     *
     * @return string
     */
    public function getCustomerCountryCode()
    {
        return $this->customerCountryCode;
    }


    /**
     * Set the value of customerCountryCode
     *
     * @param string $customerCountryCode
     *
     * @return self
     */
    public function setCustomerCountryCode($customerCountryCode)
    {
        $this->customerCountryCode = $customerCountryCode;

        return $this;
    }


    /**
     * Get the value of customerCity
     *
     * @return string
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }


    /**
     * Set the value of customerCity
     *
     * @param string $customerCity
     *
     * @return self
     */
    public function setCustomerCity($customerCity)
    {
        $this->customerCity = $customerCity;

        return $this;
    }


    /**
     * Get the value of customerState
     *
     * @return string
     */
    public function getCustomerState()
    {
        return $this->customerState;
    }


    /**
     * Set the value of customerState
     *
     * @param string $customerState
     *
     * @return self
     */
    public function setCustomerState($customerState)
    {
        $this->customerState = $customerState;

        return $this;
    }


    /**
     * Get the value of customerZipCode
     *
     * @return string
     */
    public function getCustomerZipCode()
    {
        return $this->customerZipCode;
    }


    /**
     * Set the value of customerZipCode
     *
     * @param string $customerZipCode
     *
     * @return self
     */
    public function setCustomerZipCode($customerZipCode)
    {
        $this->customerZipCode = $customerZipCode;

        return $this;
    }


    /**
     * Get the value of redirectUrl
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }


    /**
     * Set the value of redirectUrl
     *
     * @param string $redirectUrl
     *
     * @return self
     */
    public function setRedirectUrl($redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }


    /**
     * Get the value of checkoutUrl
     *
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->checkoutUrl;
    }


    /**
     * Set the value of checkoutUrl
     *
     * @param string $checkoutUrl
     *
     * @return self
     */
    public function setCheckoutUrl($checkoutUrl)
    {
        $this->checkoutUrl = $checkoutUrl;

        return $this;
    }


    /**
     * Get the value of cardHolderName
     *
     * @return string
     */
    public function getCardHolderName()
    {
        return $this->cardHolderName;
    }


    /**
     * Set the value of cardHolderName
     *
     * @param string $cardHolderName
     *
     * @return self
     */
    public function setCardHolderName($cardHolderName)
    {
        $this->cardHolderName = $cardHolderName;

        return $this;
    }


    /**
     * Get the value of cardNumber
     *
     * @return string
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }


    /**
     * Set the value of cardNumber
     *
     * @param string $cardNumber
     *
     * @return self
     */
    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;

        return $this;
    }


    /**
     * Get the value of cardExpirationMonth
     *
     * @return string
     */
    public function getCardExpirationMonth()
    {
        return $this->cardExpirationMonth;
    }


    /**
     * Set the value of cardExpirationMonth
     *
     * @param string $cardExpirationMonth
     *
     * @return self
     */
    public function setCardExpirationMonth($cardExpirationMonth)
    {
        $this->cardExpirationMonth = $cardExpirationMonth;

        return $this;
    }


    /**
     * Get the value of cardExpirationYear
     *
     * @return string
     */
    public function getCardExpirationYear()
    {
        return $this->cardExpirationYear;
    }


    /**
     * Set the value of cardExpirationYear
     *
     * @param string $cardExpirationYear
     *
     * @return self
     */
    public function setCardExpirationYear($cardExpirationYear)
    {
        $this->cardExpirationYear = $cardExpirationYear;

        return $this;
    }


    /**
     * Get the value of cardCvv
     *
     * @return string
     */
    public function getCardCvv()
    {
        return $this->cardCvv;
    }


    /**
     * Set the value of cardCvv
     *
     * @param string $cardCvv
     *
     * @return self
     */
    public function setCardCvv($cardCvv)
    {
        $this->cardCvv = $cardCvv;

        return $this;
    }
}
