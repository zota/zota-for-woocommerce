<?php

namespace Zotapay;

/**
 * Class ApiCallback.
 */
class ApiCallback
{
    /**
     * Type
     *
     * @var null|string
     */
    protected $type;

    /**
     * Status
     *
     * @var null|string
     */
    protected $status;

    /**
     * Error Message
     *
     * @var null|string
     */
    protected $errorMessage;

    /**
     * Endpoint ID
     *
     * @var null|string
     */
    protected $endpointID;

    /**
     * Processor Transaction ID
     *
     * @var null|string
     */
    protected $processorTransactionID;

    /**
     * Order ID
     *
     * @var null|string
     */
    protected $orderID;

    /**
     * Merchant OrderID
     *
     * @var null|string
     */
    protected $merchantOrderID;

    /**
     * Amount
     *
     * @var null|string
     */
    protected $amount;

    /**
     * Currency
     *
     * @var null|string
     */
    protected $currency;

    /**
     * Customer Email
     *
     * @var null|string
     */
    protected $customerEmail;

    /**
     * Custom Param
     *
     * @var null|string
     */
    protected $customParam;

    /**
     * Extra Data
     *
     * @var null|string
     */
    protected $extraData;

    /**
     * Original Request
     *
     * @var null|string
     */
    protected $originalRequest;

    /**
     * Signature
     *
     * @var null|string
     */
    protected $signature;


    public function __construct($stream = null)
    {
        Zotapay::getLogger()->info('Callback request received.');

        // param added for testing
        if (null === $stream) {
            $stream = 'php://input';
        }

        // logging debug
        Zotapay::getLogger()->debug('Callback request file get contents from stream: {stream}', ['stream' => $stream]);

        // fetch the body
        $raw = \file_get_contents($stream);
        if (false === $raw) {
            // @codeCoverageIgnoreStart
            $this->handleError('Get raw data failed.');
            // @codeCoverageIgnoreEnd
        }

        // logging debug
        Zotapay::getLogger()->debug('Callback request json decode raw data.');

        // get data
        $data = \json_decode($raw, JSON_OBJECT_AS_ARRAY);
        if (\json_last_error() !== JSON_ERROR_NONE) {
            $this->handleError(\json_last_error_msg());
        }

        // logging debug
        Zotapay::getLogger()->debug('Callback request signature verification.');

        // signature verification
        if (false === $this->signatureVerify($data)) {
            $message = 'Invalid Signature';
            $this->errorMessage = $message;

            Zotapay::getLogger()->error('Callback request error: {error}', ['error' => $message]);

            header("HTTP/1.1 401 Unauthorized");

            throw new \Zotapay\Exception\InvalidSignatureException($message);
        }

        // set data
        $this->type = isset($data['type']) ? $data['type'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->errorMessage = isset($data['errorMessage']) ? $data['errorMessage'] : null;
        $this->endpointID = isset($data['endpointID']) ? $data['endpointID'] : null;
        $this->processorTransactionID = isset($data['processorTransactionID']) ? $data['processorTransactionID'] : null;
        $this->orderID = isset($data['orderID']) ? $data['orderID'] : null;
        $this->merchantOrderID = isset($data['merchantOrderID']) ? $data['merchantOrderID'] : null;
        $this->amount = isset($data['amount']) ? $data['amount'] : null;
        $this->currency = isset($data['currency']) ? $data['currency'] : null;
        $this->customerEmail = isset($data['customerEmail']) ? $data['customerEmail'] : null;
        $this->customParam = isset($data['customParam']) ? $data['customParam'] : null;
        $this->extraData = isset($data['extraData']) ? $data['extraData'] : null;
        $this->originalRequest = isset($data['originalRequest']) ? $data['originalRequest'] : null;
        $this->signature = isset($data['signature']) ? $data['signature'] : null;

        Zotapay::getLogger()->info('Callback request merchantOrderID #{merchantOrderID} data set.', ['merchantOrderID' => $this->getMerchantOrderID()]);
    }


    /**
     * @param string $error
     *
     * @throws \Zotapay\Exception\ApiCallbackException
     */
    private function handleError($error)
    {
        $message = 'Zotapay API Callback error: ' . $error;

        $this->errorMessage = $message;

        Zotapay::getLogger()->error('Callback request error: {error}', ['error' => $error]);

        header("HTTP/1.1 400 Bad request");

        throw new \Zotapay\Exception\ApiCallbackException($message);
    }


    /**
     * @param array $data returned data from the callback
     *
     * @return bool
     */
    public function signatureVerify($data)
    {
        if (!isset($data['signature'])) {
            return false;
        }

        $verify = array();

        $verify['endpointID'] = isset($data['endpointID']) ? $data['endpointID'] : '';
        $verify['orderID'] = isset($data['orderID']) ? $data['orderID'] : '';
        $verify['merchantOrderID'] = isset($data['merchantOrderID']) ? $data['merchantOrderID'] : '';
        $verify['status'] = isset($data['status']) ? $data['status'] : '';
        $verify['amount'] = isset($data['amount']) ? $data['amount'] : '';
        $verify['customerEmail'] = isset($data['customerEmail']) ? $data['customerEmail'] : '';
        $verify['merchantSecretKey'] = \Zotapay\Zotapay::getMerchantSecretKey();

        $signature = hash('sha256', \implode('', $verify));

        return $signature === $data['signature'];
    }


    /**
     * Get the value of Type
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
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
     * Get the value of Endpoint ID
     *
     * @return null|string
     */
    public function getEndpointID()
    {
        return $this->endpointID;
    }

    /**
     * Get the value of Processor Transaction ID
     *
     * @return null|string
     */
    public function getProcessorTransactionID()
    {
        return $this->processorTransactionID;
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
     * Get the value of Merchant OrderID
     *
     * @return null|string
     */
    public function getMerchantOrderID()
    {
        return $this->merchantOrderID;
    }

    /**
     * Get the value of Amount
     *
     * @return null|string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get the value of Currency
     *
     * @return null|string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the value of Customer Email
     *
     * @return null|string
     */
    public function getCustomerEmail()
    {
        return $this->customerEmail;
    }

    /**
     * Get the value of Custom Param
     *
     * @return null|string
     */
    public function getCustomParam()
    {
        return $this->customParam;
    }

    /**
     * Get the value of Extra Data
     *
     * @return null|string
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * Get the value of Original Request
     *
     * @return null|string
     */
    public function getOriginalRequest()
    {
        return $this->originalRequest;
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
}
