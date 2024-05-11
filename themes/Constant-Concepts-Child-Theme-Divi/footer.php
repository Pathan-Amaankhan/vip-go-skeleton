<?php
if ( et_theme_builder_overrides_layout( ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ) || et_theme_builder_overrides_layout( ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ) ) {
    // Skip rendering anything as this partial is being buffered anyway.
    // In addition, avoids get_sidebar() issues since that uses
    // locate_template() with require_once.
    return;
}

/**
 * Fires after the main content, before the footer is output.
 *
 * @since 3.10
 */
do_action( 'et_after_main_content' );

if ( 'on' === et_get_option( 'divi_back_to_top', 'false' ) ) : ?>

	<span class="et_pb_scroll_top et-pb-icon"></span>

<?php endif;

if ( ! is_page_template( 'page-template-blank.php' ) ) : ?>

			<footer id="main-footer">
				<?php get_sidebar( 'footer' ); ?>

				<div id="footer-bottom">
					<div class="container clearfix">
					
					<?php
					if ( has_nav_menu( 'footer-menu' ) ) : ?>

						<div id="et-footer-nav">
								<?php
									wp_nav_menu( array(
										'theme_location' => 'footer-menu',
										'depth'          => '1',
										'menu_class'     => 'bottom-nav',
										'container'      => '',
										'fallback_cb'    => '',
									) );
								?>
						</div>

			     <?php endif; ?>
				<?php
					if ( false !== et_get_option( 'show_footer_social_icons', true ) ) {
						get_template_part( 'includes/social_icons', 'footer' );
					}

					// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo et_core_fix_unclosed_html_tags( et_core_esc_previously( et_get_footer_credits() ) );
					// phpcs:enable
				?>
					</div>
				</div>
			</footer>
		</div>

<?php endif; // ! is_page_template( 'page-template-blank.php' ) ?>

	</div>
	<?php wp_footer(); ?>
	<script src="<?php echo get_stylesheet_directory_uri() ?>/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ?>/css/bootstrap.css">
	
<div class="home-popup modal fade" id="quoteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
	<div class="modal-content">
	  <!--<div class="modal-header">
	  </div>-->
	  <div class="modal-body review_profile_term_wrap steps_wrap">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		  <span aria-hidden="true">&times;</span>
		</button>
		<?php  $terms = get_website_terms(); echo $terms; ?>
		
		<div class="view_profile"></div>
		
	<div class="agreement">
		<input type="checkbox" id="terms_conditions" name="terms_conditions" value="terms">
		<label for="terms_conditions"> I have read and agree to the Terms of Service</label>
	</div>
		
	  </div>
	</div>
  </div>
</div>

	
	
<style>
#quoteModalCenter .et_pb_row{width:90%;}
#quoteModalCenter h4{line-height:30px;}
.modal {
z-index: 999999;
}
.modal-backdrop {
z-index: 99999;
}

.modal .close {
position: relative;
z-index: 99;
}

.page-id-207536 #quoteModalCenter p:last-of-type {
color: #666 !important;
text-align: left;
}

.agreement {
text-align: center;
}

.request_section {
min-height: calc(100vh - 263px);
}

pdf-viewer#viewer {
pointer-events: none;
}

.disable-select {
-webkit-touch-callout: none;
-webkit-user-select: none;
-khtml-user-select: none;    
-moz-user-select: none;      
-ms-user-select: none;      
user-select: none;
}
@media print{
.disable-select{
   display:none
}
}

@media print{
.disable-text{
  filter:grayscale(100%)
}
.fixed-elements{
  position: relative;
}
}

.mepr_tos a{color:#fff;}
</style>

<script>
jQuery( document ).ready(function() {
	jQuery( 'input[type="checkbox"]' ).prop('checked', false);
    //jQuery("input[type=text]:not(#mepr_firm1)").keyup
	jQuery(".case_wrap input[type=text]").keyup(
     function () {
    	  if(this.id != 'mepr_jefferies_contact_email_address1'){
			 //this.value = this.value.substr(0, 1).toUpperCase() + this.value.substr(1).toLowerCase(); 
			  var val = jQuery(this).attr('id');
				 if(val !='mepr_jefferies_contact_email_address1'){
					 this.value = this.value.toLowerCase().replace(/\b(\w)/g, s => s.toUpperCase());
				 }
		  }
     }
 );
 
 var timeout = null
jQuery('#user_email1').on('keyup', function() {
    var email = this.value;
	if(email == ''){
		jQuery('#mepr_firm1').val('');
		return false;
	}
	var check_email = IsEmail(email);
	if(check_email){
		clearTimeout(timeout);
		timeout = setTimeout(function() {           
		console.log(email);
		//var email = jQuery(this).val();
		if(email != ''){
			jQuery.ajax({
				type : "post",
				url : '/wp-admin/admin-ajax.php',
				context: this,
				data : { action: "get_firm", 'email' : email},
				success: function(response) {
					if(response != 0){
						jQuery('#mepr_firm1').val(response);
						jQuery('#mepr_firm1').attr('readonly', true);
					}else{
						//jQuery('#mepr_firm1').val('');
						jQuery('#mepr_firm1').attr('readonly', false);
					}
				}
			});	
		}
		}, 500)
	}
})
 
jQuery("#wpforms-208652-field_13 li input,#wpforms-208662-field_6 li input,#mepr_agree_to_tos1").change(function(){
	 if(this.checked) {
		jQuery('#quoteModalCenter').modal({backdrop: 'static', keyboard: false});  
		jQuery('#terms_conditions').prop('checked', false);
		getDetails();
    }
});

jQuery("#terms_conditions").change(function(){
	 if(this.checked) {
        jQuery('#quoteModalCenter').modal('hide');
    }
});

jQuery(".close").click(function(){
		jQuery('#wpforms-208652-field_13 li input,#wpforms-208662-field_6 li input,#mepr_agree_to_tos1').prop('checked', false);
        jQuery('#quoteModalCenter').modal('hide');
});

jQuery(".mepr_tos a").click(function(e){
		e.preventDefault();
		if(jQuery('#mepr_agree_to_tos1').is(":checked")){
			jQuery('#mepr_agree_to_tos1').prop('checked', false);
		}else{
			jQuery('#terms_conditions').prop('checked', false);
			jQuery('#mepr_agree_to_tos1').prop('checked', true);
			jQuery('#quoteModalCenter').modal('show');
		}
});

jQuery(".page-id-207356 .mepr-submit").click(function(e){
	e.preventDefault();
	var validRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@(jefferies.com)$/;  
	var val = jQuery('#mepr_jefferies_contact_email_address1').val();
	if(val!= ''){
		var check_email = IsEmail(val);
		if(check_email){
				if (!(val.toLowerCase().match(validRegex))) {  
				jQuery('#mepr_jefferies_contact_email_address1').addClass('invalid');
                jQuery('#err_jeff').show();
				return false;
			}else{
				jQuery('#mepr_jefferies_contact_email_address1').removeClass('invalid');
                jQuery('#err_jeff').hide();
			}				
		}else{
			jQuery('#mepr_jefferies_contact_email_address1').addClass('invalid');
			return false;
		}
	}
});

function IsEmail(email) {
  var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!regex.test(email)) {
    return false;
  }else{
    return true;
  }
}

function getDetails(){
 var first_name = $("#user_first_name1").val();
 var last_name = $("#user_last_name1").val();
 //var email = $("#user_email").val();
 var address_1 = $("#mepr-address-one1").val();	
 var address_2 = $("#mepr-address-two1").val();	
 var country = $("#mepr-address-country1").val();	
// var state = $(".mepr-states-text").val();
 var city = $("#mepr-address-city1").val();
 var zip = $("#mepr-address-zip1").val();
 var sponsor_email = $("#mepr_jefferies_contact_email_address1").val();
 var firm = $("#mepr_firm1").val();
 
 if($('.mepr-states-text').is(':visible')) {
	 var state = $(".mepr-states-text").val();
 }else{
	  var state = $(".mepr-states-dropdown").val();
 }
 
jQuery.ajax({
		type : "post",
		url : '/wp-admin/admin-ajax.php',
		context: this,
		data : { action: "getUserMeta", first_name: first_name,last_name: last_name, address_1: address_1, address_2: address_2, country: country, state: state, city: city, zip: zip, sponsor_email: sponsor_email, firm: firm},
		success: function(response) {
			$(".view_profile").html(response);
			
		}
	});	
}

/** new script **/
jQuery('.mepr_first_name, .mepr_last_name,.mepr_mepr-address-city,.mepr_mepr-address-country,.mepr_mepr-address-state,.mepr_mepr-address-zip,.mepr_mepr_jefferies_contact_email_address,.mepr_mepr_jefferies_contact_name, .page-id-207356 .mepr_password,.page-id-207356 .mepr_password_confirm ').addClass('half_col');
jQuery('.mepr_first_name').before('<h3 class="field_heading">Personal Information</h3>');
jQuery('.mepr_mepr-address-one').before('<h3 class="field_heading">Address Information</h3>');
jQuery('.mepr_mepr_jefferies_contact_name').before('<h3 class="field_heading">Jefferies Contact Information</h3>');
jQuery('.page-id-207356 .mepr-geo-country').after('<h3 class="field_heading">Create Password</h3>');
jQuery('.mepr_mepr_firm').after('<h3 class="field_heading hide_subs">Subscription Plan - Annual $1000.00</h3>');
jQuery('.single-memberpressproduct.postid-260192 .mepr-submit').val('Yes I Confirm');
jQuery('.mepr-account-change-password').append('<a href="/my-account/?action=newpassword">Change Password</a>');
jQuery('#mepr_jefferies_contact_email_address1').after('<span class="error" id="err_jeff">@jefferies.com domain required</span>');
});
</script>
</body>
</html>