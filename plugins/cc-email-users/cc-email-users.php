<?php
/**
 * Plugin Name: CC Email Paid Subscriber Users
 * Plugin URI: http://constantconcepts.io/
 * Description: A plugin to email user to continue with their paid subscription on website.
 * Version: 1.1
 * Author: Constant Concepts
 * Author URI: http://constantconcepts.io/
 **/

 function cc_emailusersmenu(){
   add_menu_page('CC Email USers','Email Users','manage_options','cc_eu_home','cc_eu_home','');
   add_submenu_page( 'cc_eu_home', 'Bulk Email Users', 'Bulk / CSV','manage_options', 'cc_eu_csv','cc_eu_csv');
 }
 add_action('admin_menu','cc_emailusersmenu');

 function cc_eu_home(){
	echo '<h2>Email USERS</h2>
		 <p>Email Users to Renew their subscriptions on the website, you can <a href="/wp-admin/admin.php?page=cc_eu_csv">Bulk Email</a> users using a csv file.</p>';
 }

 function cc_eu_csv(){
	$drycheck=""; $livecheck="";
	if(isset($_POST['drylive'])){
		if($_POST['drylive']=="dry"){
			$drycheck="checked";
		}else{
			$livecheck="checked";
		}
	}else{
		$drycheck="checked";
	}

	if(isset($_POST['csvUrl'])){
		$csvUri=$_POST['csvUrl'];
	}else{
		$csvUri="";
	}

	echo '<style type="text/css">
			body{font-family:arial;font-size:14px;}.note{color:#ff0000;font-style:italic;}form{margin:20px 0px 50px 0px;text-align:center;}
			.drylive{margin:0px 2px!important;}#csvUrl{width:60%;margin:0px 10px;}input{padding:5px;}label{margin:0px 15px;}th,td{text-align:left;padding:5px;}
			.ccplset{margin-top:50px;}
		</style>
		<form method="POST" id="ccdelusers" name="ccdelusers">
			<h3>Bulk Send Emails Using CSV file</h3>
			<p class="noteset">Step 1: Upload your csv file to Media.</p>
			<p class="noteset">Step 2: Copy full csv path from Media and paste into url box below.</p>
			<p class="note">NOTE: There should be only 1 column in csv file containing email addresses of users to be emailed.</p>
			<p>
				<label>Dry Run:<input type="radio" name="drylive" value="dry" class="drylive" id="dldry" '.$drycheck.'/></label>
				<label>Live:<input type="radio" name="drylive" value="live" class="drylive" id="dllive" '.$livecheck.'/></label>
			</p>
			<strong>Enter CSV file url</strong>
			<input type"url" name="csvUrl" id="csvUrl" value="'.$csvUri.'" pattern="https://.*" title="Enter a valid CSV file url" placeholder="Upload CSV file to media manage, copy full url and paste here!" required>
			<input type="submit" value="SUBMIT" name="btnSubmit">
		</form>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				jQuery("#ccdelusers").submit(function(){
					if(jQuery("#dllive").is(":checked")){ return confirm("Are you sure you want to send renewal email to users from CSV file ?");	}
				});
			});
		</script>';

	if(isset($_POST['csvUrl'])){
		global $wpdb;
		$csvUrl=$_POST['csvUrl'];
		if(($handle = fopen($csvUrl, "r")) !== FALSE) {
			echo "<table width='100%'>";
			echo "<tr>";
				echo "<th>S.No.</th><th>Email</th><th>Result</th><th>Action</th>";    
			echo "</tr>";
			$sn=1;
			  while(($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			     if(!empty($data[0])){
					 $getUser = "SELECT `ID`,`user_email`,`display_name` FROM ".$wpdb->users." WHERE `user_email`='".$data[0]."'";
					 $user = $wpdb->get_results($getUser, OBJECT);
					 echo "<tr>";
					 echo "<td>".$sn."</td>";
					 echo "<td>".$data[0]."</td>";	
					 if(count($user)>0){
						 $cc_unsubscribed =get_user_meta($user[0]->ID, 'unsubscribe' , true );
						 echo "<td>Found</td>";     
						 if($_POST['drylive']=="live"){
							   update_user_meta($user[0]->ID, 'cc_user_deactivated',0);
							   update_user_meta($user[0]->ID, 'subs_expired',1);
							   update_user_meta($user[0]->ID, 'login_count',1);
							   sendEmailToUsers($user[0]->user_email);
							   if($cc_unsubscribed == 1){
								echo "<td>Unsusbcribed Mail Not Sent</td>";
							   }else{
							    echo "<td>Mail Sent</td>";
							   }
						  }else{
							  $cc_sent=get_user_meta($user[0]->ID, 'subs_expired' , true );
							  if($cc_unsubscribed == 1){
								  echo "<td>Unsubscribed</td>";
							  }else{
							   if($cc_sent==1){ echo "<td>Already Sent</td>";}else{echo "<td>Mail Can Be Sent</td>"; }
							  }
						   }
					   }else{
							echo "<td>User Not Found</td>";
							echo "<td>-</td>";
					   }
					 echo "</tr>";
					 $sn++;
			     }
			  }
			  echo "</table>";  
			  fclose($handle);
		}else{
			echo "Invalid file url!";
		}
	}

}

function sendEmailToUsers($to){
	$post_data = getlatestCommentary();
	$title = $date = $excerpt = '';
	if(!empty($post_data)){
		$post_data = json_decode($post_data);
		$title = $post_data->title;
		$date = $post_data->date;
		$excerpt = $post_data->excerpt;
	}
	$user = get_user_by( 'email', $to );
	$cc_unsubscribed =get_user_meta($user->ID, 'unsubscribe' , true );
	$name = get_user_meta( $user->ID, 'first_name', true );
	$userId = base64_encode($user->ID);
	$site_url = site_url();
	
	if(!$cc_unsubscribed){
		$subject = 'Jefferies - New Commentary Out';
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$message = '<body style="background-color:rgb(77,114,160,.2)"><table style="max-width:600px;margin:0 auto;font-family:sans-serif;width:100%"><thead><tr><td style="padding:15px;text-align:center;background-color:#fff"><img src="'.$site_url.'/wp-content/uploads/2018/06/Jefferies-Logo-Official-Black.png" alt="Jefferies" width="150" height="38" style="display:block;margin:0 auto"></td></tr></thead><tbody><tr><td style="padding:45px 15px;background-color:#4d73a0;text-align:center"><h3 style="color:#fff;font-size:20px;margin:0 0 30px">Don’t miss out on our latest commentaries<br>stay ahead of the curve with exclusive insights!</h3><h4 style="color:#fff;font-size:18px;margin:0 0 10px">'.$title.'</h4><p style="font-size:12px;color:#fff;margin:0 0 20px">'.$date.'</p><p style="font-size:16px;color:#fff;line-height:1.5">'.$excerpt.'</p></td></tr><tr><td style="padding:32px 28px 50px;background-color:#fff"><h3 style="color:#000;font-size:18px;margin:0">Renew Your Subscription for Premium Commentaries Today!</h3><div style="padding:10px">Dear '.$name.',<br>Your subscription for premium commentaries is due for renewal, and we wanted to remind you to take advantage of uninterrupted access to our exclusive content. Our team works tirelessly to deliver high-quality analysis, in-depth insights, and thought-provoking commentary on the latest trends and developments in the industry<br><br>By renewing your subscription, you’ll continue to enjoy:<ol><li>Access to exclusive commentaries and analysis.</li><li>Timely updates on key events and trends.</li><li>Expert insights from industry professionals.</li><li>Engaging content that adds value to your knowledge base.</li></ol><div style="text-align:center;margin-top:20px"><a href="'.$site_url.'/login/" style="background:#4d73a0;border-radius:4px;padding:14px 30px;color:#fff;font-size:16px;text-decoration:none;display:inline-block">Renew Now</a></div><div><strong>Thanks<br>Jefferies Team</strong></div><div style="width:100%;border-bottom:1px solid #ddd;margin:20px 0"></div><div style="display:block;text-align:center"><a href="#" style="display:inline-block;font-weight:700;color:#000;text-decoration:none">Need Help?</a><br><div>Please send any feedback or queries to</div><a href="mailto:support@jeffries.com">support@jeffries.com</a></div></div></td></tr></tbody><tfoot><tr><td style="text-align:center;padding:15px;font-size:13px"><a href="'.$site_url.'/unsubscribe?id='.$userId.'" style="color:#000">Unsubscribe</a><br><div style="margin-top:5px">© 2024 Jefferies Financial Group Inc.</div></td></tr></tfoot></table></body>';
		wp_mail($to, $subject, $message, $headers, array( '' ));
	}
}

function getlatestCommentary(){
	global $wpdb;
	$args = array(
  'numberposts' => 1,
  'post_type'   => 'post',
  'orderby'    => 'date',
  'sort_order' => 'desc'
);
$post_arr = array();
$latest_post = get_posts( $args );
if(!empty($latest_post)){
	$id = $latest_post[0]->ID;
	$post_arr['title'] = $latest_post[0]->post_title;
	$post_arr['date'] = date('F j, Y',strtotime($latest_post[0]->post_date));
	$excerpt = get_the_excerpt($id);
	$excerpt = substr($excerpt, 0, 260);
	$result = substr($excerpt, 0, strrpos($excerpt, ' '));
	$post_arr['excerpt'] = $result.'...';
}
return json_encode($post_arr);
}

?>