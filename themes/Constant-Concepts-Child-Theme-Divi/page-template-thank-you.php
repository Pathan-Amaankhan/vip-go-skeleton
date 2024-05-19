<?php 
$user_id = get_current_user_id();
$txn_no = 0;
if($user_id!=0){
if(isset($_GET['trans_num'])){
	$txn_no = $_GET['trans_num'];
}
$name = get_user_meta( $user_id, 'first_name', true );
//$transaction_date = getTransactionsDateByUser($user_id);
$transaction_date = getTransactionsDateBytxnno($txn_no);
 ?>
<p><img src="/wp-content/uploads/2024/04/checkbox-icon.png" width="72" height="73" alt="checkbox-icon" class="wp-image-321871 alignnone size-full" /></p>
<p>Hello <?php echo $name; ?></p>
<h2>Your subscription is in Process!</h2>
<p>We have sent an email to you with subscription details. <br>Please check your inbox for the same.</p>
<div class="subscription_details">

<p><strong>Please find your subscription details below:-</strong></p>
<div class="table_overflow">
  <table border="1" style="border-collapse: collapse; width: 100%;">
    <thead>
      <tr>
        <th>SUBSCRIPTION TYPE</th>
        <th>VALIDITY</th>
        <th>AMOUNT</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Annual</td>
        <td><?php echo $transaction_date ?></td>
        <td>$1000</td>
      </tr>
    </tbody>
  </table>
  </div>
</div>
<div class="back_home_wrap">
<p><strong>Go to your account to avail benefits of subscription</strong></p>
<a href="/" class="btn_btn no_arrow">Back to Home</a>
</div>
<?php } else { ?>
<div class="no_data">No Data found, please try again!</div>
<?php } ?>