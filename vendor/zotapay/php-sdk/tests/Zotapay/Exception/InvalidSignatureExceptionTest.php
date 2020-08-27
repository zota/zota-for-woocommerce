<?php

namespace Zotapay\Exception;

/**
 * @internal
 */
final class InvalidSignatureExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $this->expectException(\Zotapay\Exception\InvalidSignatureException::class);

        throw new \Zotapay\Exception\InvalidSignatureException();
    }
}
