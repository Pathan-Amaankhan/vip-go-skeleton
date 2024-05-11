jQuery(document).ready(function($){
	
	jQuery('#datetimepicker').datetimepicker({
				format:'Y-m-d H:i'
			});

  // AJAX url
  var ajax_url = plugin_ajax_object.ajax_url;
  //alert(ajax_url);
  
  jQuery('.del_meeting').click(function(){
	  var meeting_id = jQuery(this).attr('rel');
	  var data_del = {
			  'action': 'deleteMeeting',
			  'mid': meeting_id,
			};
			
			jQuery.ajax({
				  url: ajax_url,
				  type: 'post',
				  data: data_del,
				  success: function(del_response){
					 if(del_response == '1'){
							alert('Meeting Deleted Successfully!');
							location.reload();
					 }else{
						alert('Not deleted Please try again!');
					 }
				  },
				  complete:function(response2){
				   
				  }
			  });
			  
   });
  

  // Search record/*
  jQuery('#create_meeting').click(function(){
	 //validation on fields
	 
	 var mtitle = jQuery("#mtitle").val();
	 var mdate = jQuery("#datetimepicker").val();
	 var mduration = jQuery("#duration").val();
	 var mpass = jQuery("#pass").val();
	 
	 if(mtitle == ''){
		 jQuery("#err_title").html('Please enter meeting title!');
		 return false;
	 }else{
		 jQuery("#err_title").html('');
	 }
	 
	 if(mdate == ''){
		 jQuery("#err_date").html('Please enter meeting date!');
		 return false;
	 }else{
		 jQuery("#err_date").html('');
	 }
	 
	 if(mduration == ''){
		 jQuery("#err_duration").html('Please enter meeting duration!');
		 return false;
	 }else{
		 jQuery("#err_duration").html('');
	 }
	 
	 if(mpass == ''){
		 jQuery("#err_pass").html('Please enter meeting password!');
		 return false;
	 }else{
		 jQuery("#err_pass").html('');
	 }
	 jQuery(this).prop('disabled', true);
	  var data = {
		  'action': 'createZoomMeeting',
		  'title': mtitle,
		  'date': mdate,
		  'duration': mduration,
		  'password':mpass
		};
	 
	 jQuery.ajax({
		  url: ajax_url,
		  type: 'post',
		  data: data,
		  success: function(response){
			  if(response == 1){
				jQuery('.link_created').show();
				jQuery('#meeting_form').trigger("reset");
                jQuery('#create_meeting').prop('disabled', false);
			  }else if(response == 2){
				  jQuery('.zoom_keys').show();
			  }else{
				  
			  }
		  },
		  complete:function(response){
		   
		  }
		  });
	
  });
  
  jQuery('#start_upload').click(function(){
	  jQuery('.up_message').hide();
	  var meeting_id = jQuery(this).attr('rel');
	   jQuery(this).attr('disabled',true);
	   jQuery(this).hide();
	   
		   var data2 = {
			  'action': 'sendInvites',
			  'mid': meeting_id,
			};
			
			jQuery.ajax({
				  url: ajax_url,
				  type: 'post',
				  data: data2,
				  success: function(response2){
					 if(response2 == 'sent'){
						jQuery('.up_message').show();
						jQuery('.up_message').html('Invite link sent sucessfully to members!');
					 }else{
					    jQuery('.up_message').show();
						jQuery('.up_message').html('Link not sent please try again!');
					 }
				  },
				  complete:function(response2){
				   
				  }
			  });
		
	});
 
});