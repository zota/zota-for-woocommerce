<?php

namespace Zotapay\Log;

/**
 * @internal
 */
final class LogLevelsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Data Array
     * @return array
     */
    public function getData()
    {
        return [
            ['emergency', 8],
            ['alert', 7],
            ['critical', 6],
            ['error', 5],
            ['warning', 4],
            ['notice', 3],
            ['info', 2],
            ['debug', 1]
        ];
    }

    /**
     * \Zotapay\Log\LogLevels::isValidLevel()
     *
     * @dataProvider getData
     */
    public function testIsValidLevelWtihValidLevel($level, $severity)
    {
        $isValidLevel = \Zotapay\Log\LogLevels::isValidLevel($level);

        static::assertSame(true, $isValidLevel);
    }

    /**
     * \Zotapay\Log\LogLevels::isValidLevel()
     */
    public function testIsValidLevelWtihNotValidLevel()
    {
        $isValidLevel = \Zotapay\Log\LogLevels::isValidLevel('test');

        static::assertSame(false, $isValidLevel);
    }

    /**
     * \Zotapay\Log\LogLevels::getLevelSeverity()
     *
     * @dataProvider getData
     */
    public function testGetLevelSeverity($level, $severity)
    {
        $getLevelSeverity = \Zotapay\Log\LogLevels::getLevelSeverity($level);

        static::assertSame($severity, $getLevelSeverity);
    }
}
