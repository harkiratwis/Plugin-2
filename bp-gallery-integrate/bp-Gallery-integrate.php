<?php
/*
Plugin Name: BP Gallery Integrate
Description: Integrate Gallery with Buddypress profiles
Version: 1.0
Author: Harkirat
License: GPL2
*/

// Setup the navigation
// From here... ->
function my_setup_nav() {
	global $bp;
	if (is_user_logged_in()) {
	    bp_core_new_nav_item( array( 
		'name' => __( 'My Gallery', 'buddypress' ), 
		'slug' => 'my-gallery', 
		'position' => 75,
		'screen_function' => 'my_gallery_link',
		'show_for_displayed_user' => true,
		'item_css_id' => 'my-gallery'
	    ) );
	}
}
add_action( 'bp_setup_nav', 'my_setup_nav', 1000 );

function bbg_setup_nav() {
	global $bp;
	if (is_user_logged_in()) {
		bp_core_new_subnav_item( array( 
			'name' => 'View Image Listing',
			'slug' => 'image-listing',
			'parent_url' => $bp->loggedin_user->domain . 'my-gallery' . '/',
			'parent_slug' => 'my-gallery',
			'screen_function' => 'image_listing_link',
			'position' => 55
		) );
	}
}
add_action( 'bp_setup_nav', 'bbg_setup_nav', 100 );

 
// Print gallery title
function my_gallery_title() {
	echo 'My Gallery';
}

// Print Image Listing title
function image_listing_title() {
	echo 'Image Listing';
}



// Print Image Listing content
function image_listing_content() {
	global $bp;
	global $wpdb;
	
	$id = $_REQUEST["id"];
	
	$userId = $bp->displayed_user->id;
	
	/***START FOR PAGINATION CODE****/
	
	$tables = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images WHERE user_id = $userId " ); 
	$tot_rows=count($tables);
	
	$num_rows_per_page = 3;
	$start_limit = 0;
	$end_limit = $num_rows_per_page;
	
	$page=1;
	if(isset($_GET['page']) && $_GET['page']!='')
	{
	    $page = $_GET['page'];
	    $start_limit =($page - 1) * $num_rows_per_page;
	}
	$num_pages = ceil($tot_rows/$num_rows_per_page);
	
	$start=$start_limit+1;
	$cmp=$start+4;
	if($cmp<$tot_rows)
	{
	  $start2=$start+4;
	}
	else
	{
	    if($page>=$num_pages)
	    {
		$start2=$tot_rows;
	    }
	    else
	    {
		$start2=($tot_rows-$start+1);
	    }   
	}

	$limit = " Limit $start_limit, $end_limit";
		
	$image_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images WHERE user_id = ".$userId." $limit"); 
	/***END FOR PAGINATION CODE****/
	
	$url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/";
	if(empty($id)):
	
		$css_path = plugins_url( 'css/default.css', __FILE__ ); ?>

		<link rel="stylesheet" href="<?php echo $css_path; ?>" type="text/css" media="screen" />
            
		<div class="heding_holder" >
			<span class='id'>ID</span>
			<span class='img_title'>Image Title</span>
			<span class='img'>Image</span>
			<span class='action' >Action</span>
		</div><?php
	
		if(!empty($image_array)) {
		
			foreach($image_array as $image_con)
			{
				$imageId = $image_con->id;
				$imageTitle = $image_con->image_title;
				$imageName = $image_con->image; 
				$category_id = $image_con->category_id; 
				$element_id = $image_con->element_id; 
				$url = get_bloginfo('url')."/wp-content/uploads/bpg/thumbs/"; ?>
				
				<div class="heding_holder" >
					<span class='id_sub' ><?php echo $imageId; ?></span>
					<span class='img_title_sub'><?php echo $imageTitle; ?></span>
					<span class='img_sub'  ><img src="<?php echo $url; echo $imageName; ?>" height="50" width="50"></span>
					<div class='action_sub' >
                   				<div class="btn1">
							<form name='edit_gallery' method='post' action="">
								<input type="hidden" name="id" value="<?php echo $imageId; ?>" />
								<input type="hidden" name="imageTitle" value="<?php echo $imageTitle; ?>" />
								<input type="hidden" name="g_image" value="<?php echo $imageName; ?>" />
								<input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />
								<input type="hidden" name="element_id" value="<?php echo $element_id; ?>" />
								<input type="submit" name="EditRecord" value="Edit" />
							</form>
						</div>
						<div class="btn2">
							<form name='delete_gallery' method='post' action="">
								<input type="hidden" name="id" value="<?php echo $imageId; ?>" />
								<input type="hidden" name="imageTitle" value="<?php echo $imageTitle; ?>" />
								<input type="hidden" name="g_image" value="<?php echo $imageName; ?>" />
								<input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />
								<input type="hidden" name="element_id" value="<?php echo $element_id; ?>" />
								<input type="submit" name="DeleteRecord" value="Delete" />
							</form>
						</div>
					</div>
				</div><?php
		
			} 
			
			/***START FOR PAGINATION CODE****/
			$nav  = '';

			for($page2 = 1; $page2 <= $num_pages; $page2++)
			{
			   if ($page2 == $page)
			   {
			      $nav .= " $page "; // no need to create a link to current page
			   }
			   else
			   {
			      $nav .= " <a href=\"image-listing?page=$page2\">$page2</a> ";
			   }
			 } 
			   if ($page > 1)
			{
			   $pagef  = $page - 1;
			   

			   echo $first = " <a href=\"image-listing?page=1\">[First Page]</a> ";
			   echo $prev  = " <a href=\"image-listing?page=$pagef\">[Prev]</a> ";
			}
			else
			{
			  echo  $prev  = '&nbsp;'; // we're on page one, don't print previous link
			  echo $first = '&nbsp;'; // nor the first page link
			}

			if ($page < $num_pages)
			{
			   $pagel = $page + 1;
			   echo $next = " <a href=\"image-listing?page=$pagel\">[Next]</a> ";

			   echo $last = " <a href=\"image-listing?page=$num_pages\">[Last Page]</a> ";
			}
			else
			{
			   echo $next = '&nbsp;'; // we're on the last page, don't print next link
			   echo $last = '&nbsp;'; // nor the last page link
			}

			// print the navigation link
			  $pagingStr="Page:&nbsp;&nbsp;&nbsp;".$first . $prev . $nav . $next . $last;
			/***END FOR PAGINATION CODE****/
			
			
		} else {
			echo "<div style='padding:20px; color:#000;'>Please uplaod the Image first.</div>";
		}
	endif;
	
	if ((isset($_POST["DeleteRecord"])) && ($_POST["DeleteRecord"] == "Delete")) {

		delete_bp_gallery();
		
	}
	
	if ((isset($_POST["EditRecord"])) && ($_POST["EditRecord"] == "Edit")) {

		if(!empty($id)) {
		
			// Query Gallery Category table for all images 
			$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_category");

			// Query Gallery Element table for all images 
			$gallery_elements = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_elements"); 
			
			$css_path = plugins_url( 'css/default.css', __FILE__ ); ?>
	
    		<link rel="stylesheet" href="<?php echo $css_path; ?>" type="text/css" media="screen" />
		
			<form name='bp_gallery' method='post' enctype='multipart/form-data' action="">

				<div class="f_area">

					<div class="f_area_holder"><label>Image Title</label>
					<input type='text' name='image_title' value="<?php echo $_REQUEST['imageTitle']; ?>"/></div>
                    

					<div class="f_area_holder"><label>Upload Image</label>
					
						<input type='file' name='g_image' value="" />
						<input type="hidden" name="old_image" value="<?php echo $_REQUEST['g_image']; ?>" /> 
					</div>
					<?php $url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/"; ?>
					<div class='f_img_holder'><img src="<?php echo $url.''.$_REQUEST['g_image']; ?>" ></div>

					<div  class="f_area_holder"><label>Select Category</label>
					<select name='category_id' class='d_box'><?php
						// And go! - Print each gallery's slug and feed gallery id into shortcode magic
						foreach ($gallery_array as $gallery_con) { 
							$cat_id = $gallery_con->cat_id;
							$cat_name = $gallery_con->cat_name; ?> 
							<option value="<?php echo $cat_id; ?>" <?php if($cat_id == $_REQUEST['category_id']) { echo "selected=selected"; } ?>><?php echo $cat_name; ?></option><?php
						}; ?>
					</select></div>

					<div  class="f_area_holder"><label>Select Element</label>
					<select name='element_id' class='d_box'><?php 
						// And go! - Print each gallery's slug and feed gallery id into shortcode magic
						foreach ($gallery_elements as $gallery_ele) { 
							$element_id = $gallery_ele->element_id;
							$element_name = $gallery_ele->element_name;
							 ?> 
							<option value="<?php echo $element_id; ?>" <?php if($element_id == $_REQUEST['element_id']) { echo "selected=selected"; } ?>><?php echo $element_name; ?></option><?php
						}; ?>
					</select></div>
                    
				    <input name='submitted_form' type='hidden' id='submitted_form' value='image_upload_form' />

				    <input type='hidden' name='id' value="<?php echo $_REQUEST['id']; ?>" />
				    
				    <input type='hidden' name='user_id' value="<?php echo $userId; ?>" />
		    
				    <div class='f_area_holder reset'><input type='submit' name='submit' value='Submit' /></div>

				</div>

			</form><?php

		}
	
	}
	
	if ((isset($_POST["submitted_form"])) && ($_POST["submitted_form"] == "image_upload_form")) {
		update_bp_gallery();
	}
}


// Print gallery content
function my_gallery_content() {
	global $bp;
	global $wpdb;
	
	$userId = $bp->displayed_user->id;
	$css_path = plugins_url( 'css/default.css', __FILE__ ); ?>
	
	<link rel="stylesheet" href="<?php echo $css_path; ?>" type="text/css" media="screen" />
    
	<?php // Query Gallery Category table for all images 
	$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_category");
	
	// Query Gallery Element table for all images 
	$gallery_elements = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_elements");

	echo "<form name='bp_gallery' method='post' enctype='multipart/form-data' action='".save_bp_gallery()."'>";

		echo "<div class='f_area'>";
	
			echo "<div class='f_area_holder'><label>Image Title</label>";
			echo "<input type='text' name='image_title' value='' /></div>";
	
			echo "<div class='f_area_holder'><label>Upload Image</label>";
			echo "<input type='file' name='g_image' value='' /></div>";
		
			echo "<div class='f_area_holder'><label>Select Category</label>";
			echo "<select name='category_id' class='d_box'>";

				// And go! - Print each gallery's slug and feed gallery id into shortcode magic
				foreach ($gallery_array as $gallery_con) { 
					$cat_id = $gallery_con->cat_id;
					$cat_name = $gallery_con->cat_name;
					echo "<option value='$cat_id'>$cat_name</option>";
				};
			echo "</select></label></div>";
		
			echo "<div class='f_area_holder'><label>Select Element</label>";
			echo "<select name='element_id' class='d_box'>";
				// And go! - Print each gallery's slug and feed gallery id into shortcode magic
				foreach ($gallery_elements as $gallery_ele) { 
					$element_id = $gallery_ele->element_id;
					$element_name = $gallery_ele->element_name;
					echo "<option value='$element_id'>$element_name</option>";
				};
			echo "</select></label></div>";
		
			echo "<input name='submitted_form' type='hidden' id='submitted_form' value='image_upload_form' />";
			
			echo "<input type='hidden' name='user_id' value='$userId' />";
			
			echo "<div class='f_area_holder reset'><input type='submit' name='submit' value='Submit' /></div>";
	
		echo "</div>";
		
	echo "</form>";
	
}

/*
	**************************************
	bp gallery function for the short code	
	**************************************
*/
function bpgallery_func( $atts ){
	global $bp;
	global $wpdb;
	// Query Gallery table for all galleries where displayed user is author 
	$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images GROUP BY user_id");

	// And go! - Print each gallery's slug and feed gallery id into shortcode magic
	$url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/"; 
	$urlImg = WP_PLUGIN_URL."/bp-gallery-integrate/images/";
	$pluginurl = WP_PLUGIN_URL."/bp-gallery-integrate/"; 
	$siteurl = get_bloginfo('url');
	$furl = urlencode($urlImg);
	
	//$plugin_path = plugin_basename(__DIR__);
	$css_path = plugins_url( 'css/default.css', __FILE__ );
	$lightbox_path = plugins_url( 'css/lightbox.css', __FILE__ );
	
	$lightbox_plusone = plugins_url( 'js/plusone.js', __FILE__ );

	
	// include files for the light box ?>
	<link rel="stylesheet" href="<?php echo $css_path; ?>" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php echo $lightbox_path; ?>" type="text/css" media="screen" />
	
	<script src="<?php echo $lightbox_plusone; ?>"></script>
	
	<script>
		function showPreview(val,imageId) {
		
			document.getElementById('bpgallery').innerHTML = '<img src='+val+'>';
			
			var xmlhttp;
			if (imageId.length==0)
			  {
			  document.getElementById("countViews").innerHTML="";
			  return;
			  }
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			xmlhttp.onreadystatechange=function()
			  {
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
			    document.getElementById("countViews").innerHTML=xmlhttp.responseText;
			    }
			  }
			xmlhttp.open("GET","<?php echo $pluginurl; ?>/bp-gallery.php?imageId="+imageId,true);
			xmlhttp.send();
		}
		
		function fullgallery(userId,image) {
			document.getElementById('bpgallery1').innerHTML = '<img src='+image+'>';
			var xmlhttp;
			if (userId.length==0)
			  {
			  document.getElementById("txtHint").innerHTML="";
			  return;
			  }
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			xmlhttp.onreadystatechange=function()
			  {
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
			    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
			    }
			  }
			xmlhttp.open("GET","<?php echo $pluginurl; ?>/bp-gallery.php?userId="+userId,true);
			xmlhttp.send();
			
		}
		
		function filterByCatName(catId) {
			var xmlhttp;
			if (catId.length==0)
			  {
			  document.getElementById("showgal").innerHTML="";
			  return;
			  }
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			xmlhttp.onreadystatechange=function()
			  {
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
			    document.getElementById("showgal").innerHTML=xmlhttp.responseText;
			    }
			  }
			xmlhttp.open("GET","<?php echo $pluginurl; ?>/bp-cat-gallery.php?catId="+catId,true);
			xmlhttp.send();
		}
		
		function filterByCatElement(eleId) {
			
			if(document.getElementById("element_name1").checked == true){
				var one = '1';
			} else {
				var one = '0';
			}
			
			if(document.getElementById("element_name2").checked == true){
				var two = '2';
			} else {
				var two = '0';
			}
			
			if(document.getElementById("element_name3").checked == true){
				var three = '3';
			} else {
				var three = '0';
			}
			
			if(document.getElementById("element_name4").checked == true){
				var four = '4';
			} else {
				var four = '0';
			}
			
			
			if(document.getElementById("element_name5").checked == true){
				var five = '5';
			} else {
				var five = '0';
			}
			
			if(document.getElementById("element_name6").checked == true){
				var six = '6';
			} else {
				var six = '0';
			}
			
			if(document.getElementById("element_name7").checked == true){
				var seven = '7';
			} else {
				var seven = '0';
			}
			
			
			var t = one+","+two+","+three+","+four+","+five+","+six+","+seven;
			
			var xmlhttp;
			if (eleId.length==0)
			  {
			  document.getElementById("showgal").innerHTML="";
			  return;
			  }
			if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			xmlhttp.onreadystatechange=function()
			  {
			  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			    {
			    document.getElementById("showgal").innerHTML=xmlhttp.responseText;
			    }
			  }
			xmlhttp.open("GET","<?php echo $pluginurl; ?>/bp-ele-gallery.php?eleId="+t,true);
			xmlhttp.send();
		}
		
		
	</script>
	
	<?php 
	
	// Query Gallery category table for all category where displayed in Gallery
	$gallery_category = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_category");
	
	// Query Gallery elements table for all elements where displayed in Gallery
	$gallery_elements = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_elements");
	
	$content = '<div class="cat_listing" >';
		foreach($gallery_category as $galCat) {
			$content .= '<span class="nav_listing" >';
				$content .= "<a href=\"javascript:filterByCatName($galCat->cat_id)\">";
					$content .= $galCat->cat_name;
				$content .= '</a>';
			$content .= '</span>';
		}
	$content .= '</div>';
	

	$content .= '<div style="float:left; margin-bottom:20px;">';
		foreach($gallery_elements as $galEle) {
			$content .= '<span class="chk_main"  >';
				$content .= '<span class="chk_main_lft"  >';
					$content .= "<input type='checkbox' id='element_name".$galEle->element_id."' onclick=\"javascript:filterByCatElement($galEle->element_id)\" name='element_name".$galEle->element_id."' value='$galEle->element_name' >";
				$content .= '</span>';
				$content .= '<span class="chk_main_rt"  >';
					$content .= $galEle->element_name;
				$content .= '</span>';
			$content .= '</span>';
				
		}
	$content .= '</div>';

	
	$content .= "<p>";
		
		$content .= "<div class='gal_main'>";
	
			$content .= "<div id='showgal'>";
			
				foreach ($gallery_array as $galCon) {
					$gallery_id = $galCon->id;
					$image = $galCon->image;
					$title = $galCon->image_title;	
					$userId = $galCon->user_id;
					$imagepath = $urlImg.$image;
					
					$content .= "<a href=\"javascript:fg_popup_form('fg_formContainer','fg_form_InnerContainer','fg_backgroundpopup');fullgallery('$userId','$imagepath')\">";
						$content .= "<div class='img_f'>";
							$content .= "<div class='title_strip'>$title</div>";
							$content .= "<img src='$url"."$image' alt='' />";
						$content .= "</div>";
					$content .= "</a>";
					
				}
			
			$content .= "</div>";
			
		$content .= "</div>";
	
	$content .= "</p>";
	
	$content .= "<div id='fg_formContainer'>";
		
		$content .= "<div id='fg_box_Close'><a href=\"javascript:fg_hideform('fg_formContainer','fg_backgroundpopup');\"><img src='$urlImg"."btn_close.gif'></a></div>";

		$content .= "<div id='fg_form_InnerContainer'>";
 
		    	$content .= "<div class='bp_gal'>";

		    		$content .= "<div id='txtHint'></div>";
		    		 			    		
			$content .= "</div>";

			$content .= "<div id='countViews'></div>";
			
			//$content .= "<div class='gal_strip'>asdfas<span>dfads</span></div>";
				
			$content .= "<div id='bpgallery' class='bpgallery'><div id='bpgallery1'></div></div>";
				
			$content .= "<div class='icon_s_main' >";
			
			$content .= "<div class='icon_1' >";
			
				$content .= "<iframe src=\"//www.facebook.com/plugins/like.php?href=$furl"."$image&amp;send=false&amp;layout=box_count&amp;width=51&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font&amp;height=90\" scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:51px; height:90px;' allowTransparency='true'></iframe>";
				 
			$content .= "</div>";

			$content .= "<div class='icon_2'>";
				$content .= "<a href='https://twitter.com/share' class='twitter-share-button' data-url='$furl"."$image' data-lang='en' data-related='anywhereTheJavascriptAPI' data-count='vertical'>Tweet</a>";
				$content .= "<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"//platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>";
			$content .= "</div>";

			$content .= "<div class='icon_3'>";
				$content .= "<script type='text/javascript' src=\"//assets.pinterest.com/js/pinit.js\"></script>";
				 
				$content .= "<a href=\"http://pinterest.com/pin/create/button/?url=$furl"."$image%2F&media=$furl"."$image\" class='pin-it-button' count-layout='vertical'><img border='0' src=\"//assets.pinterest.com/images/PinExt.png\" title='Pin It' /></a>";

			$content .= "</div>";
		
		$content .= "</div>";

	    $content .= "</div>";

			
    
	   
	$content .= "</div>";
	$content .= "<div id='fg_backgroundpopup'></div>";
	
	return $content;
}
add_shortcode( 'bpgallery', 'bpgallery_func' );


/*
	********************************************************
	bp gallery function for recent uploaded record in widget
	********************************************************
*/
function bp_recent_uploaded($args) {
	global $bp;
	global $wpdb;
	
	extract($args);
	
	$url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/"; 
	
	// Query Gallery table for all galleries where displayed user is author 
	$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images ORDER BY id DESC LIMIT 25");

	echo $before_widget;
	echo $before_title;
	echo "Recent Uploaded";
	echo $after_title;
	echo "<div style='float:left; width:250px;'>";
		foreach($gallery_array as $galRecent) {
			$image = $galRecent->image;
			echo "<span style='float:left; padding-left:5px;'>";
				echo "<img src='$url"."$image' height='45' width='45'>";
			echo "</span>";
		}
	echo "</div>";
	echo $after_widget;
}
register_sidebar_widget('Recent Uploaded','bp_recent_uploaded');


/*
	****************************************************
	bp gallery function for most viewed record in widget
	****************************************************
*/
function bp_most_viewed($args) {

	extract($args);

	global $bp;
	global $wpdb;
	
	$url = WP_PLUGIN_URL."/bp-gallery-integrate/images/thumbs/";
	
	// Query Gallery table for all galleries where displayed user is author 
	$gallery_array = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."gallery_images ORDER BY viewer DESC LIMIT 25");
	
	echo $before_widget;
	echo $before_title;
	echo "Most Views";
	echo $after_title;
	echo "<div style='float:left; width:250px;'>";
		foreach($gallery_array as $galRecent) {
			$image = $galRecent->image;
			echo "<span style='float:left; padding-left:5px;'>";
				echo "<img src='$url"."$image' height='45' width='45'>";
			echo "</span>";
		}
	echo "</div>";
	echo $after_widget;
}
register_sidebar_widget('Most Viewes','bp_most_viewed');


/*
	*****************************************
	bp gallery function for delete the record	
	*****************************************
*/
function delete_bp_gallery() {
	global $bp;
	global $wpdb;
	
	$id =	$_REQUEST['id']; 

	$delete = $wpdb->query(
			"DELETE FROM ".$wpdb->prefix."gallery_images
			 WHERE id = '$id'
			"
		);
	
	if($delete) {
		echo $res = "<span style='color:red;'>Record deleted successfully.</span>";
	}
	
}

/*
	*****************************************
	bp gallery function for update the record	
	*****************************************
*/
function update_bp_gallery() {
	global $bp;
	global $wpdb;
	error_reporting(0);
	$image_title = 		$_REQUEST['image_title'];
	$image_name = 		$_REQUEST['g_image'];
	$category_id = 		$_REQUEST['category_id'];
	$element_id = 		$_REQUEST['element_id'];
	$user_id = 		$_REQUEST['user_id'];
	$old_image =		$_REQUEST['old_image'];
	$id = 			$_REQUEST['id'];

	if(empty($_FILES['g_image']['name']))
	{
		$_REQUEST['g_image']  = $old_image;
	} else {
		$_REQUEST['g_image']  = $_FILES['g_image']['name'];
	}
	
	// file needs to be jpg,gif,bmp,x-png and 4 MB max
	if (($_FILES["g_image"]["type"] == "image/jpeg" || $_FILES["g_image"]["type"] == "image/pjpeg" || $_FILES["g_image"]["type"] == "image/gif" || $_FILES["g_image"]["type"] == "image/x-png") && ($_FILES["g_image"]["size"] < 9000000))
	{
  
		// some settings for image
		$max_upload_width = '450';

		$max_upload_height = '250';

		// some settings for thumbnail
		$max_upload_width_thumb = '113';

		$max_upload_height_thumb = '79';
	
	
		// if uploaded image was JPG/JPEG
		if($_FILES["g_image"]["type"] == "image/jpeg" || $_FILES["g_image"]["type"] == "image/pjpeg"){	
			$image_source = imagecreatefromjpeg($_FILES["g_image"]["tmp_name"]);
			$image_source_thumb = imagecreatefromjpeg($_FILES["g_image"]["tmp_name"]);
		}		
		// if uploaded image was GIF
		if($_FILES["g_image"]["type"] == "image/gif"){	
			$image_source = imagecreatefromgif($_FILES["g_image"]["tmp_name"]);
			$image_source_thumb = imagecreatefromgif($_FILES["g_image"]["tmp_name"]);
		}	
		// BMP doesn't seem to be supported so remove it form above image type test (reject bmps)	
		// if uploaded image was BMP
		if($_FILES["g_image"]["type"] == "image/bmp"){	
			$image_source = imagecreatefromwbmp($_FILES["g_image"]["tmp_name"]);
			$image_source_thumb = imagecreatefromwbmp($_FILES["g_image"]["tmp_name"]);
		}			
		// if uploaded image was PNG
		if($_FILES["g_image"]["type"] == "image/x-png"){
			$image_source = imagecreatefrompng($_FILES["g_image"]["tmp_name"]);
			$image_source_thumb = imagecreatefrompng($_FILES["g_image"]["tmp_name"]);
		}
		
	

		$remote_file = ABSPATH."wp-content/plugins/bp-gallery-integrate/images/".$_FILES["g_image"]["name"];
		imagejpeg($image_source,$remote_file,100);
		chmod($remote_file,0644);

		//for thumbnail
		$remote_file_thumb = ABSPATH."wp-content/plugins/bp-gallery-integrate/images/thumbs/".$_FILES["g_image"]["name"];
		imagejpeg($image_source_thumb,$remote_file_thumb,100);
		chmod($remote_file_thumb,0644);



		// get width and height of original image
		list($image_width, $image_height) = getimagesize($remote_file);

		if($image_width>$max_upload_width || $image_height >$max_upload_height){
			$proportions = $image_width/$image_height;
		
			if($image_width>$image_height){
				$new_width = $max_upload_width;
				$new_height = round($max_upload_width/$proportions);
			}		
			else{
				$new_height = $max_upload_height;
				$new_width = round($max_upload_height*$proportions);
			}		
		
		
			$new_image = imagecreatetruecolor($new_width , $new_height);
			$image_source = imagecreatefromjpeg($remote_file);
		
			imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
			imagejpeg($new_image,$remote_file,100);
		}


		//for thumbnail
		// get width and height of original image
		list($image_width_thumb, $image_height_thumb) = getimagesize($remote_file_thumb);

		if($image_width_thumb>$max_upload_width_thumb || $image_height_thumb >$max_upload_height_thumb){
			$proportions_thumb = $image_width_thumb/$image_height_thumb;
		
			if($image_width_thumb>$image_height_thumb){
				$new_width_thumb = $max_upload_width_thumb;
				$new_height_thumb = round($max_upload_width_thumb/$proportions);
			}		
			else{
				$new_height_thumb = $max_upload_height_thumb;
				$new_width_thumb = round($max_upload_height_thumb*$proportions_thumb);
			}		
		
		
			$new_image_thumb = imagecreatetruecolor($new_width_thumb , $new_height_thumb);
			$image_source_thumb = imagecreatefromjpeg($remote_file_thumb);
		
			imagecopyresampled($new_image_thumb, $image_source_thumb, 0, 0, 0, 0, $new_width_thumb, $new_height_thumb, $image_width_thumb, $image_height_thumb);
			imagejpeg($new_image_thumb,$remote_file_thumb,100);
		}
	
		$images_name = $_REQUEST['g_image'];
		
	}

	$updated = $wpdb->query( "UPDATE ".$wpdb->prefix."gallery_images SET image_title = '$image_title', image = '$_REQUEST[g_image]', category_id = $category_id, element_id = $element_id WHERE id = $id" ); 
		
		
	if($updated) {
		echo "<span style='color:red;'>Record updated Successfully</div>";
	}	
}

function save_bp_gallery() {

	global $bp;
	global $wpdb;
	error_reporting(0);
	$image_title = 		$_REQUEST['image_title'];
	$image_name = 		$_REQUEST['g_image'];
	$category_id = 		$_REQUEST['category_id'];
	$element_id = 		$_REQUEST['element_id'];
	$user_id = 		$_REQUEST['user_id'];
		
	// upload the file
	if ((isset($_POST["submitted_form"])) && ($_POST["submitted_form"] == "image_upload_form")) {
	
		// file needs to be jpg,gif,bmp,x-png and 9 MB max
		if (($_FILES["g_image"]["type"] == "image/jpeg" || $_FILES["g_image"]["type"] == "image/pjpeg" || $_FILES["g_image"]["type"] == "image/gif" || $_FILES["g_image"]["type"] == "image/x-png") && ($_FILES["g_image"]["size"] < 9000000))
		{
	  
			// some settings for image
			$max_upload_width = '450';
	
			$max_upload_height = '250';

			// some settings for thumbnail
			$max_upload_width_thumb = '113';
	
			$max_upload_height_thumb = '79';
		
		
			// if uploaded image was JPG/JPEG
			if($_FILES["g_image"]["type"] == "image/jpeg" || $_FILES["g_image"]["type"] == "image/pjpeg"){	
				$image_source = imagecreatefromjpeg($_FILES["g_image"]["tmp_name"]);
				$image_source_thumb = imagecreatefromjpeg($_FILES["g_image"]["tmp_name"]);
			}		
			// if uploaded image was GIF
			if($_FILES["g_image"]["type"] == "image/gif"){	
				$image_source = imagecreatefromgif($_FILES["g_image"]["tmp_name"]);
				$image_source_thumb = imagecreatefromgif($_FILES["g_image"]["tmp_name"]);
			}	
			// BMP doesn't seem to be supported so remove it form above image type test (reject bmps)	
			// if uploaded image was BMP
			if($_FILES["g_image"]["type"] == "image/bmp"){	
				$image_source = imagecreatefromwbmp($_FILES["g_image"]["tmp_name"]);
				$image_source_thumb = imagecreatefromwbmp($_FILES["g_image"]["tmp_name"]);
			}			
			// if uploaded image was PNG
			if($_FILES["g_image"]["type"] == "image/x-png"){
				$image_source = imagecreatefrompng($_FILES["g_image"]["tmp_name"]);
				$image_source_thumb = imagecreatefrompng($_FILES["g_image"]["tmp_name"]);
			}
			
		

			$remote_file = ABSPATH."wp-content/plugins/bp-gallery-integrate/images/".$_FILES["g_image"]["name"];
			imagejpeg($image_source,$remote_file,100);
			chmod($remote_file,0644);

			//for thumbnail
			$remote_file_thumb = ABSPATH."wp-content/plugins/bp-gallery-integrate/images/thumbs/".$_FILES["g_image"]["name"];
			imagejpeg($image_source_thumb,$remote_file_thumb,100);
			chmod($remote_file_thumb,0644);
	
	
			// get width and height of original image
			list($image_width, $image_height) = getimagesize($remote_file);
	
			if($image_width>$max_upload_width || $image_height >$max_upload_height){
				$proportions = $image_width/$image_height;
			
				if($image_width>$image_height){
					$new_width = $max_upload_width;
					$new_height = round($max_upload_width/$proportions);
				}		
				else{
					$new_height = $max_upload_height;
					$new_width = round($max_upload_height*$proportions);
				}		
			
			
				$new_image = imagecreatetruecolor($new_width , $new_height);
				$image_source = imagecreatefromjpeg($remote_file);
			
				imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $new_width, $new_height, $image_width, $image_height);
				imagejpeg($new_image,$remote_file,100);
			}

			//for thumbnail
			// get width and height of original image
			list($image_width_thumb, $image_height_thumb) = getimagesize($remote_file_thumb);

			if($image_width_thumb>$max_upload_width_thumb || $image_height_thumb >$max_upload_height_thumb){
				$proportions_thumb = $image_width_thumb/$image_height_thumb;
			
				if($image_width_thumb>$image_height_thumb){
					$new_width_thumb = $max_upload_width_thumb;
					$new_height_thumb = round($max_upload_width_thumb/$proportions);
				}		
				else{
					$new_height_thumb = $max_upload_height_thumb;
					$new_width_thumb = round($max_upload_height_thumb*$proportions_thumb);
				}		
			
			
				$new_image_thumb = imagecreatetruecolor($new_width_thumb , $new_height_thumb);
				$image_source_thumb = imagecreatefromjpeg($remote_file_thumb);
			
				imagecopyresampled($new_image_thumb, $image_source_thumb, 0, 0, 0, 0, $new_width_thumb, $new_height_thumb, $image_width_thumb, $image_height_thumb);
				imagejpeg($new_image_thumb,$remote_file_thumb,100);
			}
		
			$image_name = $_FILES["g_image"]["name"];
			
			$insert = $wpdb->insert( 
					'wp_gallery_images', 
					array( 
						'image_title' => "$image_title", 
						'image' => "$image_name",
						'category_id' => "$category_id",
						'element_id' => "$element_id",
						'user_id' => "$user_id"
					), 
					array( 
						'%s', 
						'%s', 
						'%d',
						'%d',
						'%d'
					) 
				);
			
			echo "<span style='color:red'>Record inserted Successfully</span>";
			
		}
		else{
			echo "<span style='color:red'>Error Uploading Image</span>";
			
		}
	}
	
}


// Setup gallery link
function my_gallery_link () {
	add_action( 'bp_template_title', 'my_gallery_title' );
	add_action( 'bp_template_content', 'my_gallery_content' ); 
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

// Setup image_listing link
function image_listing_link () {
	add_action( 'bp_template_title', 'image_listing_title' );
	add_action( 'bp_template_content', 'image_listing_content' ); 
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
?>
