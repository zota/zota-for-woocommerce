<?php

namespace Zotapay\Log;

/**
 * Log levels class.
 */
class LogLevels
{
    /**
     * Log Levels
     *
     * Description of levels:
     *     'emergency': System is unusable.
     *     'alert': Action must be taken immediately.
     *     'critical': Critical conditions.
     *     'error': Error conditions.
     *     'warning': Warning conditions.
     *     'notice': Normal but significant condition.
     *     'info': Informational messages.
     *     'debug': Debug-level messages.
     */
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';

    /**
     * Level strings mapped to integer severity.
     *
     * @var array
     */
    protected static $levelToSeverity = array(
        self::EMERGENCY => 8,
        self::ALERT     => 7,
        self::CRITICAL  => 6,
        self::ERROR     => 5,
        self::WARNING   => 4,
        self::NOTICE    => 3,
        self::INFO      => 2,
        self::DEBUG     => 1,
    );

    /**
     * Validate a level string.
     *
     * @param string $level Log level.
     * @return bool True if $level is a valid level.
     */
    public static function isValidLevel($level)
    {
        return array_key_exists(strtolower($level), self::$levelToSeverity);
    }

    /**
     * Translate level string to integer.
     *
     * @param string $level Log level, options: emergency|alert|critical|error|warning|notice|info|debug.
     * @return int 1 (debug) - 8 (emergency) or 0 if not recognized
     */
    public static function getLevelSeverity($level)
    {
        return self::isValidLevel($level) ? self::$levelToSeverity[ strtolower($level) ] : 0;
    }
}
