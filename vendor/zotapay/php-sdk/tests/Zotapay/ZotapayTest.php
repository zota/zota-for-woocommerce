<?php

namespace Zotapay;

/**
 * @internal
 */
final class ZotapayTest extends \PHPUnit\Framework\TestCase
{
    use \Zotapay\TestHelper;

    /** @var array */
    protected $logger;


    /**
     * Get Logger
     */
    public function testGetLogger()
    {
        $logger = Zotapay::getLogger();

        $this->assertInstanceOf(\Zotapay\Log\LoggerInterface::class, $logger);
    }


    /**
     * Set Logger
     */
    public function testSetLogger()
    {
        $logger = new \Zotapay\Log\DefaultLogger();
        Zotapay::setLogger($logger);

        $this->assertSame($logger, Zotapay::getLogger());
    }


    /**
     * Set setApiVersion
     */
    public function testSetApiVersion()
    {
        $version = 'text_version';

        Zotapay::setApiVersion($version);
        $this->assertEquals($version, Zotapay::getApiVersion());
    }


    /**
     * Set setApiBase
     */
    public function testSetApiBase()
    {
        $url = 'https://example.com';

        Zotapay::setApiBase($url);
        $this->assertEquals($url, Zotapay::getApiBase());
        $this->assertEquals($url . '/api/' . Zotapay::getApiVersion(), Zotapay::getApiUrl());
    }


    /**
     * Set setApiUrl
     */
    public function testSetApiUrl()
    {
        $url = 'https://example.com';

        Zotapay::setApiUrl($url);
        $this->assertEquals($url, Zotapay::getApiUrl());
    }


    /**
     * Set setLogThreshold
     */
    public function testSetLogThreshold()
    {
        $treshold = 'error';

        Zotapay::setLogThreshold($treshold);
        $this->assertEquals($treshold, Zotapay::getLogThreshold());
    }


    /**
     * Set setLogDestination
     */
    public function testSetLogDestination()
    {
        $destination = dirname(__FILE__);

        Zotapay::setLogDestination($destination);
        $this->assertEquals($destination, Zotapay::getLogDestination());
    }


    /**
     * Set setMockResponse
     */
    public function testSetMockResponse()
    {
        $mockResponse = [
            '{"code":"200","message":"","data":{}}',
            200
        ];

        Zotapay::setMockResponse($mockResponse);
        $this->assertEquals($mockResponse, Zotapay::getMockResponse());
    }
}
