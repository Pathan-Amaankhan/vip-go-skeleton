<?php

namespace OM4\Zapier;

use WC_Logger;
use OM4\Zapier\Exception\InvalidLogLevelException;

defined( 'ABSPATH' ) || exit;

/**
 * Internal logger utilising the WooCommerces WC_Logger; class
 * Implements the \Psr\Log\LoggerInterface interface
 *
 * @see https://www.php-fig.org/psr/psr-3/ PSR-3: Logger Interface.
 */
class Logger {

	/**
	 * Default log level. Everything which equal or below is always logging no
	 * matter if the WC_ZAPIER_DEBUG is unset or false.
	 */
	const DEFAULT_LEVEL = 3;

	/**
	 * Valid log levels
	 *
	 * @var array
	 */
	protected $levels = [
		0 => 'emergency',
		1 => 'alert',
		2 => 'critical',
		3 => 'error',
		4 => 'warning',
		5 => 'notice',
		6 => 'info',
		7 => 'debug',
	];

	/**
	 * WC logger instance
	 *
	 * @var WC_Logger
	 */
	protected $wc_logger;

	/**
	 * Logger context the WC_Logger uses to group content together.
	 *
	 * @var array
	 */
	protected $context;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->wc_logger = wc_get_logger();
		$this->context   = [ 'source' => 'woocommerce-zapier' ];
	}

	/**
	 * System is unusable.
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function emergency( $message, $context = [] ) {
		$this->log( 'emergency', $message, $context );
	}

	/**
	 * Action must be taken immediately
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function alert( $message, $context = [] ) {
		$this->log( 'alert', $message, $context );
	}

	/**
	 * Critical conditions
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function critical( $message, $context = [] ) {
		$this->log( 'critical', $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function error( $message, $context = [] ) {
		$this->log( 'error', $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function warning( $message, $context = [] ) {
		$this->log( 'warning', $message, $context );
	}

	/**
	 * Normal but significant events
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function notice( $message, $context = [] ) {
		$this->log( 'notice', $message, $context );
	}

	/**
	 * Interesting events
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function info( $message, $context = [] ) {
		$this->log( 'info', $message, $context );
	}

	/**
	 * Detailed debug information
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 */
	public function debug( $message, $context = [] ) {
		$this->log( 'debug', $message, $context );
	}

	/**
	 * Logs with an arbitrary level
	 *
	 * @param  string       $log_level  The name of the logging level.
	 * @param  string       $message    The message to be logged. Can be formated for printf.
	 * @param  array|string $context    [optional] Dynamic part of the formatted message.
	 * @throws InvalidLogLevelException In case the log level is invalid.
	 */
	public function log( $log_level, $message, $context = [] ) {
		$message = $this->assemble_message( $message, $context );
		if ( ! in_array( $log_level, $this->levels, true ) ) {
			throw new InvalidLogLevelException( $log_level, $message );
		}

		/*
		 * If WC_ZAPIER_DEBUG isn't on, then only log messages below (more critical) than the default level.
		 * If WC_ZAPIER_DEBUG is on, then log all levels all the time
		 * The WC_ZAPIER_DEBUG is defined in the woocommerce-zapier-debug plugin.
		 */
		if (
			( ! defined( 'WC_ZAPIER_DEBUG' ) || ! WC_ZAPIER_DEBUG ) &&
			self::DEFAULT_LEVEL < array_search( $log_level, $this->levels, true )
		) {
			return;
		}
		$this->wc_logger->log( $log_level, $message, $this->context );
	}

	/**
	 * Combine message with provided context
	 * Using vsprintf for formatting.
	 *
	 * @param  string       $message The message to be logged. Can be formated for printf.
	 * @param  array|string $context [optional] Dynamic part of the formatted message.
	 * @return string
	 */
	protected function assemble_message( $message, $context = [] ) {
		$context = is_array( $context ) ? $context : [ $context ];
		return vsprintf( $message, $context );
	}
}
