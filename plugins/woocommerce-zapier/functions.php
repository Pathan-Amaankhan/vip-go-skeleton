<?php

use OM4\Zapier\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * This function should be used to access the OM4\Zapier\Plugin singleton class.
 * It's simpler to use this function instead of a global variable.
 *
 * @return OM4\Zapier\Plugin
 */
function WC_Zapier() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return Plugin::instance();
}

/**
 * Determine whether or not the WooCommerce plugin is active (including Network Activated in WordPress Multisite).
 *
 * Based on the logic in WooCommerce's WC_Dependencies::woocommerce_active_check() function.
 *
 * @internal Do not use this function, can be changed or removed without notice.
 * @return boolean Whether the WooCommerce plugin is active or not.
 */
function wc_zapier_check_woocommerce_active() {

	$is_woocommerce = (array) get_option( 'active_plugins', [] );

	if ( is_multisite() ) {
		$is_woocommerce = array_merge( $is_woocommerce, get_site_option( 'active_sitewide_plugins', [] ) );
	}

	return in_array( 'woocommerce/woocommerce.php', $is_woocommerce, true ) || array_key_exists( 'woocommerce/woocommerce.php', $is_woocommerce );
}
