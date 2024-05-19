<?php
/*
Plugin Name: Post To PDF
Plugin URI: http://constantconcepts.io/
Description: Generate PDF files from wordpress posts.
Author: Constant Concepts
Text Domain: Post To PDF
Version: 2.0
Author URI: http://constantconcepts.io/
License: Proprietary
*/

function post_to_pdf () {
	if(get_post_type(get_the_ID())=='post'&& is_single()){
	ob_start();
	$post=get_post(get_the_ID());
	$current_user = wp_get_current_user();
	wp_enqueue_style( 'style.css', get_stylesheet_uri() );

	include ($_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/cc-post-to-pdf/lib/vendor/autoload.php');

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
					
	$footer_section="<p id='footertext'>This commentary is for the exclusive use of <strong>".$current_user->user_firstname." ".$current_user->user_lastname.", ". $current_user->user_email."</strong> - ".date('m/d/Y h:i:s')."</p>"; 

	$titleClean = apply_filters( 'post_to_pdf_title', apply_filters( 'the_title', $post->post_title, $post->ID ), $post );

	$postTitle= '<div class="entry-header"><h1 class="entry-title">'. $titleClean . ' - ' . get_the_time('m/d/Y', $post->ID).'</h1></div>';

	$post_content = apply_filters( 'post_to_pdf_content', $post->post_content, $post );
	$post_content = apply_filters( 'post_to_pdf_print_content', $post_content );
	$post_content = apply_filters( 'the_content', $post_content );
	$shortcodes   = implode( '|', apply_filters( 'post_to_pdf_print_remove_shortcodes', array( 'vc_', 'az_' ) ) );
	$post_content = preg_replace( "/\[\/?({$shortcodes})[^\]]*?\]/", '', $post_content );

	$postContent='<div class="entry-content">'. $post_content .'</div>';

    $html='<style type="text/css">p{line-height:18px;}h1{font-size:25px;} a{color:#000;text-decoration:none;}a:hover{text-decoration:none;} #footertext{font-size:12px;text-align:left;font-family:arial;color:#5f5c5c;}</style>';
	$html.='<div id="content"><div class="post" style="font-family:arial;font-size:12px;">';
	$html.=$postTitle;
	$html.=$postContent;
    $html.='</div></div>';
	//$mpdf=new mPDF('+aCJK',$pageSize.$pageLayout,0,$default_font,$marginLeft,$marginRight,$marginTop,$marginBottom,$marginHeader,$marginFooter);
	$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8',]);

	$mpdf->AliasNbPages( '{PAGETOTAL}' );
	$mpdf->setAutoTopMargin = 'stretch';
	$mpdf->setAutoBottomMargin = 'stretch';

	$mpdf->autoMarginPadding = 0;
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

	$mpdf->SetWatermarkText('Demo');
	$mpdf->showWatermarkText = true;
	$mpdf->watermark_font = 'arialblack';
	$mpdf->watermarkAngle = 35;
	$mpdf->SetProtection(array());
	$mpdf->WriteHTML($html);
	$file_name =$post->ID.'.pdf';
	$tpages= $mpdf->page;
	$pheight= $tpages*1550;

	//$mpdf->Output($file_name,'I');

	$ccptdir=$_SERVER['DOCUMENT_ROOT'].'/wp-content/plugins/cc-post-to-pdf/temp/';
	if(!is_dir($ccptdir)){mkdir($ccptdir, 0755, true);}
	$mpdf->Output($ccptdir.$file_name,\Mpdf\Output\Destination::FILE);
    echo '<!DOCTYPE html><html lang="en"><head><title>'.$post->post_title.' | '.get_bloginfo('name').'</title></head><body>';
	echo '<style type="text/css">html,body{margin:0;padding:0;overflow-x:hidden;}embed{pointer-events:none;}#ccupi{display:none;}  @media print {body *{display:none!important;}#ccupi{display:block!important;font-size:30px;text-align:center;font-weight:bold;margin-top:100px}} iframe{border:none;}#overlay{ position:fixed; width:100%;height:'.$pheight.'px;top:35px;left:0;right:0;bottom:0;z-index:2;pointer-events:none;}</style>';
	echo '<div id="ccpdf">';
	echo '<iframe width="100%" height="'.$pheight.'px" src="'.site_url().'/wp-content/plugins/cc-post-to-pdf/js/web/viewer.html?file='.site_url().'/wp-content/plugins/cc-post-to-pdf/temp/'.$file_name.'&dButton=false&pButton=false&oButton=false&nButton=false&sButton=#zoom=auto&pagemode=none" title="Embedded PDF" class="pdfjs-iframe" frameBorder="0"></iframe>';
	echo '</div><div id="overlay"></div>';
    echo '<div id="ccupi">Use PRINT icon to print this page.</div>';
	echo '</body></html>';
    exit();
	}
}


if(isset($_POST['btnjfswfp'])&&$_POST['btnjfswfp']=='jfswvjppdf'){
add_action( 'get_header', 'post_to_pdf' );
}

 
function pdf_icon_on_post($content){
	if(get_post_type(get_the_ID())=='post'&& is_single()){
		$ID = get_the_ID();
		$cat_array = array();
		$categories = get_the_category($ID);
		if(!empty($categories)){
			foreach($categories as $cat):
				$cat_array[] = $cat->slug;
			endforeach;
		}
		if(in_array("media-highlights", $cat_array)){
			return $content;
		}else{
			$pdfPrintLink=$content;
			$pdfPrintLink.='<p><a href="javascript:void(0);" id="anchppdf">';
			$pdfPrintLink.='<img src="'.plugins_url().'/cc-post-to-pdf/images/pdf_icon.png"/>';
			$pdfPrintLink.=' Print PDF</a></p>';
			$pdfPrintLink.='<form method="POST" id="frmppdf" target="_blank" style="display:none;">';
			$pdfPrintLink.='<input type="submit" name="btnjfswfp" id="btnjfswfp" value="jfswvjppdf">';
			$pdfPrintLink.='</form>';
			$pdfPrintLink.='<script type="text/javascript">';
			$pdfPrintLink.='jQuery(document).ready(function(){';
					$pdfPrintLink.='jQuery("#anchppdf").click(function(){';
						$pdfPrintLink.='jQuery("#btnjfswfp").trigger("click")';
					$pdfPrintLink.='});';
			$pdfPrintLink.='});';
			$pdfPrintLink.='</script>';        
			return $pdfPrintLink;
		}
	}else{
		return $content;
	}
}


if(isset($_POST['btnjfswfp'])&&$_POST['btnjfswfp']=='jfswvjppdf'){
	;
}else{
	add_filter( "the_content", "pdf_icon_on_post" );
}
?>