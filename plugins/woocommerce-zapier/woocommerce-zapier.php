<?php

defined( 'ABSPATH' ) || exit;

/*
Plugin Name: WooCommerce Zapier Integration
Plugin URI: https://woocommerce.com/products/woocommerce-zapier/
Description: Integrates WooCommerce with the <a href="https://www.zapier.com" target="_blank">Zapier</a> web automation service.
Version: 1.9.5
Author: OM4
Author URI: https://wczap.com/
Text Domain: wc_zapier
Woo: 243589:0782bdbe932c00f4978850268c6cfe40
WC requires at least: 3.0.0
WC tested up to: 3.7.0
*/

/*
Copyright 2013-2019 OM4 (email: plugins@om4.com.au    web: https://om4.com.au/plugins/)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'WC_ZAPIER_PLUGIN_FILE', __FILE__ );
define( 'WC_ZAPIER_MINIMUM_SUPPORTED_PHP_VERSION', '5.4' );

/**
 * Displays a message if PHP version isn't supported.
 *
 * @return void
 */
function wc_zapier_incompatible_php_version_admin_notice() {
	$class = 'notice notice-error';
	// Translators: %1$s: WC_ZAPIER_MINIMUM_SUPPORTED_PHP_VERSION Supported PHP Version, %2$s: PHP_VERSION Currently running PHP version.
	$message = __( 'Your WooCommerce Zapier Integration is no longer sending data to Zapier because the integration is only compatible with PHP version %1$s or later. To resume sending to Zapier, please contact your web host to upgrade from PHP version %2$s to a newer version. We recommend using PHP 7.2 or greater.', 'wc_zapier' );
	$message = sprintf( $message, WC_ZAPIER_MINIMUM_SUPPORTED_PHP_VERSION, PHP_VERSION );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

/**
 * Displays a message if WooCommerce not available.
 *
 * @return void
 */
function wc_zapier_missing_woocommerce_admin_notice() {
	$class   = 'notice notice-error';
	$message = __( 'The WooCommerce Zapier Integration plugin requires WooCommerce. Please install and activate WooCommerce and try again.', 'wc_zapier' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

/**
 * Check PHP compatibility.
 * Note: According to the documentation (https://docs.woocommerce.com/document/create-a-plugin/)
 *   the files inside 'woo-includes' not needed any more.
 *   More info: https://github.com/woocommerce/woocommerce/issues/16304
 */
if ( version_compare( PHP_VERSION, WC_ZAPIER_MINIMUM_SUPPORTED_PHP_VERSION, '<=' ) ) {
	add_action( 'admin_notices', 'wc_zapier_incompatible_php_version_admin_notice' );
	return;
}

/**
 * Loading files
 */
require_once 'autoload.php';

/**
 * Check if WooCommerce is active
 */
if ( ! wc_zapier_check_woocommerce_active() ) {
	add_action( 'admin_notices', 'wc_zapier_missing_woocommerce_admin_notice' );
	return;
}

// Let's get this thing started!
WC_Zapier();
