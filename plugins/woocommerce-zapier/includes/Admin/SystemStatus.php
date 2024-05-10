<?php

namespace OM4\Zapier\Admin;

use OM4\Zapier\Plugin;
use OM4\Zapier\Feed\FeedFactory;

defined( 'ABSPATH' ) || exit;

/**
 * Adds various debugging information to the WooCommerce System Status screen
 */
class SystemStatus {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_system_status_environment_rows', array( $this, 'woocommerce_system_status_rows' ) );
	}

	/**
	 * Add our own debugging information to the WooCommerce Status screen.
	 *
	 * Executed by the woocommerce_system_status_environment_rows WooCommerce filter.
	 *
	 * @param array $posting data to be displayed on the WooCommerce status screen.
	 *
	 * @return mixed
	 */
	public function woocommerce_system_status_rows( $posting ) {
		// Number of active Zapier Feeds.
		$feeds                   = FeedFactory::get_enabled_feeds();
		$num_feeds               = count( $feeds );
		$posting['zapier_feeds'] = array(
			'name'    => __( 'Zapier Integration - Active Feeds', 'wc_zapier' ),
			// Translators: %d: Count of the active Zapier Feed.
			'note'    => sprintf( _n( '%d active Zapier Feed.', '%d active Zapier Feeds.', $num_feeds, 'wc_zapier' ), $num_feeds ),
			'success' => $num_feeds ? true : false,
		);

		// Pending Cron Tasks.
		$cron_array     = _get_cron_array();
		$num_cron_tasks = 0;
		foreach ( (array) $cron_array as $time => $cron ) {
			if ( 'version' === $time ) {
				continue;
			}
			foreach ( (array) $cron as $hook => $task ) {
				foreach ( (array) $task as $id => $details ) {
					if ( strpos( $hook, 'zapier_triggered_' ) !== false ) {
						$num_cron_tasks ++;
					}
				}
			}
		}
		$note    = '';
		$success = true;
		if ( 0 === $num_cron_tasks ) {
			// Translators: %d: number of Zapier cron tasks.
			$note = sprintf( __( '%d pending Zapier cron tasks.', 'wc_zapier' ), $num_cron_tasks );
		} elseif ( 1 === $num_cron_tasks ) {
			// 1 pending cron task.
			// Translators: %1$d: number of Zapier cron tasks. %2$s: URL of the WooCommerce Zapier documentation.
			$note    = sprintf( __( '%1$d pending Zapier cron task. Your WordPress cron may not be working correctly. Please see <a href="%2$s">here for troubleshooting steps</a>.', 'wc_zapier' ), $num_cron_tasks, esc_url( Plugin::DOCUMENTATION_URL . '#troubleshooting' ) );
			$success = false;
		} else {
			// More than 1 pending cron tasks.
			// Translators: %1$d: number of Zapier cron tasks. %2$s: URL of the WooCommerce Zapier documentation.
			$note    = sprintf( __( '%1$d pending Zapier cron tasks. Your WordPress cron may not be working correctly. Please see <a href="%2$s">here for troubleshooting steps</a>.', 'wc_zapier' ), $num_cron_tasks, esc_url( Plugin::DOCUMENTATION_URL . '#troubleshooting' ) );
			$success = false;
		}
		$posting['zapier_cron_tasks'] = array(
			'name'    => __( 'Zapier Integration - Cron Tasks', 'wc_zapier' ),
			'note'    => $note,
			'success' => $success,
		);

		// Send sample data to each active feed.
		$i = 0;
		foreach ( $feeds as $feed ) {
			$i++;
			$result = $feed->trigger()->send_sample_data_payload( $feed );

			$note = '';
			if ( true === $result ) {
				// Translators: %s: URL of the Zapier Webhook.
				$note = sprintf( __( 'Successfully sent sample data to %s.', 'wc_zapier' ), esc_url( $feed->webhook_url() ) );
			} else {
				// Translators: %1$s: URL of the Zapier Webhook. %2$s: Error message. %3$s: URL of the WooCommerce Zapier documentation.
				$note   = sprintf( __( 'Error sending sample data to %1$s. Error message: %2$s. Please see <a href="%3$s">here for troubleshooting steps</a>.', 'wc_zapier' ), esc_url( $feed->webhook_url() ), esc_html( $result ), esc_url( Plugin::DOCUMENTATION_URL . '#troubleshooting' ) );
				$result = false;
			}

			$posting[ 'zapier_' . $feed->title() . 'feed_name' ]   = array(
				// Translators: %d: Number of the current Zapier Feed.
				'name'    => sprintf( __( 'Zapier Feed #%d Name', 'wc_zapier' ), $i ),
				'note'    => $feed->title(),
				'success' => true,
			);
			$posting[ 'zapier_' . $feed->title() . 'webhook_url' ] = array(
				// Translators: %d: Number of the current Zapier Feed.
				'name'    => sprintf( __( 'Zapier Feed #%d Webhook URL', 'wc_zapier' ), $i ),
				'note'    => $feed->webhook_url(),
				'success' => true,
			);
			$posting[ 'zapier_' . $feed->title() . 'trigger' ]     = array(
				// Translators: %d: Number of the current Zapier Feed.
				'name'    => sprintf( __( 'Zapier Feed #%d Trigger', 'wc_zapier' ), $i ),
				'note'    => $feed->trigger()->get_trigger_title(),
				'success' => true,
			);
			$posting[ 'zapier_' . $feed->title() . 'result' ]      = array(
				// Translators: %d: Number of the current Zapier Feed.
				'name'    => sprintf( __( 'Zapier Feed #%d Test Result', 'wc_zapier' ), $i ),
				'note'    => $note,
				'success' => $result,
			);
		}
		return $posting;
	}
}
