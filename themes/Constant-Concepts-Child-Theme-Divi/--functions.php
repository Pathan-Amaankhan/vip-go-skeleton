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
				$bhtmloutput.=  '</article>';
				if($sn>=10){break;}
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
			if(parseInt(jQuery('#totalCount').val())<=10){jQuery('#showolderentriesprev').hide();}
			jQuery('.updateblogposts').click(function(){
				var prevnext=parseInt(jQuery(this).attr('rel'));
				jQuery('#jefferiesblogpostwrap').css('pointer-events','none');
				jQuery('#jefferiesblogpostwrap').css('opacity','0.2');
				jQuery('.pagination').css('pointer-event','none');

				if(!prevnext){jblimit+=10;}else{jblimit-=10;}
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
					if(jblimit+10>=parseInt(jQuery('#totalCount').val())){jQuery('#showolderentriesprev').hide();}
					if(jblimit+10<parseInt(jQuery('#totalCount').val())){jQuery('#showolderentriesprev').show();}
					jQuery('html, body').animate({ scrollTop: (jQuery('#main-content').offset().top)},500);
				});
			});
		});
		</script>
		<style type='text/css'>.pagination{margin-bottom:30px;}</style>
		";		

		}else{
				$bhtmloutput.= "<div id='nopofnd'>No posts found!.'</div>'<style type='text/css'>#nopofnd{min-height:500px;}</style>";
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
				if($sn>$limit&&$sn<=$limit+10){
				$bhtmloutput.= '<article id="post-'.$p->ID.'" class="et_pb_post clearfix et_pb_no_thumb post-249130 post type-post status-publish format-standard hentry category-uncategorized">';
				$bhtmloutput.= '<h2 class="entry-title"><a href="'.esc_url(get_permalink($p->ID)).'">'.$p->post_title.'</a></h2>';
				$bhtmloutput.= '<p class="post-meta"> <span class="published">'.get_the_date('M j, Y', $p->ID ).'.</span> </p>';
				$bhtmloutput.=  '</article>';
				}
			$sn++;
			}
		}else{
				$bhtmloutput.= "No posts found!";
		}

		echo $bhtmloutput;


    die(); // this is required to return a proper result
}

add_action( 'wp_ajax_jefferiesajaxload', 'loadjefferiesblogposts' );
add_action('wp_ajax_nopriv_jefferiesajaxload', 'loadjefferiesblogposts');

function post_published_notification( $ID, $post ) {

	$current_user = wp_get_current_user();
	wp_enqueue_style( 'style.css', get_stylesheet_uri() );

	include ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/post-to-pdf/mpdf/mpdf.php');

	$marginLeft=15;
	$marginRight=15;
	$marginTop=6;
	$marginBottom=8;
	$marginHeader=9;

	$marginFooter=9;
	$pageLayout='post';
	$pageSize='A4';

	$usersitelogoleft ="<img src='".site_url()."/wp-content/plugins/post-to-pdf/images/jeflogo-pdf-lt.png' style='float:left;height:40px;'>";
	$usersitelogoright="<img src='".site_url()."/wp-content/plugins/post-to-pdf/images/jeflogo-pdf-rt.png' style='float:right;height:30px;'>";

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
	$mpdf=new mPDF('+aCJK',$pageSize.$pageLayout,0,$default_font,$marginLeft,$marginRight,$marginTop,$marginBottom,$marginHeader,$marginFooter);
	
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
?>