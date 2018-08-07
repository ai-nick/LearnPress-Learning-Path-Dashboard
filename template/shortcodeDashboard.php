<?php
/**
 * Template for displaying archive course content
 *
 * @author  Nick Williams
 * @package LearnPress/Templates
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit();
//global $post;
$argies = array('post_type'=>'lp_learning_path_cpt',
				'order'=>'asc');
$posts = new WP_Query($argies);

$out = '<div class="container-fluid">';
//$cUser = learn_press_get_current_user();
$cUserID = get_current_user_id();
$passed = array();
$outHeader = '';
$bgCheckStatus = get_user_meta($cUserID, 'user_bg_check_passed', true);
if($cUserID == ''){
	$out .= '<div class=" panel panel-warning text-centered"><div class="panel-heading"><h1 class="text-centered">Step 1: Create An Account</h1></div>';
	$out .= '<div class="panel-body"><p>If you havent registered for an account yet click <a href="'.get_site_url().'/register">
	here</a> to create one, if you have already made an account click <a href="'.get_site_url().'/login">
	here</a> to go to the login page</p></div></div></div></div>';
	wp_reset_postdata();
	echo $out;
} else {
	$userPath = get_user_meta($cUserID, '_lpr_learning_path', true);
	//$out .='<p>'.$userPath[0].'</p>';
	//$out .='<p>'.($userPath).'</p>';
	//$out .= '<div class="panel panel-warning">';
	if ($posts->have_posts()){
		if($userPath == ''){
			$out .= '<div class="panel panel-warning text-center"><div class="panel-heading"><h1>Step 2: Choose A Learning Path</h1></div>';
			$out .= '<div class="panel-body">';
			$out .= '<p>Before you get started, we’ll need to go through the Brand Enthusiast Course. 
			 Here, you’ll learn about the core values of Strider as well as some basic safety tips and fun facts. 
			  After that, you’ll have the choice to choose which course or courses you’d like to take. The Early Learning Course is for those who would like to become a 
			  Certified Early Learning Strider Instructor to help teach young children how to ride a Strider bike! The Inclusive Learning Course will teach you how to instruct an 
			  Inclusive Learning Course. Inclusive Learning Courses are designed to teach and help those with disabilities how to enjoy riding a bike. 
			  Once you have completed the background check, the Brand Enthusiast Course, and your preferred path (Early Learning or Inclusive Learning), 
			  you will be granted access to the Certified Instructor Resources. To be eligible for Strider Education pricing, you’ll need to fill out the <a href="https://www.striderbikes.com/education/application">Strider Education Instructor application</a>. In addition to filling out the Strider Education application, you’ll also need to submit a background check. IMPORTANT: NO REFUNDS WILL BE ISSUED FOR COURSES PURCHASED IF YOU DO NOT PASS YOUR BACKGROUND CHECK. </p>';
			while ($posts->have_posts()):
				$posts->the_post();
				$postID = get_the_ID();
				$out .= '<div class="learning_path col-md-4 text-center">
				<h2>Path Name: '.get_the_title().'</h2>';
				if($cUserID){
					$out .='<button class="add-to-lp btn-success" data-id="'.$postID.'"
					data-nonce="'.wp_create_nonce('learning_path_add_path_to_user').'" data-user="'.$cUserID.'">
					Take this path</button><br><br>'; 
				}
				$out .='<p> ' .get_the_content().'</p>';
				$out .= '</div>';
			endwhile;
			$out .= '</div></div>';
		} else {
			while ($posts->have_posts()):
				$posts->the_post();
				$postID = get_the_ID();
				if ($userPath == $postID){
					$out .= '<div class="panel panel-warning text-center"><div class="panel-heading"><h1>
					Your Current Path: '.get_the_title().'</h1></div><div class="panel-body">';
					//$out .= '<p>Current Path</p>';
					$out .='<button class="btn-danger add-to-lp remove-lp-path" data-id=""
					data-nonce="'.wp_create_nonce('learning_path_add_path_to_user').'" data-user="'.$cUserID.'">
					Change Your Path</button>';
					$out .='<p> ' .get_the_content().'</p>';
					$out .='<h3>Step 3: Pass The Following Courses</h3>';
					$out .='<div class="col-md-2"></div>';
					$courseID = get_post_meta($postID, '_lp_learning_path_course', false);
					//$arrayLen = sizeof($courseID[0]);
					//$out .= '<div><p>'.$courseID[0][0].'</p></div>';
					foreach($courseID[0] as $i){
						$courseObj = get_post($i);
						$out .='<div class="col-md-4 centered"><h3><a href="'.get_the_permalink($courseObj->ID).'">'.$courseObj->post_title.'</a></h3>';
						$out .='<div class="img-responsive">'.get_the_post_thumbnail($courseObj->ID).'</div><br><br>';
						$out .='<p>'.$courseObj->post_content.'</p>';
						$cU = learn_press_get_current_user();
						$userGrade = $cU -> has_passed_course($i);
						if($userGrade){
							$out .='<div><p>Course Status: <strong>Passed!</strong></p></div></div>';
							if($bgCheckStatus == 1 && $courseObj->post_title !== "Brand Enthusiast"){
								$passed[] = $courseObj->post_title;
							}
						} else {
							$out .='<div><p>Course Status: <strong> Not Complete </strong></p></div></div>';
						}
					}
					$out .= '</div></div>';
				}
			endwhile;
		}
		if(sizeof($passed) != 0){
			foreach($passed as $pc){
				$outHeader .= '<h2 class="text-center"> Congrats you are now a certified '.$pc.' instructor</h2>';
			}
		}
		$out.='</div></div>';
		//wp_reset_postdata();
	}
	$out .= '</div>';
	wp_reset_postdata();
	echo $outHeader . $out;
	}
?>
