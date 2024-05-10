<?php

namespace OM4\Zapier;

defined( 'ABSPATH' ) || exit;

/**
 * Dynamic call for new classes methods with WordPress deprecation warning
 */
trait LegacyTrait {

	/**
	 * Deprecating the object access as well.
	 */
	public function __construct() {
		_deprecated_function(
			esc_attr( __CLASS__ . '::__construct()' ),
			'1.9.0',
			esc_attr( self::$new_class . '::__construct()' )
		);
	}

	/**
	 * Calling on object content
	 *
	 * @param string $name      The name of the called method.
	 * @param array  $arguments The argument.
	 * @return mixed            The original value of the callback, or FALSE on error.
	 */
	public function __call( $name, array $arguments ) {
		return self::deprecated_call( $name, $arguments );
	}

	/**
	 * Calling on static content
	 *
	 * @param string $name      The name of the called method.
	 * @param array  $arguments The argument.
	 * @return mixed            The original value of the callback, or FALSE on error.
	 */
	public static function __callStatic( $name, array $arguments ) {
		return self::deprecated_call( $name, $arguments, false );
	}

	/**
	 * Handling the call
	 *
	 * @param string $name           The name of the called method.
	 * @param array  $arguments      The argument.
	 * @param bool   $object_context Define object/static calling context.
	 * @return mixed                 The original value of the callback, or FALSE on error.
	 */
	protected static function deprecated_call( $name, array $arguments, $object_context = true ) {
		$class = $object_context ? new self::$new_class() : self::$new_class;

		$called_function_text = sprintf(
			'%s::%s()',
			__CLASS__,
			$name
		);

		$new_function_text = sprintf(
			'%s::%s()',
			self::$new_class,
			$name
		);

		_deprecated_function( esc_attr( $called_function_text ), '1.9.0', esc_attr( $new_function_text ) );
		return call_user_func_array( [ $class, $name ], $arguments );
	}
}
