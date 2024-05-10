<?php

namespace OM4\Zapier\Admin;

use OM4\Zapier\Feed\FeedFactory;
use OM4\Zapier\Plugin;

defined( 'ABSPATH' ) || exit;

/**
 * Administration (dashboard) pointers/help
 */
class Pointers {

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pagenow;

		// Display the setup help/pointer on all dashboard screens except the add/edit screen.
		if (
			! defined( 'DOING_AJAX' )
			&& 'post-new.php' !== $pagenow
			&& 'post.php' !== $pagenow
			&& 0 === FeedFactory::get_number_of_enabled_feeds()
		) {
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueuing both scripts and styles
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		// Using Pointers.
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		// Register our action.
		add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );
	}

	/**
	 * Prints any scripts and data queued for the footer.
	 *
	 * @return void
	 */
	public function admin_print_footer_scripts() {
		$pointer_content  = '<h3>' . __( 'Integrate WooCommerce &amp; Zapier', 'wc_zapier' ) . '</h3>';
		$pointer_content .= '<p>' . sprintf( __( 'To integrate WooCommerce with Zapier, you must have at least one active Zapier Feed.', 'wc_zapier' ), admin_url( 'post-new.php?post_type=wc_zapier_feed' ) ) . '</br />';
		// Translators: %s: URL of the newly created Zapier Feed.
		$pointer_content .= '<p>' . sprintf( __( '<a href="%s">Click here to create one</a>.', 'wc_zapier' ), admin_url( 'post-new.php?post_type=wc_zapier_feed' ) ) . '</p>';
		// Translators: %s: URL of the documentation of the WooCommerce Zapier.
		$pointer_content .= '<p>' . sprintf( __( '<a href="%s" target="_blank">View Documentation</a>.', 'wc_zapier' ), Plugin::DOCUMENTATION_URL ) . '</p>';
		?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function ($) {
				$('#toplevel_page_woocommerce').pointer({
					content: '<?php echo wp_kses_post( $pointer_content ); ?>',
					position: 'top',
					close: function () {
						// Once the close button is hit.
					}
				}).pointer('open');
			});
			//]]>
		</script>
		<?php
	}
}
