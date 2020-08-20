<?php

namespace Zotapay\Log;

/**
 * Class DefaultLogHandler.
 */
class DefaultLogHandler
{
    /**
     * Handle a log entry.
     *
     * @param int    $timestamp Log timestamp.
     * @param string $level emergency|alert|critical|error|warning|notice|info|debug.
     * @param string $message Log message.
     * @param array  $context Additional information.
     *
     * @return bool False if value was not handled and true if value was handled.
     */
    public function handle($timestamp, $level, $message, $context)
    {
        $entry = self::formatEntry($timestamp, $level, $message, $context);

        // check log destination
        $logDestination = \Zotapay\Zotapay::getLogDestination();

        if (null !== $logDestination && touch($logDestination)) {
            return \error_log($entry . PHP_EOL, 3, $logDestination);
        }

        return \error_log($entry, 0);
    }

    /**
     * Builds a log entry text from level, timestamp and message.
     *
     * @param int    $timestamp Log timestamp.
     * @param string $level emergency|alert|critical|error|warning|notice|info|debug.
     * @param string $message Log message.
     * @param array  $context Additional information for log handlers.
     *
     * @return string Formatted log entry.
     */
    public static function formatEntry($timestamp, $level, $message, $context)
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        $interpolated = array(
            'timestamp' => date('c', $timestamp),
            'level'     => strtoupper($level),
            'message'   => strtr($message, $replace),
        );

        return implode(' ', $interpolated);
    }
}
