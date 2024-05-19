<?php
/*
Template Name: Email Unapproved Members
*/

//echo dirname(__FILE__);

   
function create_csv() {
    global $wpdb;  
    $filepath = $_SERVER['DOCUMENT_ROOT'].'/wp-content/themes/Constant-Concepts-Child-Theme-Divi/report.csv';
	
    $fd = fopen($filepath, 'w');
    if($fd === FALSE) {
        die('Failed to open temporary file');
    }
   
	global $wpdb;
	$args = array(
		'role'    => 'jef_clients',
		'orderby' => 'ID',
		'order'   => 'DESC',
		'meta_query' => array(
				array(
					'key'     => 'wpforms-pending',
					'value'   => 1,
					'compare' => '='
				),
		)
	);
	$users = get_users( $args );
	
    fputcsv($fd, array('First Name', 'Last Name', 'Email Address', 'Firm Name', 'Jefferies Sponsor Division', 'Jefferies Sponsor Email address','Jefferies Sponsor First Name','Jefferies Sponsor last Name'));
	
	$i=1;
    foreach($users as $user):
		
		$user_id = $user->ID;
	    $name = $user->display_name;
		$email = $user->user_email;
	
		$first_name = getClientMeta($user_id,'first_name');
		$last_name = getClientMeta($user_id,'last_name');
		$sponsor_firm = getClientMeta($user_id,'mepr_firm');
		$sponsor_email = getClientMeta($user_id,'mepr_jefferies_sponsor_email');
		$sponsor_division = getClientMeta($user_id,'mepr_jefferies_sponsor_division');
		$sponsor_first_name = getClientMeta($user_id,'mepr_jefferies_sponsor_first_name');
		$sponsor_last_name = getClientMeta($user_id,'mepr_jefferies_sponsor_last_name');
	
		$modified_values = array($first_name,$last_name,$email,$sponsor_firm,$sponsor_division,$sponsor_email,$sponsor_first_name,$sponsor_last_name);
		 fputcsv($fd, $modified_values);
            
	$i++;
    endforeach;
	
    rewind($fd);
    fclose($fd);
    return $filepath;
}

$to = 'ethan@constantconcepts.io';
$subject = 'Unapproved members report';
$headers = "From: Jefferis <postmaster@mail.constantconcepts.dev>\r\n";
$headers .= "Reply-To: postmaster@mail.constantconcepts.dev\r\n";
$headers .= "CC: test@constantconcepts.io\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$body = '<html lang="en"> <head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta http-equiv="X-UA-Compatible" content="ie=edge"> <link rel="stylesheet" href="style.css"> <title>Unapproved Members Report</title> </head> <body> <div style="background:#fff; width:100%;padding: 30px;"> <div class="logo" style="margin:20px 0; padding:0;"> <img style="width:150px;" src="https://jefferiesmacrostrategy.com/wp-content/uploads/2018/06/Jefferies-Logo-Official-Black.png" class="logo"> </div> <p>Hi Admin,<br/> Please refer to attached CSV file for list of unapproved members on site. </p> Thanks<br/> Jefferies Team. </div> </div> </body> </html>';
$attachment = create_csv();

$sent = wp_mail($to, $subject, $body, $headers,$attachment);
if($sent) {
   echo 'message sent!';
}else{
   echo 'message not sent!';
}




?> 
 