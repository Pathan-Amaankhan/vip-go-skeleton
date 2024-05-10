<?php

namespace OM4\Zapier\Admin;

use Exception;
use OM4\Zapier\Plugin;
use OM4\Zapier\Feed\Feed;
use OM4\Zapier\Trigger\TriggerFactory;

defined( 'ABSPATH' ) || exit;

/**
 * Add/Edit Zapier Feed dashboard screen.
 */
class FeedUI {

	/** @var string */
	private $prefix = 'wc_zapier_';

	/** @var array */
	private $meta_fields = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_filter( 'manage_wc_zapier_feed_posts_columns', array( $this, 'columns' ) );
		add_filter( 'manage_wc_zapier_feed_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );

		add_action( 'admin_head-post.php', array( $this, 'hide_publishing_actions' ) );
		add_action( 'admin_head-post-new.php', array( $this, 'hide_publishing_actions' ) );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		// Zapier Feeds listing screen.
		add_filter( 'bulk_actions-edit-wc_zapier_feed', array( $this, 'bulk_actions' ) );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

		$this->meta_fields = array(
			array(
				'type' => 'title',
				'desc' => '<p>' . __( 'To configure your Zapier Feed, complete the information below.', 'wc_zapier' ) . '</p>' .
						// Translators: %s: URL of the WooCommerce Zapier documentation.
						'<p>' . sprintf( __( 'Note: The setup process is quite involved, so we recommend <a href="%s" target="_blank" title="(Opens in a new window)">reading the documentation</a>.', 'wc_zapier' ), Plugin::DOCUMENTATION_URL . '#setup' ) . '</p>' .
						// Translators: %s: URL of the Triggers feature in the documentation.
						'<p>' . sprintf( __( '<a href="%s" target="_blank" title="(Opens in a new window)">Please click here for a description of each Trigger</a>.', 'wc_zapier' ), Plugin::DOCUMENTATION_URL . '#triggers' ) . ' </p>',
				'id'   => "{$this->prefix}feed_details",
			),
			array(
				'id'                      => "{$this->prefix}trigger",
				'title'                   => __( 'Trigger', 'wc_zapier' ),
				'type'                    => 'radio',
				'options'                 => TriggerFactory::get_triggers_for_display(),
				'maps_to'                 => 'trigger',
				'display_on_posts_screen' => true,
			),
			array(
				'id'                      => "{$this->prefix}webhook_url",
				'title'                   => __( 'Webhook URL', 'wc_zapier' ),
				// Translators: $s: Webhook URL Example.
				'desc'                    => '<br />' . sprintf( __( 'The URL to your Zapier webhook. This information is provided by Zapier when you create a new Zap on the Zapier website.<br />Example: <code>%s</code>', 'wc_zapier' ), Feed::WEBHOOK_URL_EXAMPLE ),
				'type'                    => 'text',
				'css'                     => 'min-width:400px;',
				'maps_to'                 => 'webhook_url',
				'display_on_posts_screen' => true,
			),
			array(
				'id'      => "{$this->prefix}title",
				'title'   => __( 'Title', 'wc_zapier' ),
				'desc'    => '<br />' . __( 'Descriptive title/name of this Zapier Feed.<br />Should typically match the name of your Zap on the Zapier website.', 'wc_zapier' ),
				'type'    => 'text',
				'css'     => 'min-width:400px;',
				'maps_to' => 'title',
			),
			array(
				'type' => 'sectionend',
				'id'   => "{$this->prefix}feed_details",
			),
		);
	}

	/**
	 * Adds the Zapier Feed Details metabox to the Add/Edit Zapier Feed Dashboard Screen
	 *
	 * @return void
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'zapierfeedinfo',
			__( 'Zapier Feed Details', 'wc_zapier' ),
			array( $this, 'metabox_output' ),
			'wc_zapier_feed',
			'normal',
			'high'
		);
	}

	/**
	 * Obtains the current/default values for the zapier feed fields
	 * This is necessary because the woocommerce_admin_fields() uses
	 * get_option() Executed by the pre_option_* filters.
	 *
	 * @param string $method Method being called.
	 * @param array  $args   Enumerated array containing the parameters passed.
	 *
	 * @return false|string
	 */
	public function __call( $method, $args ) {
		global $post;
		$feed  = new Feed( $post );
		$field = str_replace( "pre_option_{$this->prefix}", '', $method );
		if ( $field !== $method ) {
			switch ( $field ) {
				case 'webhook_url':
					return $feed->webhook_url();
				case 'trigger':
					return is_null( $feed->trigger() ) ? 'wc.new_order' : $feed->trigger()->get_trigger_key();
				case 'title':
					return $feed->title();
			}
		}
		return false;
	}

	/**
	 * The output for the metabox that is shown on the Add/Edit Zapier Feed screen.
	 */
	public function metabox_output() {
		global $post;

		// We're going to use WooCommerce's settings/admin fields API (including the woocommerce_admin_fields() function).
		require_once WC()->plugin_path() . '/includes/admin/wc-admin-functions.php';

		wp_nonce_field( "{$this->prefix}feed_details", "{$this->prefix}feed_details_nonce", true, true );

		foreach ( $this->meta_fields as $field ) {
			$name = "pre_option_{$field['id']}";
			add_filter( $name, array( $this, $name ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['post'] ) ) {
			// We're editing an existing feed.

			// Check for validation errors and display them if necessary.
			$messages = get_option( 'wc_zapier_feed_messages', array() );

			if ( isset( $messages[ $post->ID ] ) && is_array( $messages[ $post->ID ] ) ) {
				// Critical errors.
				foreach ( $messages[ $post->ID ]['errors'] as $error ) {
					// Error messages contain HTML code, so we can't use esc_html().
					echo '<div class="error"><p>' . wp_kses_post( $error ) . '</p></div>';
				}
				// Friendly warnings.
				foreach ( $messages[ $post->ID ]['warnings'] as $warning ) {
					// Warnings contain HTML code, so we can't use esc_html().
					echo '<div class="updated"><p>' . wp_kses_post( $warning ) . '</p></div>';
				}
				unset( $messages[ $post->ID ] );
				update_option( 'wc_zapier_feed_messages', $messages );
			} elseif ( 'publish' === get_post_status( $post ) ) {
				// No warnings/errors with this feed, and it is published (active).
				echo '<div class="updated"><p>' . esc_html( __( 'This Zapier Feed is active and ready to receive real data.', 'wc_zapier' ) ) . '</p></div>';
			} else {
				// No warnings/errors with this feed.
				echo '<div class="updated"><p>' . esc_html( __( 'This Zapier Feed is inactive. No real data will be sent to this feed until it is made active (published).', 'wc_zapier' ) ) . '</p></div>';
			}
		}

		// Add new feed screen.
		woocommerce_admin_fields( $this->meta_fields );

		foreach ( $this->meta_fields as $field ) {
			$name = "pre_option_{$field['id']}";
			remove_filter( $name, array( $this, $name ) );
		}
	}

	/**
	 * Saves the zapier feed data into the correct fields
	 * Executed by the 'save_post' hook.
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post Post object.
	 *
	 * @return mixed
	 * @throws Exception If saving fails and the corresponding error logging fails.
	 */
	public function save_post( $post_id, $post ) {
		// Ignore other post types.
		if ( 'wc_zapier_feed' !== $post->post_type ) {
			return;
		}

		// Ignore post revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Ignore autosaves.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Ignore autosaves.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Ignore auto drafts.
		if ( 'auto-draft' === $post->post_status ) {
			return;
		}

		// Ignore feeds that are being trashed.
		if ( 'trash' === $post->post_status ) {
			return;
		}

		// Ignore unauthenticated requests.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Verify nonce.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		if ( ! isset( $_POST[ "{$this->prefix}feed_details_nonce" ] ) || ! wp_verify_nonce( $_POST[ "{$this->prefix}feed_details_nonce" ], "{$this->prefix}feed_details" ) ) {
			return;
		}

		$feed = new Feed( $post );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$feed->set_title( isset( $_POST[ "{$this->prefix}title" ] ) ? $_POST[ "{$this->prefix}title" ] : '' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$feed->set_webhook_url( isset( $_POST[ "{$this->prefix}webhook_url" ] ) ? $_POST[ "{$this->prefix}webhook_url" ] : '' );
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$feed->set_trigger_with_key( isset( $_POST[ "{$this->prefix}trigger" ] ) ? $_POST[ "{$this->prefix}trigger" ] : '' );

		$validation_results = $feed->validate();

		if ( is_array( $validation_results ) ) {
			// We have warnings and/or errors.
			$messages = get_option( 'wc_zapier_feed_messages', array() );

			$messages[ $post->ID ] = $validation_results;

			if ( ! empty( $validation_results['errors'] ) ) {
				// Validation errors exist.
				$messages[ $post->ID ]['errors'][] = _n( 'This Zapier Feed cannot be activated until this issue is resolved.', 'This Zapier Feed cannot be made active until these issues are resolved.', count( $messages[ $post->ID ]['errors'] ), 'wc_zapier' );
			}
			update_option( 'wc_zapier_feed_messages', $messages );
		}

		add_filter( 'redirect_post_location', array( $this, 'redirect_post_location' ), 10, 2 );

		// Temporarily disable this save_post hook while we update the post record.
		remove_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		$feed->save();
		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

		return $post_id;
	}

	/**
	 * If we encounter a validation error when saving a Feed,
	 * override the redirect location so WordPress doesn't display a message.
	 * Instead, our validation error messages are displayed.
	 *
	 * Executed by the 'redirect_post_location' WordPress filter.
	 *
	 * @param string $location URL.
	 * @param int    $post_id Post ID of the post that was just saved.
	 *
	 * @return string
	 */
	public function redirect_post_location( $location, $post_id ) {
		$location = add_query_arg( 'message', '0', get_edit_post_link( $post_id, 'url' ) );
		return $location;
	}

	/**
	 * If we're on the Add/Edit Zapier feed screen, hide the Visibility and
	 * Published Date from the Publish Metabox.
	 *
	 * @return void
	 */
	public function hide_publishing_actions() {
		global $post;
		if ( 'wc_zapier_feed' === $post->post_type ) {
			echo '
					<style type="text/css">
							#misc-publishing-actions #visibility,
							#misc-publishing-actions .curtime {
									display:none;
							}
					</style>
			';
		}
	}

	/**
	 * Disable the Bulk Edit feature on the Zapier Feeds listing screen.
	 *
	 * Executed by the 'bulk_actions-edit-wc_zapier_feed' filter.
	 *
	 * @param array $actions Bulk actions to filter.
	 *
	 * @return array
	 */
	public function bulk_actions( $actions ) {
		if ( isset( $actions['edit'] ) ) {
			unset( $actions['edit'] );
		}
		return $actions;
	}

	/**
	 * Disable Quick Edit on the Zapier Feeds listing screen.
	 *
	 * @param array   $actions Array of actions.
	 * @param WP_Post $post    Post object.
	 *
	 * @see http://core.trac.wordpress.org/ticket/19343.
	 * @return mixed
	 */
	public function post_row_actions( $actions, $post ) {
		if ( 'wc_zapier_feed' === $post->post_type ) {
			unset( $actions['inline hide-if-no-js'] );
		}
		return $actions;
	}

	/**
	 * Customise the columns that are displayed on the Zapier Feeds dashboard listing screen.
	 *
	 * Executed by the 'manage_wc_zapier_feed_posts_columns' filter.
	 *
	 * @param array $columns Columns to override.
	 *
	 * @return array
	 */
	public function columns( $columns ) {
		// Remove date column.
		unset( $columns['date'] );

		// Add our custom fields.
		foreach ( $this->meta_fields as $field ) {
			if ( isset( $field['display_on_posts_screen'] ) && $field['display_on_posts_screen'] ) {
				$columns[ $field['id'] ] = $field['title'];
			}
		}
		return $columns;
	}

	/**
	 * Output the custom columns on the Zaper Feeds listing screen.
	 *
	 * Executed by the 'manage_wc_zapier_feed_posts_custom_column' filter.
	 *
	 * @param string $column Column name being displayed.
	 * @param int    $post_id Post ID for the Post being displayed.
	 *
	 * @return void
	 */
	public function custom_column( $column, $post_id ) {

		$feed = new Feed( $post_id );

		foreach ( $this->meta_fields as $field ) {
			if ( $column === $field['id'] ) {
				if ( isset( $field['maps_to'] ) ) {
					switch ( $field['id'] ) {
						case "{$this->prefix}trigger":
							// Convert the trigger id into a user-friendly name.
							if ( ! is_null( $feed->{$field['maps_to']}() ) ) {
								// Just in case the trigger key in the database no longer exists.
								echo esc_html( $feed->{$field['maps_to']}()->get_trigger_title() );
							}
							break;
						default:
							echo esc_html( $feed->{$field['maps_to']}() );
					}
				}
			}
		}
	}

}
