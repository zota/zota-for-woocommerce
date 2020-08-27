<?php

namespace Zotapay\Exception;

/**
 * @internal
 */
final class InvalidRequestExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testException()
    {
        $this->expectException(InvalidRequestException::class);

        throw new \Zotapay\Exception\InvalidRequestException();
    }
}
