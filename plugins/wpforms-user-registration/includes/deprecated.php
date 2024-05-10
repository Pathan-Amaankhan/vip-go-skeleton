<?php
/**
 * Deprecated functions.
 * This file is used to keep backward compatibility with older versions of the plugin.
 * The functions and classes listed below will be removed in December 2023.
 *
 * @since 2.2.0
 */

/**
 * Check addon requirements.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 */
function wpforms_user_registration_required() {

	_deprecated_function( __FUNCTION__, '2.2.0 of the WPForms User Registration addon' );
}

/**
 * Deactivate the plugin.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 */
function wpforms_user_registration_deactivation() {

	_deprecated_function( __FUNCTION__, '2.2.0 of the WPForms User Registration addon' );
}

/**
 * Admin notice for a minimum PHP version.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 */
function wpforms_user_registration_fail_php_version() {

	_deprecated_function( __FUNCTION__, '2.2.0 of the WPForms User Registration addon' );
}

/**
 * Admin notice for minimum WPForms version.
 *
 * @since 2.0.0
 * @deprecated 2.2.0
 */
function wpforms_user_registration_fail_wpforms_version() {

	_deprecated_function( __FUNCTION__, '2.2.0 of the WPForms User Registration addon' );
}

/**
 * Load the plugin updater.
 *
 * @since 1.0.0
 * @deprecated 2.2.0
 *
 * @param string $key License key.
 */
function wpforms_user_registration_updater( $key ) {

	_deprecated_function( __FUNCTION__, '2.2.0 of the WPForms User Registration addon' );
}
