<?php

namespace Zotapay\Exception;

/**
 * @internal
 */
final class ApiCallbackExceptionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testException()
    {
        $this->expectException(\Zotapay\Exception\ApiCallbackException::class);

        \Zotapay\Zotapay::setLogThreshold('critical'); // skip errors

        new \Zotapay\ApiCallback();
    }
}
