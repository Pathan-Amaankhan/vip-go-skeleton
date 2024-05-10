 <?php 
  $user_id = get_current_user_id();
  $user = new MeprUser($user_id); //A MeprUser object
  $users_memberships = $user->active_product_subscriptions();
 
  
 
 $terms = get_website_terms(); ?>
<div class="subscriptions_term_wrap steps_wrap" id="step_1">
 <!--  Subscription terms -->
 <?php echo $terms; ?>
 
  <div class="submit_btn_wrap">
    <div class="checkbox_wrap">
      <label><input type="checkbox" id="step_1_confirm"> <span>I accept terms & conditions</span></label>
	  <span class="error" id="error_1">Please accept terms & conditions to continue</span>
    </div>
    <div class="submit_btn">
      <a href="javascript:;" id="step_1_btn" class="btn_btn">Confirm</a>
    </div>
  </div>
  
</div>

<?php $user_data = json_decode(getCurrentUserData());?>

<style>
#step_2,#step_3{display:none;}
.error{display:none;}
</style>

<script>
$( document ).ready(function() {
    $("#step_1_btn").click(function(e){
		e.preventDefault();
		if($('#step_1_confirm').is(":checked")){
			$('#error_1').hide();
			window.location.href = '/register/paid-subscription/';
		}else{
			$('#error_1').show();
		}
	});
	
	jQuery("#step_1_confirm").change(function(){
	 if(this.checked) {
		$('#error_1').hide();
	 }else{
		$('#error_1').show();
	 }
	});
});
</script>