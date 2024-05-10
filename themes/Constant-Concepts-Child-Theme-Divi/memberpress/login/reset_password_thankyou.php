<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');} ?>

<h3><?php _ex('Password successfully created', 'ui', 'memberpress'); ?></h3>
<p><?php _ex('Your password has been created and an email has also been sent notifying you of the change. Be sure to write your new password down and store it somewhere safe.', 'ui', 'memberpress'); ?></p>

<script>
setTimeout(function(){
	<?php //wp_safe_redirect(wp_login_url()); exit(); ?>
	<?php //wp_safe_redirect(home_url()); exit(); ?>
	window.location.href="<?php echo home_url().'/login';  ?>";
	}, 3000);
</script>