<?php

namespace Zotapay\Exception;

/**
 * @internal
 */
final class ApiConnectionExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $this->expectException(ApiConnectException::class);

        $curlClient = new \Zotapay\HttpClient\CurlClient();

        $curlClientReflector = new \ReflectionClass('\Zotapay\HttpClient\CurlClient');

        $handleCurlError = $curlClientReflector->getMethod('handleCurlError');
        $handleCurlError->setAccessible(true);

        $handleCurlError->invoke($curlClient, '', '', '');
    }
}
