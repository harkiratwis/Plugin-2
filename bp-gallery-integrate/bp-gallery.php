<?php
require_once('../../../wp-load.php');

global $wpdb;

$userId = $_GET['userId']; 

$url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/";

$urlImg = WP_PLUGIN_URL."/bp-gallery-integrate/images/";
	
$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images WHERE user_id = $userId");

$i = 0; 

echo "<table><tr>";
foreach($gallery_array as $slideGallery) { 
	echo "<td>";
	$i++; 
	$image = $slideGallery->image;
	$id = $slideGallery->id;
	$userId = $slideGallery->id;
	
	echo "<a href='#' onclick=\"javascript:showPreview('$urlImg"."$image','$userId',this);return false;\"><img src='$url"."$image' height='60' width='60'></a><br>";
	echo "</td>";
	if($i %2==0) {
		echo "</tr><tr>";
	}
	
} 
echo "</tr></table>";



$imageId = $_GET['imageId'];

if(!empty($imageId)) {
	$wpdb->query("update ".$wpdb->prefix."gallery_images SET viewer = viewer+1 WHERE id = $imageId");
	

	$image_title = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images as a, ".$wpdb->prefix."users as b WHERE a.user_id = b.ID AND a.id = $imageId");
	
	foreach($image_title as $titles){
		 
		echo "<div class='gal_strip'>$titles->image_title by <span>$titles->user_nicename</span></div>";
	}
	
}
?>
