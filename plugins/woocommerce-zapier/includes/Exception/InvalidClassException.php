<?php

namespace OM4\Zapier\Exception;

defined( 'ABSPATH' ) || exit;

/**
 * Exception to trigger when non-existent class is used.
 */
class InvalidClassException extends BaseException {

	/**
	 * Construct the exception.
	 *
	 * @link https://php.net/manual/en/exception.construct.php
	 *
	 * @param string $class The name of the class.
	 * @param int    $code  [optional] The Exception code.
	 */
	public function __construct( $class, $code = 0 ) {
		$message = sprintf(
			'Class %s not exists.',
			$class
		);
		parent::__construct( $message, $code );
	}
}
