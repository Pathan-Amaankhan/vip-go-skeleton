<?php

namespace OM4\Zapier\Plugin;

use OM4\Zapier\Logger;
use OM4\Zapier\Plugin;
use OM4\Zapier\Payload\Plugin\Subscription\Order as Payload;
use OM4\Zapier\Trigger\Base;
use WC_Subscriptions;

defined( 'ABSPATH' ) || exit;

/**
 * Functionality that is enabled when the WooCommerce Subscriptions plugin is active.
 * Plugin URL: https://woocommerce.com/products/woocommerce-subscriptions/
 */
class Subscriptions {

	/**
	 * The minimum WooCommerce Subscriptions version that this plugin supports.
	 */
	const MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION = '2.4.0';

	/**
	 * Trigger keys that the subscriptions data should be added to.
	 *
	 * @var array
	 */
	private $trigger_keys = array(
		// New Order.
		'wc.new_order',
		// New Order Status Change.
		'wc.order_status_change',
	);

	/**
	 * Constructor
	 */
	public function __construct() {

		// Version check.
		if ( version_compare( WC_Subscriptions::$version, self::MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice' ) );
			( new Logger() )->alert(
				'WooCommerce Subscriptions plugin version (%s) is less than %s',
				[ WC_Subscriptions::$version, self::MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION ]
			);
			return;
		}

		add_filter( 'wc_zapier_trigger_directories', array( $this, 'wc_zapier_trigger_directories' ) );

		foreach ( $this->trigger_keys as $trigger_key ) {
			add_filter( "wc_zapier_data_{$trigger_key}", array( $this, 'order_data_override' ), 10, 2 );
		}

	}

	/**
	 * Load Subscriptions-related Triggers from the subscriptions sub directory.
	 *
	 * Executed by the 'wc_zapier_trigger_directories' filter.
	 *
	 * @param array $directories The array of directories to scan for PHP files.
	 *
	 * @return array
	 */
	public function wc_zapier_trigger_directories( $directories ) {
		$directories['OM4\\Zapier\\Trigger\\Subscription\\'] = Plugin::$plugin_dir . 'includes/Trigger/Subscription';
		return $directories;
	}

	/**
	 * Displays a message if the user isn't using a supported version of WooCommerce Subscriptions.
	 */
	public function admin_notice() {
		?>
		<div id="message" class="error">
			<p>
			<?php
				// Translators: %s: MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION Supported Woocommerce Subscription Version.
				echo esc_html( sprintf( __( 'The WooCommerce Zapier Integration plugin is only compatible with WooCommerce Subscriptions version %s or later. Please update WooCommerce Subscriptions.', 'wc_zapier' ), self::MINIMUM_SUPPORTED_SUBSCRIPTIONS_VERSION ) );
			?>
			</p>
		</div>
		<?php
	}

	/**
	 * When sending WooCommerce Order data to Zapier, also send any additional WC subscriptions fields.
	 *
	 * @param array                   $order_data Order data that will be overridden.
	 * @param OM4\Zapier\Trigger\Base $trigger    Trigger that initiated the data send.
	 *
	 * @return array
	 */
	public function order_data_override( $order_data, Base $trigger ) {
		if ( $trigger->is_sample() ) {
			$payload = Payload::from_sample();
		} else {
			// Sending live data.
			$payload    = new Payload();
			$renewal_id = get_post_meta( $order_data['id'], '_subscription_renewal', true );
			if ( ! empty( $renewal_id ) ) {
				$payload->is_subscription_renewal = true;
				$payload->subscription_id         = (int) $renewal_id;
			} else {
				$payload->is_subscription_renewal = false;
				$payload->subscription_id         = 0;
			}
		}

		return $order_data + $payload->to_array();
	}
}
