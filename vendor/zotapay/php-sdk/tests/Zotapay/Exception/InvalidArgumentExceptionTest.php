<?php

namespace Zotapay\Exception;

/**
 * @internal
 */
final class InvalidArgumentExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $this->expectException(InvalidArgumentException::class);

        $curlClient = new \Zotapay\HttpClient\CurlClient();

        $curlClient->request('non-existing', '', []);
    }
}
