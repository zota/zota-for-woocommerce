<?php

namespace Zotapay\Log;

/**
 * @internal
 */
final class DefaultLoggerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            ['emergency', 'Test {message}', ['message' => 'Emergency']],
            ['alert','Test {message}', ['message' => 'Alert']],
            ['critical','Test {message}', ['message' => 'Critical']],
            ['error','Test {message}', ['message' => 'Error']],
            ['warning','Test {message}', ['message' => 'Warning']],
            ['notice','Test {message}', ['message' => 'Notice']],
            ['info','Test {message}', ['message' => 'Info']],
            ['debug','Test {message}', ['message' => 'Debug']]
        ];
    }

    /**
     * \Zotapay\Log\DefaultLogger::log()
     *
     * @dataProvider getData
     */
    public function testLog($level, $message, $context)
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->log($level, $message, $context);
        $this->assertSame(true, $log);
    }


    /**
     * \Zotapay\Log\DefaultLogger::__construct()
     */
    public function testConstructorWithNotValidThreshold()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger(-1);

        $tresholdReflection = new \ReflectionProperty(\Zotapay\Log\DefaultLogger::class, 'threshold');
        $tresholdReflection->setAccessible(true);

        $this->assertSame(0, $tresholdReflection->getValue($defaultLogger));
    }


    /**
     * \Zotapay\Log\DefaultLogger::log()
     */
    public function testLogWithNotValidLevel()
    {
        $this->expectException(\Zotapay\Exception\InvalidArgumentException::class);

        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $defaultLogger->log('not-valid-level', 'Test with not valid Level');
    }

    /**
     * \Zotapay\Log\DefaultLogger::emergency()
     */
    public function testEmergency()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->emergency('Test Emergency');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::alert()
     */
    public function testAlert()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->alert('Test Alert');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::critical()
     */
    public function testCritical()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->critical('Test Critical');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::error()
     */
    public function testError()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->error('Test Error');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::warning()
     */
    public function testWarning()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->warning('Test Warning');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::notice()
     */
    public function testNotice()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->notice('Test Notice');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::info()
     */
    public function testInfo()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->info('Test Info');
        $this->assertSame(true, $log);
    }

    /**
     * \Zotapay\Log\DefaultLogger::debug()
     */
    public function testDebug()
    {
        $defaultLogger = new \Zotapay\Log\DefaultLogger();
        $log = $defaultLogger->debug('Test Debug');
        $this->assertSame(true, $log);
    }
}
