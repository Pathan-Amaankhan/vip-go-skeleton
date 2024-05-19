<?php
/**
 * Plugin Name: CC Deactivate Users
 * Plugin URI: http://constantconcepts.io/
 * Description: You can Activate / Deactivate individual users by changing User Status on EDIT PROFILE page OR you can Bulk Deactivate users using a csv file.
 * Version: 1.1
 * Author: Constant Concepts
 * Author URI: http://constantconcepts.io/
 **/

 function cc_decusersmenu(){
   add_menu_page('CC Deactivate USers','Deactivate Users','manage_options','cc_du_home','cc_du_home','');
   add_submenu_page( 'cc_du_home', 'Bulk Deactivate Users', 'Bulk / CSV','manage_options', 'cc_du_csv','cc_du_csv');
   add_submenu_page( 'cc_du_home', 'Deactivate User Settings', 'Setting','manage_options', 'cc_du_settings','cc_du_settings');
 }

 add_action('admin_menu','cc_decusersmenu');


 function cc_du_home(){
	echo '<h2>DEACTIVATE USERS</h2>
		 <p>You can Activate / Deactivate individual users by changing User Status on EDIT PROFILE page OR you can <a href="/wp-admin/admin.php?page=cc_du_csv">Bulk Deactivate</a> users using a csv file</p>
	';
 }


 function cc_du_csv(){
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


	echo '
		<style type="text/css">
			body{font-family:arial;font-size:14px;}.note{color:#ff0000;font-style:italic;}form{margin:20px 0px 50px 0px;text-align:center;}
			.drylive{margin:0px 2px!important;}#csvUrl{width:60%;margin:0px 10px;}input{padding:5px;}label{margin:0px 15px;}th,td{text-align:left;padding:5px;}
			.ccplset{margin-top:50px;}
		</style>
		<form method="POST" id="ccdelusers" name="ccdelusers">
			<h3>Bulk Deactivate Users Using CSV file</h3>
			<p class="noteset">Step 1: Upload your csv file to Media.</p>
			<p class="noteset">Step 2: Copy full csv path from Media and paste into url box below.</p>
			<p class="note">NOTE: There should be only 1 column in csv file containing email addresses of users to be deleted.</p>
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
					if(jQuery("#dllive").is(":checked")){ return confirm("Are you sure you want to DEACTIVATE all users from CSV file ?");	}
				});
			});
		</script>
	';

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
		 
						 echo "<td>Found</td>";     
						 if($_POST['drylive']=="live"){
							   update_user_meta($user[0]->ID, 'cc_user_deactivated',1);
							   wp_update_user(array( 'ID' => $user[0]->ID,'role' => ''));

							   echo "<td>DEACTIVATED</td>";
						  }else{
							  $cc_is_deactivated=get_user_meta($user[0]->ID, 'cc_user_deactivated' , true );
							  if($cc_is_deactivated==1){
									echo "<td>Already Deactivated</td>";
							  }else{
									echo "<td>Can Be Deactivated</td>";
							  }
						   }
					   }else{
							echo "<td>Not Found</td>";
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


function cc_du_settings(){
	if(isset($_POST['cc_du_message']) && !empty($_POST['cc_du_message'])){
		update_option("cc_du_message", $_POST['cc_du_message']);
		echo '<div class="notice notice-success is-dismissible"> 
				<p><strong>Settings saved!.</strong></p>
	    </div>';
	}

	echo '
	<style type="text/css">
			body{font-family:arial;font-size:14px;}.note{color:#ff0000;font-style:italic;}form{margin:20px 0px 50px 0px;text-align:center;}
			.drylive{margin:0px 2px!important;}#cc_du_message{width:60%;margin:0px 10px;}input{padding:5px;}label{margin:0px 15px;}th,td{text-align:left;padding:5px;}
			.ccplset{margin-top:50px;}
		</style>
		<form method="POST" id="ccplset name="ccplset">
			<h2>Custom Error Message</h2>
			<p class="noteset">Custom error message to be shown on login screen if deactivated user try to login.</p>
			<strong>Custom Error Message</strong>
			<input type"text" name="cc_du_message" id="cc_du_message" value="'.get_option("cc_du_message").'" title="Enter custom error message here" placeholder="Enter Custom Error Message" required>
			<input type="submit" value="SUBMIT" name="btnSubmit">
		</form>	
	';

}


add_action( 'edit_user_profile', 'cc_deactivate_fields' );
 function cc_deactivate_fields($user){
    echo '<h3 class="heading">User Status</h3>';
    $cc_is_deactivated=get_user_meta($user->ID, 'cc_user_deactivated' , true );
	$cc_dctselsts="";
	if($cc_is_deactivated==1){$cc_dctselsts="selected";}
	echo  '
    <table class="form-table">
	<tr>
        <th><label for="contact">User Status</label></th>
 	    <td>
		<select class="form-control" name="cc_deactivated" id="cc_deactivated">
			<option value="0">ACTIVE</option>
			<option value="1" '.$cc_dctselsts.'>DEACTIVATED</option>
		</select>
        </td>
 	</tr>
    </table>';    
}

add_action( 'edit_user_profile_update', 'cc_save_deactivate_fields' );
function cc_save_deactivate_fields($user_id){		
    $cc_status = $_POST['cc_deactivated'];
	update_user_meta($user_id,'cc_user_deactivated', $cc_status);
}



add_action( 'init', 'cc_logout_user' );
 
function cc_logout_user() {
	if(is_user_logged_in()){
		global $current_user;
		$user_id =  $current_user->ID;
		$cc_is_deactivated = get_user_meta($user_id, 'cc_user_deactivated',true);
		if($cc_is_deactivated==1) { wp_logout();  wp_redirect(site_url());	exit; }
	}
}

function cc_check_status( WP_User $user ) {
    $cc_is_deactivated = get_user_meta( $user->ID, 'cc_user_deactivated',true);
    if($cc_is_deactivated==1) {
		$cc_du_message = get_option("cc_du_message");
		if(empty($cc_du_message)){ $cc_du_message="Your account is DEACTIVATED, please contact site Admin!";}
        $message = esc_html__( $cc_du_message, 'text-domain');
        return new WP_Error( 'user_not_verified', $message );
    }
 
    return $user;
}
 
add_filter( 'wp_authenticate_user', 'cc_check_status' );
?>