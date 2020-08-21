<?php
/**
 * Logger Class.
 *
 * @package ZotaWooCommerce
 * @author  Zota
 */

namespace Zota\Zota_WooCommerce\Includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Logger Class.
 */
class Logger {

	/**
	 * The Logger object
	 *
	 * @var WC_Logger
	 */
	public static $logger;

	/**
	 * The Logger levels
	 *
	 * @var array
	 */
	public static $logger_low_levels = array( 'notice', 'info', 'debug' );

	const WC_LOG_FILENAME = 'wc_gateway_zota';

	/**
	 * Add a log entry.
	 *
	 * @param string $level   One of the following:
	 *                        'emergency': System is unusable.
	 *                        'alert': Action must be taken immediately.
	 *                        'critical': Critical conditions.
	 *                        'error': Error conditions.
	 *                        'warning': Warning conditions.
	 *                        'notice': Normal but significant condition.
	 *                        'info': Informational messages.
	 *                        'debug': Debug-level messages.
	 * @param string $message Log message.
	 * @param array  $context Optional. Additional information for log handlers.
	 */
	public static function log( $level, $message, $context = array() ) {

		if ( ! class_exists( 'WC_Logger' ) ) {
			return;
		}

		if ( apply_filters( 'wc_gateway_zota_logging', true, $message ) ) {
			// Get logger.
			if ( empty( self::$logger ) ) {
				self::$logger = wc_get_logger();
			}

			// Get logging settings.
			$logging  = true;
			$settings = get_option( 'woocommerce_wc_gateway_zota_settings' );
			if ( empty( $settings ) || isset( $settings['logging'] ) && 'yes' !== $settings['logging'] ) {
				$logging = false;
			}

			// Skip logging for lower levels.
			if ( ! $logging && in_array( $level, self::$logger_low_levels, true ) ) {
				return;
			}

			// Add context.
			$context['source'] = self::WC_LOG_FILENAME;

			// Filter context.
			$context = apply_filters( 'wc_gateway_zota_logging_context', $context );

			self::$logger->log( $level, $message, $context );
		}
	}


	/**
	 * Adds an emergency level message.
	 *
	 * System is unusable.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function emergency( $message, $context = array() ) {
		self::log( 'emergency', $message, $context );
	}

	/**
	 * Adds an alert level message.
	 *
	 * Action must be taken immediately.
	 * Example: Entire website down, database unavailable, etc.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function alert( $message, $context = array() ) {
		self::log( 'alert', $message, $context );
	}

	/**
	 * Adds a critical level message.
	 *
	 * Critical conditions.
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function critical( $message, $context = array() ) {
		self::log( 'critical', $message, $context );
	}


	/**
	 * Adds an error level message.
	 *
	 * Runtime errors that do not require immediate action but should typically be logged
	 * and monitored.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function error( $message, $context = array() ) {
		self::log( 'error', $message, $context );
	}


	/**
	 * Adds a warning level message.
	 *
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things that are not
	 * necessarily wrong.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function warning( $message, $context = array() ) {
		self::log( 'warning', $message, $context );
	}


	/**
	 * Adds a notice level message.
	 *
	 * Normal but significant events.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public function notice( $message, $context = array() ) {
		self::log( 'notice', $message, $context );
	}


	/**
	 * Adds a info level message.
	 *
	 * Interesting events.
	 * Example: User logs in, SQL logs.
	 *
	 * @param string $message Message to log.
	 * @param array  $context Log context.
	 */
	public static function info( $message, $context = array() ) {
		self::log( 'info', $message, $context );
	}


	/**
	 * Adds a debug level message.
	 *
	 * Detailed debug information.
	 *
	 * @param string $message    Message to log.
	 * @param array  $context    Log context.
	 * @param int    $start_time Timestamp.
	 * @param int    $end_time   Timestamp.
	 */
	public static function debug( $message, $context = array(), $start_time = null, $end_time = null ) {

		$log_entry = ' ';

		if ( ! is_null( $start_time ) ) {
			$formatted_start_time = date_i18n( get_option( 'date_format' ) . ' g:ia', $start_time );
			$end_time             = is_null( $end_time ) ? current_time( 'timestamp' ) : $end_time;
			$formatted_end_time   = date_i18n( get_option( 'date_format' ) . ' g:ia', $end_time );
			$elapsed_time         = round( abs( $end_time - $start_time ) / 60, 2 );

			$log_entry .= "\n" . 'Start time: ' . $formatted_start_time . "\n" . $message . "\n";
			$log_entry .= 'End time ' . $formatted_end_time . ' (' . $elapsed_time . ')';
		} else {
			$log_entry .= $message;
		}

		self::log( 'debug', $message, $context );
	}
}
