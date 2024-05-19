<?php 
if(isset($_GET['id'])){
$user_id = base64_decode($_GET['id']);
$name = get_user_meta( $user_id, 'first_name', true );
update_user_meta( $user_id, 'unsubscribe', 1 );
?>
<p><img src="/wp-content/uploads/2024/04/checkbox-icon.png" width="72" height="73" alt="checkbox-icon" class="wp-image-321871 alignnone size-full" /></p>
<p>Hello <?php echo $name; ?></p>
<h2>Unsubscribed</h2>
<p>You have been unsubscribed from the Paid Subscription Renewal Jefferies mailing list.</p>
<?php } else { ?>
<div class="no_data">No Data found, please try again!</div>
<?php } ?>