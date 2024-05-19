<?php
function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
 //wp_enqueue_script( 'password-strength-meter' );
 //wp_enqueue_script( 'password-strength-meter-mediator', get_stylesheet_directory_uri() . '/js/password-strength-meter-mediator.js', array('password-strength-meter') ,'1.4');
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
add_shortcode('ts_reset_pass_link','ts_reset_pass_link');
function ts_reset_pass_link($atts){
 global $wpdb;
 $a = shortcode_atts( array(
 'user_id' => 'user_id'
 ), $atts );

 $user = new MeprUser($a['user_id']);
 $reset_password_url = $user->reset_password_link();
 return '<a href="'.$reset_password_url.'">Set your password</a>';
}
//add_action( 'mepr-reset-password-after-password-fields', 'addCaptchResetPass' );
//add_action( 'mepr-forgot-password-form', 'addCaptchResetPass' );
//add_action( 'mepr-login-form-before-submit', 'addCaptchResetPass' );
function addCaptchResetPass(){
	echo do_shortcode("[bws_google_captcha]");
}

function blogrollbyyear($atts){
		extract(shortcode_atts(array(
		'year' => date('Y'),
		), $atts));		

		$args = array(
	    'post_type'   => 'post',
		'order'       => 'DESC',
        
		'date_query'  => array(
			array(
			   'year' => $year,
			),
			),
		'numberposts' =>'-1',
        'category__not_in' => array(16),
		);

		$posts = get_posts( $args );
		

		$bhtmloutput="";

		if(count($posts)){
		$bhtmloutput="<div id='jefferiesblogpostwrap'>";
			$sn=1;
			foreach($posts as $p){
				$bhtmloutput.= '<article id="post-'.$p->ID.'" class="et_pb_post clearfix et_pb_no_thumb post-249130 post type-post status-publish format-standard hentry category-uncategorized">';
				$bhtmloutput.= '<h2 class="entry-title"><a href="'.esc_url(get_permalink($p->ID)).'">'.$p->post_title.'</a></h2>';
				$bhtmloutput.= '<p class="post-meta"> <span class="published">'.get_the_date('M j, Y', $p->ID ).'.</span> </p>';
				$bhtmloutput.=  '<div class="post-content"><div class="post-content-inner"><p>'.get_the_excerpt($p->ID).'</p></div><a href="'.esc_url(get_permalink($p->ID)).'" class="more-link">read more</a></div>';
				$bhtmloutput.=  '</article>';
				if($sn>=6){break;}
			$sn++;
			}
		
	
		$bhtmloutput.='</div>';
		$bhtmloutput.='<div class="pagination clearfix">';
		$bhtmloutput.='<div class="alignleft"><a href="javascript:void(0);" class="updateblogposts" rel="0" id="showolderentriesprev">&#171; Older Entries</a></div>';
		$bhtmloutput.='<div class="alignright"><a href="javascript:void(0);" class="updateblogposts" rel="1" id="showolderentriesnext" style="display:none;">Next Entries &#187;</a></div>';
		$bhtmloutput.='<input type="hidden" value="'.count($posts).'" id="totalCount"/>';
		$bhtmloutput.='</div>';

		$bhtmloutput.="
		<script type='text/javascript'>
		var	jblimit=parseInt(0);
		jQuery(document).ready(function($) {
			if(parseInt(jQuery('#totalCount').val())<=6){jQuery('#showolderentriesprev').hide();}
			jQuery('.updateblogposts').click(function(){
				var prevnext=parseInt(jQuery(this).attr('rel'));
				jQuery('#jefferiesblogpostwrap').css('pointer-events','none');
				jQuery('#jefferiesblogpostwrap').css('opacity','0.2');
				jQuery('.pagination').css('pointer-event','none');

				if(!prevnext){jblimit+=6;}else{jblimit-=6;}
				var data = {
					action: 'jefferiesajaxload',
			        jblimit: jblimit,
					jbyear  : ".$year.",
				};
				var ajaxurl = '/wp-admin/admin-ajax.php'; 
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#jefferiesblogpostwrap').css('pointer-events','inherit');
					jQuery('#jefferiesblogpostwrap').css('opacity','inherit');
					jQuery('.pagination').css('pointer-event','inherit');
					jQuery('#jefferiesblogpostwrap').html(response).show('slow');
					if(!jblimit){ jQuery('#showolderentriesnext').hide(); }
					if(jblimit>0){jQuery('#showolderentriesnext').show();}
					if(jblimit+6>=parseInt(jQuery('#totalCount').val())){jQuery('#showolderentriesprev').hide();}
					if(jblimit+6<parseInt(jQuery('#totalCount').val())){jQuery('#showolderentriesprev').show();}
					jQuery('html, body').animate({ scrollTop: (jQuery('#main-content').offset().top)},500);
				});
			});
		});
		</script>
		<style type='text/css'>.pagination{margin-bottom:30px;}</style>
		";		

		}else{
				$bhtmloutput.= "<div id='nopofnd' class='no_data'>No Commentaries found!</div>'<style type='text/css'>#nopofnd{min-height:500px;}</style>";
		}


	
	return 	$bhtmloutput;			
}


add_shortcode('jefferiesblog','blogrollbyyear');


function loadjefferiesblogposts() {
	    global $wpdb; 

		$limit=intval( $_POST['jblimit'] );
		$year=intval( $_POST['jbyear'] );

		$args = array(
	    'post_type'   => 'post',
		'order'       => 'DESC',
		'date_query'  => array(
			array(
			   'year' => $year,
			),
			),
		'numberposts' =>'-1',
        'category__not_in' => array(16),
		);

		$posts = get_posts( $args );

	
		if(count($posts)){
			$sn=1;
			foreach($posts as $p){
				if($sn>$limit&&$sn<=$limit+6){
				$bhtmloutput.= '<article id="post-'.$p->ID.'" class="et_pb_post clearfix et_pb_no_thumb post-249130 post type-post status-publish format-standard hentry category-uncategorized">';
				$bhtmloutput.= '<h2 class="entry-title"><a href="'.esc_url(get_permalink($p->ID)).'">'.$p->post_title.'</a></h2>';
				$bhtmloutput.= '<p class="post-meta"> <span class="published">'.get_the_date('M j, Y', $p->ID ).'.</span> </p>';
				$bhtmloutput.=  '<div class="post-content"><div class="post-content-inner"><p>'.get_the_excerpt($p->ID).'</p></div><a href="'.esc_url(get_permalink($p->ID)).'" class="more-link">read more</a></div>';
				$bhtmloutput.=  '</article>';
				}
			$sn++;
			}
		}else{
				$bhtmloutput.= "<div class='no_data'>No Commentaries found!</div>";
		}

		echo $bhtmloutput;


    die(); // this is required to return a proper result
}

add_action( 'wp_ajax_jefferiesajaxload', 'loadjefferiesblogposts' );
add_action('wp_ajax_nopriv_jefferiesajaxload', 'loadjefferiesblogposts');

function post_published_notification( $ID, $post ) {
	$cat_array = array();
	$categories = get_the_category($ID);
	if(!empty($categories)){
		foreach($categories as $cat):
			$cat_array[] = $cat->slug;
endforeach;
	}
	if(in_array("media-highlights", $cat_array)){return;}
	$current_user = wp_get_current_user();
	wp_enqueue_style( 'style.css', get_stylesheet_uri() );

	//include ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/cc-post-to-pdf/mpdf/mpdf.php');
	include ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/cc-post-to-pdf/vendor/autoload.php');

	$marginLeft=15;
	$marginRight=15;
	$marginTop=6;
	$marginBottom=8;
	$marginHeader=9;

	$marginFooter=9;
	$pageLayout='post';
	$pageSize='A4';

	$usersitelogoleft ="<img src='".site_url()."/wp-content/plugins/cc-post-to-pdf/images/jeflogo-pdf-lt.png' style='float:left;height:40px;'>";
	$usersitelogoright="<img src='".site_url()."/wp-content/plugins/cc-post-to-pdf/images/jeflogo-pdf-rt.png' style='float:right;height:30px;'>";

	$header_section="<p style='padding-bottom:15px;border-bottom:1px solid #000;'>".$usersitelogoleft.$usersitelogoright."</p>";		
	$footer_section="<p id='footertext'><strong>David Zervos, Chief Market Strategist</strong></p>"; 
	$titleClean = apply_filters( 'post_to_pdf_title', apply_filters( 'the_title', $post->post_title, $post->ID ), $post );
	$postTitle= '<div class="entry-header"><h1 class="entry-title">'. $titleClean . ' - ' . get_the_time('m/d/Y', $post->ID).'</h1></div>';

	$post_content = apply_filters( 'post_to_pdf_content', $post->post_content, $post );
	$post_content = apply_filters( 'post_to_pdf_print_content', $post_content );
	$post_content = apply_filters( 'the_content', $post_content );
	$shortcodes = implode( '|', apply_filters( 'post_to_pdf_print_remove_shortcodes', array( 'vc_', 'az_' ) ) );
	$post_content = preg_replace( "/\[\/?({$shortcodes})[^\]]*?\]/", '', $post_content );

	$postContent='<div class="entry-content">'. $post_content .'</div>';

    $html='<style type="text/css">p{line-height:18px;}h1{font-size:25px;}a{color:#000;text-decoration:none;}a:hover{text-decoration:none;}#footertext{font-size:12px;text-align:center;font-family:arial;color:#5f5c5c;}</style><div id="content"><div class="post" style="font-family:arial;font-size:12px;">';
	$html.=$postTitle;
	$html.=$postContent;
    $html.='</div></div>';
	//$mpdf=new mPDF('+aCJK',$pageSize.$pageLayout,0,$default_font,$marginLeft,$marginRight,$marginTop,$marginBottom,$marginHeader,$marginFooter);
	
	$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8',]);
	
	$mpdf->AliasNbPages( '{PAGETOTAL}' );
	$mpdf->setAutoTopMargin = 'pad';
	$mpdf->setAutoBottomMargin = 'pad';
	$mpdf->autoMarginPadding = 10;
	$mpdf->allow_charset_conversion = true;
	$mpdf->charset_in = get_bloginfo( 'charset' );

	$mpdf->autoScriptToLang = true;
	$mpdf->autoLangToFont = true;
	$mpdf->baseScript = 1;
	$mpdf->autoVietnamese = true;
	$mpdf->autoArabic = true;

	$mpdf->SetTitle($post->post_title);
    $mpdf->SetHTMLHeader($header_section);
	$mpdf->SetHTMLFooter($footer_section);

	//$mpdf->showWatermarkText = true;
	//$mpdf->watermarkTextAlpha='0.2';
	//$mpdf->SetWatermarkText('Demo Text');

	$mpdf->WriteHTML($html);
	//$file_name =$post->post_title.'.pdf';
	//$mpdf->Output($file_name,'I');
	$filename=$_SERVER['DOCUMENT_ROOT'].'/wp-content/uploads/postpdf/'.$post->post_name.'-'.$post->post_date.'.pdf';
	$mpdf->Output($filename,'F');
    

    $to = "kroberto@jefferies.com";
    $subject = "New Post Published";
    $message = "A new post has been published, please find attached pdf.";
    $headers[] = "Content-Type: text/html; charset=UTF-8";
    $headers[] = "Bcc: <jcardilli@jefferies.com>";
    $headers[] = "Bcc: <dzervos@jefferies.com>";
    $headers[] = "Bcc: <jamangoua@jefferies.com>";
    $headers[] = "Bcc: <macrostrategysupport@jefferies.com>";
    $headers[] = "Cc: <scott@constantconcepts.io>";
	
	$attachments = array($filename);
    wp_mail( $to, $subject, $message, $headers,$attachments );
}
add_action( 'publish_post', 'post_published_notification', 10, 2 );


add_filter( 'style_loader_src',  'sdt_remove_ver_css_js', 9999, 2 );
add_filter( 'script_loader_src', 'sdt_remove_ver_css_js', 9999, 2 );

function sdt_remove_ver_css_js( $src, $handle ) 
{
    $handles_with_version = [ 'style' ]; // <-- Adjust to your needs!

    if ( strpos( $src, 'ver=' ) && ! in_array( $handle, $handles_with_version, true ) )
        $src = remove_query_arg( 'ver', $src );

    return $src;
}

add_action( 'wp_ajax_get_firm', 'get_firm' );
add_action('wp_ajax_nopriv_get_firm', 'get_firm');

function get_firm() {
	global $wpdb;
	$domain = '';
	$firm_arr = array();
	$email = $_POST['email'];
	$domain_array = explode("@",$email);
	if($domain_array[1]){$domain = $domain_array[1];}
	
	$table = $wpdb->prefix . "usermeta"; 
	$res = $wpdb->get_row("SELECT user_id FROM $table where meta_value LIKE '%".$domain."'");
	if(!empty($res)){
			$firm_name = get_user_meta( $res->user_id, 'mepr_firm', true );
	}else{
		$firm_name = 0;
	}
	echo $firm_name;
	die;
}

//send mail after signup when new firm got registered.
function mepr_capture_new_member_signup_completed($event) {
	global $wpdb;
  $user = $event->get_data();
  //$txn_data = json_decode($event->args);
  //Do what you need
  
  $user_id = $user->ID;
 
  if($user_id > 0){
	  $user_data = get_userdata($user_id);
	  $firm_name = get_user_meta( $user_id, 'mepr_firm', true );
	  $user_first = get_user_meta( $user_id,'first_name', true ); 
	  $user_last = get_user_meta( $user_id,'last_name', true );
	  $user_name = $user_first.' '.$user_last;
	  $user_email =  $user_data->user_email;
	  
	  $table = $wpdb->prefix . "usermeta"; 
	  $res = $wpdb->get_results("SELECT * FROM $table where (meta_key='mepr_firm' AND meta_value = '".$firm_name."') AND user_id!='".$user_id."'");
	  
	  if(!empty($res)){
		  // not send email
	  }else{
		  //send email
		    //$to = 'macrostrategysupport@jefferies.com';
			$to = 'ethan@constantconcepts.vegas';
			$subject = 'New Firm Registration';
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			$message = '<html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta http-equiv="X-UA-Compatible" content="ie=edge"> <link rel="stylesheet" href="style.css"> <title>FIRM REGISTRATION EMAIL</title> </head> <body> <div style="background:#fff; width:100%;padding: 30px;"> <div class="logo" style="margin:20px 0; padding:0;"> <img style="width:150px;" src="https://jefferiesmacrostrategy.com/wp-content/uploads/2018/06/Jefferies-Logo-Official-Black.png" class="logo"> </div> <p>Hi Admin,<br/> New Firm is registered on the site, Please refer to details below:</p> <p>Firm Name: '.$firm_name.' <br/> User Name: '.$user_name.' <br/> User Email: '.$user_email.'</p> Thanks<br/> Jefferies Team. </div> </div> </body> </html>';
			wp_mail($to, $subject, $message, $headers, array( '' ));
	  }
  }
   //update_user_meta( $user_id, 'login_count', 1 );
  
}
add_action('mepr-event-member-signup-completed', 'mepr_capture_new_member_signup_completed');

function mepr_capture_new_member_added($event) {
  $user = $event->get_data();
  
  $user_id = $user->ID;
  //update_user_meta( $user_id, 'login_count', 0 );
  //Do what you need
}
//add_action('mepr-event-member-added', 'mepr_capture_new_member_added');

add_filter( 'rest_authentication_errors', function( $result ) {
	  if ( ! empty( $result ) ) {
		      return $result;
		        }
	    if ( ! is_user_logged_in() || ! is_admin() ) {
		        return new WP_Error( 'rest_not_logged_in', 'You are not currently logged in.', array( 'status' => 401 ) );
			  }
	    return $result;
});

function getClientMeta($user_id,$meta_key){
global $wpdb;
	$value = $wpdb->get_row("SELECT meta_value FROM  wp_usermeta where user_id='".$user_id."' AND meta_key='".$meta_key."'");
	if($value){
		return $value->meta_value;
	}else{
		return false;
	}
}

function wpf_dev_process_complete( $fields, $entry, $form_data, $entry_id ) {
      
	//Employee form entry
    if ( absint( $form_data[ 'id' ] ) == 208662 ) {
		// Get the full entry object
		$entry = wpforms()->entry->get( $entry_id );
		// Fields are in JSON, so we decode to an array
		$entry_fields = json_decode( $entry->fields, true );
		$email = $entry_fields[1]['value'];
		$membership_id = $entry_fields[11]['value'];
		$user = get_user_by( 'email', $email );
		$userId = $user->ID;
		$user->add_role( 'jef_employees' );
		addEmployeeMembership($userId,$membership_id);
	}
	
	//Client Form entry
	 if ( absint( $form_data[ 'id' ] ) == 208652 ) {
		// Get the full entry object
		$entry = wpforms()->entry->get( $entry_id );
		// Fields are in JSON, so we decode to an array
		$entry_fields = json_decode( $entry->fields, true );
		$email = $entry_fields[7]['value'];
		$membership_id = $entry_fields[18]['value'];
		$user = get_user_by( 'email', $email );
		$userId = $user->ID;
		$user->add_role( 'jef_clients' );
		addClientMembership($userId,$membership_id);
	}
 
}
add_action( 'wpforms_process_complete', 'wpf_dev_process_complete', 10, 4 );

function addEmployeeMembership($user_ID,$membership_id){
		global $wpdb;
			//update membership ID in members table

			// Create transaction
            $txn = new MeprTransaction();
            $txn->amount = 0;
            $txn->total = 0;
            $txn->user_id = $user_ID;
            $txn->product_id = $membership_id;
            $txn->status = MeprTransaction::$complete_str;
            $txn->txn_type = MeprTransaction::$payment_str;
            $txn->gateway = 'manual';
            //$txn->expires_at = gmdate('Y-m-d 23:59:59', (time() + MeprUtils::months(1)));
            $txn->subscription_id = 0;
            $txn->store();
}

function addClientMembership($user_ID,$membership_id){
		global $wpdb;
	
			/*$member = new MeprUser();
			$member->ID = $user_ID;
			$member->memberships = 207971;*/
			//$member->update_member_data();
	        /*$sub = new MeprSubscription();
            $sub->user_id = $user_ID;
            $sub->product_id = 123;
            $sub->price = 12.99;
            $sub->total = 12.99;
            $sub->period = 1;
            $sub->period_type = 'months';
            $sub->status = MeprSubscription::$active_str;
            $sub_id = $sub->store();*/
			
			// Create transaction with nil amount
            $txn = new MeprTransaction();
            $txn->amount = 0;
            $txn->total = 0;
            $txn->user_id = $user_ID;
            $txn->product_id = $membership_id;
            $txn->status = MeprTransaction::$complete_str;
            $txn->txn_type = MeprTransaction::$payment_str;
            $txn->gateway = 'manual';
            //$txn->expires_at = gmdate('Y-m-d 23:59:59', (time() + MeprUtils::months(1)));
            $txn->subscription_id = 0;
            $txn->store();
}

function getMemberMeta($id){
global $wpdb;
	$value = $wpdb->get_row("SELECT * FROM  wp_mepr_events where id='".$id."'");
	if($value){
		return date("F j, Y", strtotime($value->created_at));
	}else{
		return false;
	}
}

function getTransactionsDate($user_id,$membership_id){
	global $wpdb;
	//echo "SELECT * FROM wp_mepr_transactions where user_id='".$user_id."' AND product_id='".$membership_id."'";
	$first_payment_date = '';
	$last_payment_date = '';
	$transaction_date = '';
	$data = $wpdb->get_results("SELECT * FROM wp_mepr_transactions where user_id='".$user_id."' AND product_id='".$membership_id."'");
	$transaction_arr = array();
	if(isset($data)){
		foreach($data as $value){
			$transaction_arr[] = date("d/m/Y", strtotime($value->created_at) );
		}
		$first_payment_date = $transaction_arr[0];
		$last_payment_date = end($transaction_arr);
		$transaction_date = $first_payment_date.'-'.$last_payment_date;
	}
	return $transaction_date;
}

function getSubscriberPaymentStatus($user_id,$membership_id){
	global $wpdb;
	//echo "SELECT * FROM wp_mepr_transactions where user_id='".$user_id."' AND product_id='".$membership_id."'";
	$expiry_date = '';
	$date = date('Y-m-d');
	//$date = '2023-03-05';
	$status = '';
	$data = $wpdb->get_results("SELECT * FROM wp_mepr_transactions where user_id='".$user_id."' AND product_id='".$membership_id."'");
	$transaction_arr = array();
	$status = 'Up to Date';
	
	if(isset($data)){
		foreach($data as $value){
			//$transaction_arr[] = date("Y-m-d", strtotime($value->expires_at) );
			$transaction_arr[] = $value->expires_at;
			
		}
		$expiry_date = end($transaction_arr);
		
		$expiry_date_format = date("Y-m-d", strtotime($expiry_date) );
		if($expiry_date == '0000-00-00 00:00:00'){
			$status = 'Never';
		}else{
			$last_renewal_date = date('Y-m-d', strtotime($expiry_date_format. ' + 1 month'));
			if($date > $last_renewal_date){
				$status = 'Expired';
			}
			if(($date < $last_renewal_date) && ($date > $expiry_date_format)){
				$status = 'Pending Renewal';
			}
		}
	}
	return $status;
}

function set_blog_session($user_login){
    $user_obj = get_user_by('login', $user_login );
    $user_id = $user_obj->ID;
	$membership_id = 260192;
	$key = 'subs_expired';
	$single = true;
	$subs_check = get_user_meta( $user_id, $key, $single ); 
	$user = new MeprUser( $user_id );
	$active = $user->is_active_on_membership( $membership_id );
	//$already_subscribed = $user->is_already_subscribed_to( $membership_id );
	$pending_status = getPendingTransactionStatus($user_id);
	//$users_memberships = $user->active_product_subscriptions($membership_id);
	$users_memberships = $user->active_product_subscriptions();
	
	if( $login_amount = get_user_meta( $user_id, 'login_count', true ) ){
        // They've Logged In Before, increment existing total by 1
		$logged = 1;
        update_user_meta( $user_id, 'login_count', ++$login_amount );
    } else {
        // First Login, set it to 1
		$logged = 0;
        update_user_meta( $user_id, 'login_count', 1 );
    }
	
	/*if($subs_check && $pending_status == 1 && $logged == 1){
		wp_redirect("/activate-subscriptions/");
		exit;
	}*/
	
	if($subs_check && $pending_status == 1){
		wp_redirect('/');
		exit;
	}
	
	if($logged > 0){
		if($subs_check || (!$active && $pending_status == 0)){
			wp_redirect("/activate-subscriptions/");
			exit;
		}
	}
	
	/*if($subs_check || (!$active && $already_subscribed)){
		wp_redirect("/activate-subscriptions/");
		exit;
	}*/
}
add_action('wp_login', 'set_blog_session');

/**
 * Generates custom logout URL
 */
function getLogoutUrl($redirectUrl = ''){
    if(!$redirectUrl) $redirectUrl = site_url();
    $return = str_replace("&amp;", '&', wp_logout_url($redirectUrl));
    return $return;
}

/**
 * Bypass logout confirmation on nonce verification failure
 */
function logout_without_confirmation($action, $result){
    if(!$result && ($action == 'log-out')){ 
        wp_safe_redirect(getLogoutUrl()); 
        exit(); 
    }
}
add_action( 'check_admin_referer', 'logout_without_confirmation', 1, 2);

//add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
$redirection_url = '/';
  wp_redirect( $redirection_url );
  exit();
}

add_shortcode("cc_subscription", "cc_subscription");  
function cc_subscription( $atts ){  
	ob_start();
	include('./wp-content/themes/Constant-Concepts-Child-Theme-Divi/page-template-sub.php');
	$stringa = ob_get_contents();
	ob_end_clean();
	return $stringa;
}

add_shortcode("cc_thank_you", "cc_thank_you");  
function cc_thank_you( $atts ){  
	ob_start();
	include('./wp-content/themes/Constant-Concepts-Child-Theme-Divi/page-template-thank-you.php');
	$stringa = ob_get_contents();
	ob_end_clean();
	return $stringa;
}

add_shortcode("cc_unsubscribe", "cc_unsubscribe");  
function cc_unsubscribe( $atts ){  
	ob_start();
	include('./wp-content/themes/Constant-Concepts-Child-Theme-Divi/page-template-unsubscribe.php');
	$stringa = ob_get_contents();
	ob_end_clean();
	return $stringa;
}

function get_website_terms(){
	
	$my_postid = 207048;//This is page id or post id
	$content_post = get_post($my_postid);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}

function getCurrentUserData(){
	$user_id = get_current_user_id();
	$user_info = get_userdata($user_id);
	$single = true;
	$user_arr = array();
	$user_arr['first_name'] = get_user_meta( $user_id, 'first_name', $single ); 
	$user_arr['last_name'] = get_user_meta( $user_id, 'last_name', $single );
	$user_arr['email'] = $user_info->user_email;
	$user_arr['address_1'] = get_user_meta( $user_id, 'mepr-address-one', $single );
	$user_arr['address_2'] = get_user_meta( $user_id, 'mepr-address-two', $single );
	$user_arr['city'] = get_user_meta( $user_id, 'mepr-address-city', $single );
	$user_arr['country'] = get_user_meta( $user_id, 'mepr-address-country', $single );
	$user_arr['state'] = get_user_meta( $user_id, 'mepr-address-state', $single );
	$user_arr['zip'] = get_user_meta( $user_id, 'mepr-address-zip', $single );
	$user_arr['sponsor_email'] = get_user_meta( $user_id, 'mepr_jefferies_sponsor_email', $single );
	$user_arr['firm'] = get_user_meta( $user_id, 'mepr_firm', $single );
	
	return json_encode($user_arr);
	
}

function getCountries($code){
	global $wpdb;
	$table = 'countries';
	$result = $wpdb->get_results("SELECT * FROM $table");
	$html='';
	$selected = '';
	if(!empty($result)){
		foreach($result as $res):
		if($res->alpha_2_code == $code){$selected='selected';}else{$selected='';}
		$html.='<option value="'.$res->alpha_2_code.'" '.$selected.'>'.$res->en_short_name.'</option>';
		endforeach;
	}
	return $html;
}

add_action('wp_ajax_updateUserMeta', 'updateUserMeta');
add_action('wp_ajax_nopriv_updateUserMeta', 'updateUserMeta');
function updateUserMeta(){
	global $wpdb;
	$html='';
	$user_id = get_current_user_id();
	$single = true;
    $userdata = array( 
        'ID'        => $user_id, 
        'user_email' => $_POST['email'], 
    ); 
    wp_update_user( $userdata ); 
	$user_info = get_userdata($user_id);
	
	 update_user_meta( $user_id, 'first_name', $_POST['first_name'] );
	 update_user_meta( $user_id, 'last_name', $_POST['last_name'] );
	 update_user_meta( $user_id, 'mepr-address-one', $_POST['address_1'] );
	 update_user_meta( $user_id, 'mepr-address-two', $_POST['address_2'] );
	 update_user_meta( $user_id, 'mepr-address-country', $_POST['country'] );
	 update_user_meta( $user_id, 'mepr-address-state', $_POST['state'] );
	 update_user_meta( $user_id, 'mepr-address-city', $_POST['city'] );
	 update_user_meta( $user_id, 'mepr-address-zip', $_POST['zip'] );
	 update_user_meta( $user_id, 'mepr_jefferies_sponsor_email', $_POST['sponsor_email'] );
	 update_user_meta( $user_id, 'mepr_firm', $_POST['firm'] );
	 
	$first_name = get_user_meta( $user_id, 'first_name', $single ); 
	$last_name = get_user_meta( $user_id, 'last_name', $single );
	$email = $user_info->user_email;
	$address_1 = get_user_meta( $user_id, 'mepr-address-one', $single );
	$address_2 = get_user_meta( $user_id, 'mepr-address-two', $single );
	$city = get_user_meta( $user_id, 'mepr-address-city', $single );
	$country = get_user_meta( $user_id, 'mepr-address-country', $single );
	$state = get_user_meta( $user_id, 'mepr-address-state', $single );
	$zip = get_user_meta( $user_id, 'mepr-address-zip', $single );
	$sponsor_email = get_user_meta( $user_id, 'mepr_jefferies_sponsor_email', $single );
	$firm = get_user_meta( $user_id, 'mepr_firm', $single );
	 
	$html.='<h3>Personal Information</h3>
		<table>
		  <tr> <td>Name</td> <td>'.$first_name.' '.$last_name.'</td></tr>
		  <tr><td>Email</td><td>'.$email.'</td></tr>
		</table>
		<h3>Address Information</h3>
		<table>
		  <tr><td>Address</td><td>'.$address_1.' '.$address_2.'</td></tr>
		  <tr><td>Country</td><td>'.$country.'</td></tr>
		  <tr><td>State</td><td>'.$state.'</td></tr>
		  <tr><td>City</td><td>'.$city.'</td></tr>
		  <tr><td>Zip Code</td><td>'.$zip.'</td></tr>
		</table>
		<h3>Subscription Information</h3>
		<table>
		  <tr><td>Plan Type</td><td>Annual</td></tr>
		  <tr><td>Plan Value</td><td>$1000.00</td></tr>
		</table>';
		echo $html;
		die;
}

add_action('wp_ajax_getUserMeta', 'getUserMeta');
add_action('wp_ajax_nopriv_getUserMeta', 'getUserMeta');
function getUserMeta(){
	global $wpdb;
	$html='';
	$user_id = get_current_user_id();
	if($user_id!=0){
		$user_info = get_userdata($user_id);
		$email = $user_info->user_email;
		$html.='<h3>Personal Information</h3>
		<table>
		  <tr> <td>Name</td> <td>'.$_POST['first_name'].' '.$_POST['last_name'].'</td></tr>
		  <tr><td>Email</td><td>'.$email.'</td></tr>
		</table>
		<h3>Address Information</h3>
		<table>
		  <tr><td>Address</td><td>'.$_POST['address_1'].' '.$_POST['address_2'].'</td></tr>
		  <tr><td>Country</td><td>'.$_POST['country'].'</td></tr>
		  <tr><td>State</td><td>'.$_POST['state'].'</td></tr>
		  <tr><td>City</td><td>'.$_POST['city'].'</td></tr>
		  <tr><td>Zip Code</td><td>'.$_POST['zip'].'</td></tr>
		</table>
		<h3>Subscription Information</h3>
		<table>
		  <tr><td>Plan Type</td><td>Annual</td></tr>
		  <tr><td>Plan Value</td><td>$1000.00</td></tr>
		</table>';
	}
	echo $html;
	die;
}

function getTransactionsDateByUser($user_id){
	global $wpdb;
	$first_payment_date = '';
	$last_payment_date = '';
	$transaction_date = '';
	$membership_id = '260192';
	$query = "SELECT * FROM wp_mepr_transactions where user_id='".$user_id."' AND product_id='".$membership_id."' AND status='complete'";
	$last_record = $query.' order by id desc limit 1';
	$second_last_record = $query.' order by id desc limit 1,1';
	
	$data_last = $wpdb->get_results($last_record);
	if(!empty($data_last)){
		foreach($data_last as $value){
			$created_at = date("d/m/Y", strtotime($value->created_at) );
			$expires_at = date("d/m/Y", strtotime($value->expires_at) );
		}}
		
	$datas_last = $wpdb->get_results($second_last_record);
	if(!empty($datas_last)){
		foreach($datas_last as $values){
			$created_at = date("d/m/Y", strtotime($values->expires_at) );
		}}
	
	if(empty($created_at) && empty($expires_at)){
		$created_at = date('d/m/Y');
		$expires_at = date('d/m/Y', strtotime('+1 year')); 
	}
	$transaction_date = $created_at.' to '.$expires_at;
	return $transaction_date;
}

function getPendingTransactionStatus($user_id){
	global $wpdb;
	$membership_id = '260192';
	$query = "SELECT * FROM wp_mepr_transactions where user_id='".$user_id."' AND product_id='".$membership_id."' AND status='pending'";
	$result = $wpdb->get_results($query);
	$date_arr = array();
	$date = date('Y-m-d');
	$status = 0;
	if(!empty($result)){
		foreach($result as $res):
			$date_arr[] = date("Y-m-d", strtotime($res->expires_at) );
		endforeach;
		$expire_date = end($date_arr);
		
		if($expire_date > $date){
			$status = 1;
		}
	}
	return $status;
}

function getTransactionsDateBytxnno($txn_no){
	global $wpdb;
	$transaction_date = '';
	$query = "SELECT * FROM wp_mepr_transactions where trans_num='".$txn_no."'";
	$result = $wpdb->get_row($query);
	if(!empty($result)){
		$created_at = date("d/m/Y", strtotime($result->created_at) );
		$expires_at = date("d/m/Y", strtotime($result->expires_at) );
		$transaction_date = $created_at.' to '.$expires_at;
	}
	return $transaction_date;
}

// 1) User purchases and payment is complete
function capture_completed_transaction($txn) {
  $user = new MeprUser($txn->user_id); //A MeprUser object
  //$membership = new MeprProduct($txn->product_id); //A MeprProduct object
  //$users_memberships = $user->active_product_subscriptions('ids'); //An array of membership CPT ID's
   update_user_meta($txn->user_id, 'subs_expired',0);
 
  // send email to user for subscription details
   $transaction_no = $txn->trans_num;
   $user_id = $txn->user_id;
   $user = get_user_by('ID', $user_id);
   $to = $user->user_email;
   subCompleteEmail($to,$transaction_no,$user_id);
}
add_action('mepr-txn-status-complete', 'capture_completed_transaction');


function subCompleteEmail($to,$txn_no,$user_id){
	$name = get_user_meta( $user_id, 'first_name', true );
	$transaction_date = getTransactionsDateBytxnno($txn_no);
	$site_url = site_url();
	$subject = 'Jefferies - Subscription renewed';
	$headers = array( 'Content-Type: text/html; charset=UTF-8' );
	$message = '<body style="background-color:rgb(77,114,160,.2)"><table style="max-width:600px;margin:0 auto;font-family:sans-serif;width:100%"><thead><tr><td style="padding:15px;text-align:center;background-color:#fff"><img src="'.$site_url.'/wp-content/uploads/2018/06/Jefferies-Logo-Official-Black.png" alt="Jefferies" width="150" height="38" style="display:block;margin:0 auto"></td></tr></thead><tbody><tr><td style="padding:45px 15px;background-color:#4d73a0;text-align:center"><h3 style="color:#fff;font-size:20px;margin:0 0 30px">Subscription has been renewed sucessfully </h3></td></tr><tr><td style="padding:32px 28px 50px;background-color:#fff"><h3 style="color:#000;font-size:18px;margin:0">Please find your subscription details below</h3><div style="padding:10px">Dear '.$name.',<br/><br/>  <div class="table_overflow"><table border="1" style="border-collapse:collapse;width:100%"><thead><tr><th>SUBSCRIPTION TYPE</th><th>VALIDITY</th><th>AMOUNT</th></tr></thead><tbody><tr><td>Annual</td><td>'.$transaction_date.'</td><td>$1000</td></tr></tbody></table></div>  <div style="text-align:center;margin-top:20px"><a href="'.$site_url.'/login/" style="background:#4d73a0;border-radius:4px;padding:14px 30px;color:#fff;font-size:16px;text-decoration:none;display:inline-block">Login Here</a></div><div><strong>Thanks<br>Jefferies Team</strong></div><div style="width:100%;border-bottom:1px solid #ddd;margin:20px 0"></div><div style="display:block;text-align:center"><a href="#" style="display:inline-block;font-weight:700;color:#000;text-decoration:none">Need Help?</a><br><div>Please send any feedback or queries to</div><a href="mailto:support@jeffries.com">support@jeffries.com</a></div></div></td></tr></tbody><tfoot><tr><td style="text-align:center;padding:15px;font-size:13px"><a href="'.$site_url.'/unsubscribe?id='.$userId.'" style="color:#000">Unsubscribe</a><br><div style="margin-top:5px">© 2024 Jefferies Financial Group Inc.</div></td></tr></tfoot></table></body>';
	wp_mail($to, $subject, $message, $headers, array( '' ));
}

function moveElement(&$array, $a, $b) {
  $out = array_splice($array, $a, 1);
  array_splice($array, $b, 0, $out);
}

function mepr_render_custom_fields_fn($custom_fields) {
	
  moveElement($custom_fields, 8, 0);
  return $custom_fields;
}

add_filter('mepr_render_custom_fields', 'mepr_render_custom_fields_fn');

?>