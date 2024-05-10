<?php
/*
Template Name: Client subscriptions
*/
global $wpdb;
//checking directory path and deleting files from folder
// checking table to upload
$lists = $wpdb->get_results("SELECT user_id FROM  wp_mepr_members where memberships='208483' limit 5000");

if(!empty($lists)){
	
	 $delimiter = ","; 
    $filename = "Client_Subscriptions_" . date('Y-m-d') . ".csv"; 
    // Create a file pointer 
    //$f = fopen('php://memory', 'w'); 
    // Set column headers 
    $lineData = array('Sno','ID', 'User Email', 'Firm', 'Sponsor First Name', 'Sponsor Last Name', 'Sponsor Email', 'Sponsor Division'); 
    //fputcsv($f, $fields, $delimiter); 
    // Output each row of the data, format line as csv and write to file pointer 
	$i=1;
    foreach($lists as $list):
		
		$user_id = $list->user_id;
		$data = get_user_by('id',$user_id);
		$email = $data->user_email;
		$sponsor_firm = getClientMeta($user_id,'mepr_firm');	
		$sponsor_first_name = getClientMeta($user_id,'mepr_jefferies_sponsor_first_name');
		$sponsor_last_name = getClientMeta($user_id,'mepr_jefferies_sponsor_last_name');
		$sponsor_email = getClientMeta($user_id,'mepr_jefferies_sponsor_email');
		$sponsor_division = getClientMeta($user_id,'mepr_jefferies_sponsor_division');
		
        $lineData = array($i,$user_id,$email,$sponsor_firm,$sponsor_first_name,$sponsor_last_name,$sponsor_email,$sponsor_division); 
		//$lineData = array($i,$s->date,$s->date,$s->date,$s->date,$s->date); 
       //lineData fputcsv($f, $lineData, $delimiter); 
	   
		echo '<pre>';
		print_r($lineData);
		echo '</pre>';
	$i++;
    endforeach;
	
	
	
     
    // Move back to beginning of file 
    //fseek($f, 0); 
     
    // Set headers to download file rather than displayed 
    //header('Content-Type: text/csv'); 
    //header('Content-Disposition: attachment; filename="' . $filename . '";'); 
	//ob_end_clean();
     
    //output all remaining data on a file pointer 
    //fpassthru($f); 
	exit;
}



