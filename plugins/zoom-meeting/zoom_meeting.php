<?php
/*
Plugin Name: Zoom Meeting
Description: A plugin to create zoom meeting and send links to subscribers email from excel file.
Author: Constant Concepts
Author URI: https://constantconcepts.io/
Version: 1.0
*/

require_once 'vendor/autoload.php';
 
use \Firebase\JWT\JWT;
use GuzzleHttp\Client;

$zoom_secret_key = get_option('zoom_secret_key');
$zoom_api_key = get_option('zoom_api_key');
	
define('ZOOM_API_KEY', $zoom_api_key);
define('ZOOM_SECRET_KEY', $zoom_secret_key);

function create_table() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'zoom';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		meeting_id bigint(20) NOT NULL,
		title varchar(100) DEFAULT '' NOT NULL,
		mdate varchar(100) DEFAULT '' NOT NULL,
		duration int(20) NOT NULL,
		password varchar(100) DEFAULT '' NOT NULL,
		url text NOT NULL,
		created_at date DEFAULT '0000-00-00' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

register_activation_hook( __FILE__, 'create_table' );

add_action('admin_menu', 'zoom_setup_menu');

function zoom_setup_menu(){
    add_menu_page( 'Zoom Meeting', 'Zoom Meeting', 'manage_options', 'zoom-meeting-setting', 'zoom_meeting_setting' );
	add_submenu_page( 'zoom-meeting-setting', 'All Meetings', 'All Meetings','manage_options', 'all-meetings', 'all_meetings');
	add_submenu_page( null, 'Send Invite', 'Send Invite','manage_options', 'send-invite', 'send_invite');
	add_submenu_page( 'zoom-meeting-setting', 'Create Meeting', 'Create Meeting','manage_options', 'zoom-meeting-create', 'zoom_meeting_create');
}

add_action('admin_enqueue_scripts','plugin_css_jsscriptss');
function plugin_css_jsscriptss() {
   wp_enqueue_style( 'style-css', plugins_url( '/css/style.css', __FILE__ ));
   wp_enqueue_style( 'datetimepicker', plugins_url( '/css/jquery.datetimepicker.min.css', __FILE__ ));
   // wp_enqueue_script( 'jquery-js', plugins_url( '/js/jquery.min.js', __FILE__ ),array('jquery'));
   wp_enqueue_script( 'script-js', plugins_url( '/js/script.js', __FILE__ ),array('jquery'));
   wp_enqueue_script( 'datetimepicker-js', plugins_url( '/js/jquery.datetimepicker.js', __FILE__ ),array('jquery'));
   wp_localize_script( 'script-js', 'plugin_ajax_object',array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
   
}

function zoom_meeting_setting(){
	
	if(isset($_POST['zoom_api_key'])){
		update_option('zoom_api_key',rtrim($_POST['zoom_api_key'],"/"));
	}
	if(isset($_POST['zoom_secret_key'])){
		update_option('zoom_secret_key',rtrim($_POST['zoom_secret_key'],"/"));
	}
	
	echo '<style type="text/css">.button-primary{margin-top:25px!important;} input{margin-bottom:10px !important;}</style>';
	$cc_zoom_api_key =get_option('zoom_api_key');
	$cc_zoom_secret_key =get_option('zoom_secret_key');
	echo '<div id="ccdecosec">';
		echo '<form method="POST">';
		echo '<h2>Enter Zoom API Key and Secret :</h2>';
		echo '<table>';
		 echo '<tr>';
			 echo '<td><label>Enter API key</label></td><td><input type="text" step="0.1" min="0" name="zoom_api_key" value="'.$cc_zoom_api_key.'" size="50" required></td>';
		 echo '</tr>';
		 
		  echo '<tr>';
			 echo '<td><label>Enter Secret Key</label></td><td><input type="text" step="0.1" min="0" name="zoom_secret_key" value="'.$cc_zoom_secret_key.'" size="50" required></td>';
		 echo '</tr>';
		 
		echo '</table>';
		echo '<input type="submit" value="Submit" name="btn-dw-submit" class="button-primary woocommerce-save-button"/>';
		echo '</form>';

		//echo '<h2>Note</h2>';
		//echo '<div>Please enter valid Zoom API Key and Secret key to proceed...</div>';
		echo '<h2>Steps for creating ZOOM API key  and Secret</h2>';
		echo '<ul>';
			echo '<li>1.Go to the Zoom Developer Dashboard and create a <a href="https://marketplace.zoom.us/develop/create" target="_blank">new app</a>.</li>';
			echo '<li>2.Choose JWT as the app type and copy the Zoom API key and secret.</li>';
			echo '<li>3.For reference check the screenshot <a href="https://prnt.sc/11wxx6x" target="_blank">here</a></li>';
		echo '</ul>';

	echo '</div>';
	
}

function all_meetings(){
	global $wpdb;
	
	$table = $wpdb->prefix . "zoom"; 
	//get all meetings
	$lists = $wpdb->get_results("SELECT * FROM $table order by id desc");
	echo '<div class="form_wrap listing">';
	echo '<h2>Meeting List:</h2>';
	echo '<a href="'.admin_url().'admin.php?page=zoom-meeting-create" class="button-primary">Add New Meeting</a><br/>';
	echo '<table>';
	if(!empty($lists)){
		echo '<tr><th>SNo</th><th>Title</th><th>Duration(min)</th><th>Date</th><th>Link</th><th>Password</th><th>Send Invite</th><th>Action</th></tr>';
		$i=1;
		foreach($lists as $list):
		
		echo '<tr>';
		    echo '<td>'.$i.'</td>';
			echo '<td>'.$list->title.'</td>';
			echo '<td>'.$list->duration.'</td>';
			echo '<td>'.$list->mdate.'</td>';
			echo '<td><a href="'.$list->url.'" target="_blank" title="'.$list->url.'" class="button-primary">Meet link</a></td>';
			echo '<td>'.$list->password.'</td>';
			echo '<td><a href="'.admin_url().'admin.php?page=send-invite&id='.$list->id.'" class="btn btn-success">Invite</a></td>';
			echo '<td><a href="javascript:;" class="btn btn-danger del_meeting" rel="'.$list->id.'">Delete</a></td>';
		echo '</tr>';
		
		$i++;
		endforeach;
	}
	echo '</table>';
	echo '</div>';
	
	echo '<style>.listing table {font-family: arial, sans-serif;border-collapse: collapse;width: 100%; margin-top:20px;}.listing td {border: 1px solid #dddddd;text-align: center;padding: 5px 10px;} .listing th{background:#4d73a0;border: 1px solid #dddddd;text-align: center;padding: 5px 10px; color:#fff; }</style>';
	
}

function send_invite(){
	global $wpdb;
	
	$meeting_id = 0;
	$target_dir = plugin_dir_path( __FILE__ ).'uploads/';
	if(isset($_GET['id'])){$meeting_id = $_GET['id'];}
	$filename_u = 'meet_'.$meeting_id.'.csv';

	echo '<div class="form_wrap">';
	echo '<h2>Send Zoom invite to users</h2>';
	
	echo '<ul>';
	echo '<li>Note: Please upload the CSV file with the users email to send invite link.</li>';
	echo '</ul>';
	
	echo '<form method="post" action="" enctype="multipart/form-data">
	<input type="file" name="doc"/>
	<input type="submit" name="submit" value="Upload" class="button-primary woocommerce-save-button"/>
	</form>';
	
	echo '</div>';
	
	if(isset($_FILES['doc']) && $_FILES['doc']['error'] == UPLOAD_ERR_NO_FILE) {
		echo '<div class="alert alert-danger">Please upload a file with CSV format</div>';
		die;
	} 
	
	if (isset($_FILES['doc']) && ($_FILES['doc']['error'] == UPLOAD_ERR_OK)) {
		
		$allowed = array('csv');
		$filename = $_FILES['doc']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if (!in_array($ext, $allowed)) {
			echo '<div class="alert alert-danger">Please upload a file with CSV format</div>';
			die;
		}
		
		$files = glob($target_dir.'/*');  
		foreach($files as $file) { 
			if(is_file($file)) {} 
			 $path_array = explode("/",$file);
			 $filename = end($path_array);
			 if($filename == $filename_u){
				unlink($file);   
			 }	  
		}
		$target_file = $target_dir . $filename_u;
		if (move_uploaded_file($_FILES["doc"]["tmp_name"], $target_file)) {
			echo "<div class='alert alert-success up_message'>The file ". htmlspecialchars( basename( $_FILES["doc"]["name"])). " has been uploaded, please click the 'Send Invitation' button below to continue.</div>";
			echo '<a href="javascript:;" class="button-primary" id="start_upload" rel="'.$meeting_id.'" >Send Invitation</a>';
			die;
		  } else {
			echo '<div class="alert alert-danger">Sorry, there was an error uploading your file.</div>';
			die;
		  }
	}
}

add_action( 'wp_ajax_sendInvites', 'sendInvites' );
add_action( 'wp_ajax_nopriv_sendInvites', 'sendInvites' );

function sendInvites(){
	
	global $wpdb;
	
	$meet_title = '';
	$meeting_date = '';
	$meeting_duration = '';
	$meeting_pass ='';
	$meeting_url = '';
	
	$meeting_id = $_POST['mid'];
	$meet_array  = get_meet_details($meeting_id);
	if(!empty($meet_array)){
		$meet_title = $meet_array->title;
		$meeting_date =  $meet_array->mdate;
		$meeting_duration = $meet_array->duration;
		$meeting_pass = $meet_array->password;
		$meeting_url = $meet_array->url;
	}
	
	//upload csv to path
	$target_dir = plugin_dir_path( __FILE__ ).'uploads/';
	$filename_u = 'meet_'.$meeting_id.'.csv';
	
	$path = $target_dir.$filename_u;
	$row = 1;
	$email_array = array();
	//reading CSV
	if (($handle = fopen($path, "r")) !== FALSE) {
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			//$num = count($data);
			if($row > 1){
				$email_array[] = $data[0];
			}
		 $row++;
		}
		fclose($handle);
	}
	
	//send mail
	if(!empty($email_array)){
		
		$to = array_filter($email_array);
		$subject = 'Jefferies - Zoom Meeting Invitation';
		$headers  = "From: zervos@jefferiesmacrostrategy.com\r\n" ."X-Mailer: php\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		$headers .= 'BCC: '. implode(",", $to) . "\r\n";
		
		 $message = '<html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta http-equiv="X-UA-Compatible" content="ie=edge"> <link rel="stylesheet" href="style.css"> <title>FIRM REGISTRATION EMAIL</title> </head> <body> <div style="background:#fff; width:100%;padding: 30px;"> <div class="logo" style="margin:20px 0; padding:0;"> <img style="width:150px;" src="https://jefferiesmacrostrategy.com/wp-content/uploads/2018/06/Jefferies-Logo-Official-Black.png" class="logo"> </div> <p>Hi Member,<br/> You have been invited for zoom meeting, Please refer to details below:</p> <p>Meeting Title: '.$meet_title.' <br/> Meeting Date: '.$meeting_date.' <br/> Meeting Duration: '.$meeting_duration.' minutes <br/> Meeting Password: '.$meeting_pass.' <br/><br/> Click <a href="'.$meeting_url.'">here</a> to join meeting.</p> Thanks<br/> Jefferies Team. </div> </div> </body> </html>';

		
		if(wp_mail( $to, $subject, $message, $headers, array( '' ) )){
			echo 'sent';
			die;
		}
		else{
			echo 'not sent';
			die;
		}
	}
	
}

function get_meet_details($meeting_id){
	global $wpdb;
	$table = $wpdb->prefix . "zoom"; 
	$meet_details = '';
	$meet = $wpdb->get_row("SELECT * FROM $table where id='".$meeting_id."'");
	if(!empty($meet)){
		$meet_details = $meet;
	}
	return $meet_details;
	
}


function zoom_meeting_create(){
	global $wpdb;
	
	echo '<div class="form_wrap">';
	echo '<h2>Fill the meeting details below:</h2>';
	
	echo '<form method="post" id="meeting_form" action="" enctype="multipart/form-data">
	<table>
	<tr><td><label>Meeting Title</label></td><td><input type="text" min="0" name="mtitle" id="mtitle" value="" size="50" required><span class="error" id="err_title"></span></td></tr>
	<tr><td><label>Meeting DateTime</label></td><td><input type="text" id="datetimepicker" min="0" name="mtime" value="" size="50" required><span class="error" id="err_date"></span></td></tr>
	<tr><td><label>Meeting Duration</label></td><td><input type="number" min="0" name="duration" id="duration" value="" size="50" required> minutes<span class="error" id="err_duration"></span></td></tr>
	<tr><td><label>Meeting Password</label></td><td><input type="password" min="0" name="pass" id="pass" value="" size="50" required><span class="error" id="err_pass"></span></td></tr>
	<tr><td></td><td><input type="button" value="Create Meeting" id="create_meeting" class="button-primary woocommerce-save-button"/></td></tr>
	</table>
	</form>';

	echo '</div>';
	echo '<div class="alert alert-success link_created">Meeting link created successfully! Click <a href="/wp-admin/admin.php?page=all-meetings">here</a> to see the meeting details and send invitation link to members.</div>';
	
	echo '<div class="alert alert-danger zoom_keys">Please enter valid ZOOM Api key and secret key to Create meeting, Click <a href="/wp-admin/admin.php?page=zoom-meeting-setting">here</a> to enter</div>';
	

	
}

add_action('wp_head','zoom_woo_style');
function zoom_woo_style(){
	echo '<style type="text/css">
	.ccloading{background: url("/wp-content/plugins/deco-woo-integration/images/loading.gif") 50% 50% no-repeat rgb(255,255,255);position: fixed;z-index: 9999;opacity: 1;left: 0px;top: 0px;width: 100%;height:100%;}
	#ccpmfname{margin-bottom:25px;} .woocommerce-cart table.cart img,.cc-ch-item-thumb img{width:150px;} .cc-ch-item-thumb{float:left;margin-right: 25px;}
	.woocommerce-checkout-review-order td.product-name{font-weight:600;} .woocommerce-checkout-review-order dl.variation{font-weight:normal;}
	.woocommerce-checkout-review-order dl.variation dt{float:none!important;} .wc-item-meta li{list-style:none;} 
	.wc-item-meta li strong.wc-item-meta-label{float:none!important;} .woocommerce-notices-wrapper + pre {display: none;}.error{color: red;margin-left: 10px;}
	</style>';
}

function getZoomAccessToken() {
    $key = ZOOM_SECRET_KEY;
    $payload = array(
        "iss" => ZOOM_API_KEY,
        'exp' => time() + 3600,
    );
    return JWT::encode($payload, $key);    
}


// Ajax import products
add_action( 'wp_ajax_createZoomMeeting', 'createZoomMeeting' );
add_action( 'wp_ajax_nopriv_createZoomMeeting', 'createZoomMeeting' );

function createZoomMeeting() {
	global $wpdb;
	$table = $wpdb->prefix . "zoom"; 
	
	$keys = check_keys();
	if($keys ==  'fail'){
		echo '2';
		die;
	}
	
	$title = (empty($_POST['title'])) ? '' : $_POST['title'];
	$date = (empty($_POST['date'])) ? '' : $_POST['date'];
	$duration = (empty($_POST['duration'])) ? '' : $_POST['duration'];
	$password = (empty($_POST['password'])) ? '' : $_POST['password'];
	$created = date('Y-m-d');
	
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://api.zoom.us',
    ]);
 
    $response = $client->request('POST', '/v2/users/me/meetings', [
        "headers" => [
            "Authorization" => "Bearer " . getZoomAccessToken()
        ],
        'json' => [
            "topic" => $title,
            "type" => 2,
            "start_time" => $date,
            "duration" => $duration, // 30 mins
            "password" => $password
        ],
    ]);
 
    $data = json_decode($response->getBody());
	if(!empty($data)){
		$meeting_id = $data->id;
		$meeting_uid = $data->uuid;
		$res = $wpdb->query("INSERT INTO $table (meeting_id,title,mdate,duration,password,url,created_at) VALUES ('$meeting_id','$title', '$date','$duration','$password','$data->join_url','$created')"  );
		if($res > 0){
			echo 1;
			die;
		}else{
			echo 0;
			die;
		}
	}
	
}

function listZoomMeeting(){
	
$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
$response = $client->request('GET', '/v2/users/me/meetings', [
    "headers" => [
        "Authorization" => "Bearer ". getZoomAccessToken()
    ]
]);
 
$data = json_decode($response->getBody());
 
if ( !empty($data) ) {
    foreach ( $data->meetings as $d ) {
        $topic = $d->topic;
        $join_url = $d->join_url;
        echo "<h3>Topic: $topic</h3>";
        echo "Join URL: $join_url";
    }
}
	
}

add_action( 'wp_ajax_deleteMeeting', 'deleteMeeting');
add_action( 'wp_ajax_nopriv_deleteMeeting', 'deleteMeeting');
function deleteMeeting(){
	global $wpdb;
	
	$meeting_id = $_POST['mid'];
	$meet = get_meet_details($meeting_id);
	$zoom_meet_id = $meet->meeting_id;
	if($zoom_meet_id!=0){
		deleteZoomMeeting($zoom_meet_id);
	}
	// delete query wordpress
	
	$table = $wpdb->prefix . "zoom"; 
	$sql="delete from $table where id=$meeting_id";
	$res = $wpdb->query($sql);
	if($res){echo '1'; die;
	}else{
		echo '0';die;
	}
}

function deleteZoomMeeting($meeting_id) {
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'https://api.zoom.us',
    ]);
 
    $response = $client->request("DELETE", "/v2/meetings/$meeting_id", [
        "headers" => [
            "Authorization" => "Bearer " . getZoomAccessToken()
        ]
    ]);
 
    if (204 == $response->getStatusCode()) {
       // echo "Meeting deleted.";
    }
}

function check_keys(){
	$zoom_secret_key = get_option('zoom_secret_key');
	$zoom_api_key = get_option('zoom_api_key');
	
	if(!empty($zoom_secret_key) && !empty($zoom_api_key)){
		$keys = 'success';
	}else{
		$keys = 'fail';
	}
	return $keys;
}
 
//deleteZoomMeeting('MEETING_ID_HERE');


register_deactivation_hook( __FILE__, 'myplugin_deactivate' );
function myplugin_deactivate(){
	delete_option( 'zoom_api_key' );
	delete_option( 'zoom_secret_key' );
}

?>