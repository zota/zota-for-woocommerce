<?php

namespace Zotapay\Log;

/**
 * @internal
 */
final class DefaultLogHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            [0, 'emergency', 'Test {message}', ['message' => 'Emergency'], '1970-01-01T00:00:00+00:00 EMERGENCY Test Emergency'],
            [0, 'alert','Test {message}', ['message' => 'Alert'], '1970-01-01T00:00:00+00:00 ALERT Test Alert'],
            [0, 'critical','Test {message}', ['message' => 'Critical'], '1970-01-01T00:00:00+00:00 CRITICAL Test Critical'],
            [0, 'error','Test {message}', ['message' => 'Error'], '1970-01-01T00:00:00+00:00 ERROR Test Error'],
            [0, 'warning','Test {message}', ['message' => 'Warning'], '1970-01-01T00:00:00+00:00 WARNING Test Warning'],
            [0, 'notice','Test {message}', ['message' => 'Notice'], '1970-01-01T00:00:00+00:00 NOTICE Test Notice'],
            [0, 'info','Test {message}', ['message' => 'Info'], '1970-01-01T00:00:00+00:00 INFO Test Info'],
            [0, 'debug','Test {message}', ['message' => 'Debug'], '1970-01-01T00:00:00+00:00 DEBUG Test Debug']
        ];
    }

    /**
     * \Zotapay\Log\DefaultLogHandler::handle()
     *
     * @dataProvider getData
     */
    public function testHandle($timestamp, $level, $message, $context)
    {
        $handler = new \Zotapay\Log\DefaultLogHandler();

        static::assertSame(true, $handler->handle($timestamp, $level, $message, $context));
    }

    /**
     * \Zotapay\Log\DefaultLogHandler::handle()
     *
     * @dataProvider getData
     */
    public function testHandleWithLogDestination($timestamp, $level, $message, $context)
    {
        $logDestination = dirname(__FILE__, 3) . '/test.log';
        \Zotapay\Zotapay::setLogDestination($logDestination);

        $handler = new \Zotapay\Log\DefaultLogHandler();

        static::assertSame(true, $handler->handle($timestamp, $level, $message, $context));

        \Zotapay\Zotapay::setLogDestination(null);
        if (\touch($logDestination)) {
            \unlink($logDestination);
        }
    }

    /**
     * \Zotapay\Log\DefaultLogHandler::formatEntry()
     *
     * @dataProvider getData
     */
    public function testformatEntry($timestamp, $level, $message, $context, $formatted)
    {
        $formatEntry = \Zotapay\Log\DefaultLogHandler::formatEntry($timestamp, $level, $message, $context);

        static::assertSame($formatted, $formatEntry);
    }
}
