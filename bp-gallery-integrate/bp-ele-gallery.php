<?php
require_once('../../../wp-load.php');

global $wpdb;

$eleId = $_GET['eleId']; 

if($eleId == '0,0,0,0,0,0,0'){
	$eleId = '1,2,3,4,5,6,7';
}

$url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/";

$urlImg = WP_PLUGIN_URL."/bp-gallery-integrate/images/";
	
$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images WHERE element_id IN ($eleId) GROUP BY user_id");

$i = 0; 


foreach($gallery_array as $slideGallery) { 

	$image = $slideGallery->image;
	$gallery_id = $slideGallery->id;
	$image = $slideGallery->image;
	$title = $slideGallery->image_title;	
	$userId = $slideGallery->user_id;

	echo "<a href=\"javascript:fg_popup_form('fg_formContainer','fg_form_InnerContainer','fg_backgroundpopup');fullgallery($userId)\">";
	
		echo "<div class='img_f'>";
				
			echo "<div class='title_strip'>$title</div>";
			echo "<img src='$url"."$image' alt='' />";

		echo "</div>";
	echo "</a>";

} 
?>
