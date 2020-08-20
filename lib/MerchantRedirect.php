<?php

namespace Zotapay;

/**
 * Class MerchantRedirect.
 */
class MerchantRedirect
{
    /**
     * Billing Descriptor
     *
     * @var null|string
     */
    protected $billingDescriptor;

    /**
     * Error Message
     *
     * @var null|string
     */
    protected $errorMessage;

    /**
     * Merchant OrderID
     *
     * @var null|string
     */
    protected $merchantOrderID;

    /**
     * Order ID
     *
     * @var null|string
     */
    protected $orderID;

    /**
     * Signature
     *
     * @var null|string
     */
    protected $signature;

    /**
     * Status
     *
     * @var null|string
     */
    protected $status;


    public function __construct()
    {
        Zotapay::getLogger()->info('Merchant redirect request received.');

        // logging debug
        Zotapay::getLogger()->debug('Merchant redirect request signature verification.');

        // signature verification
        if (false === $this->signatureVerify()) {
            $message = 'Merchant Redirect Error: Invalid Signature';
            $this->errorMessage = $message;

            Zotapay::getLogger()->error('Merchant redirect request error: {error}', ['error' => $message]);

            header("HTTP/1.1 401 Unauthorized");

            throw new \Zotapay\Exception\InvalidSignatureException($message);
        }

        // set properties
        $this->billingDescriptor = isset($_GET['billingDescriptor']) ? $_GET['billingDescriptor'] : null;
        $this->errorMessage = isset($_GET['errorMessage']) ? $_GET['errorMessage'] : null;
        $this->merchantOrderID = isset($_GET['merchantOrderID']) ? $_GET['merchantOrderID'] : null;
        $this->orderID = isset($_GET['orderID']) ? $_GET['orderID'] : null;
        $this->signature = isset($_GET['signature']) ? $_GET['signature'] : null;
        $this->status = isset($_GET['status']) ? $_GET['status'] : null;
        Zotapay::getLogger()->info('Merchant redirect request merchantOrderID #{merchantOrderID} data set.', ['merchantOrderID' => $this->getMerchantOrderID()]);
    }


    /**
     * @return bool
     */
    private function signatureVerify()
    {
        if (!isset($_GET['signature'])) {
            return false;
        }

        $verify = array();

        $verify['status'] = isset($_GET['status']) ? $_GET['status'] : '';
        $verify['orderID'] = isset($_GET['orderID']) ? $_GET['orderID'] : '';
        $verify['merchantOrderID'] = isset($_GET['merchantOrderID']) ? $_GET['merchantOrderID'] : '';
        $verify['merchantSecretKey'] = \Zotapay\Zotapay::getMerchantSecretKey();

        $signature = hash('sha256', \implode('', $verify));

        return $signature === $_GET['signature'];
    }


    /**
     * Get the value of Billing Descriptor
     *
     * @return null|string
     */
    public function getBillingDescriptor()
    {
        return $this->billingDescriptor;
    }


    /**
     * Get the value of Error Message
     *
     * @return null|string
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }


    /**
     * Get the value of Merchant OrderID
     *
     * @return null|string
     */
    public function getMerchantOrderID()
    {
        return $this->merchantOrderID;
    }


    /**
     * Get the value of Order ID
     *
     * @return null|string
     */
    public function getOrderID()
    {
        return $this->orderID;
    }


    /**
     * Get the value of Signature
     *
     * @return null|string
     */
    public function getSignature()
    {
        return $this->signature;
    }


    /**
     * Get the value of Status
     *
     * @return null|string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
