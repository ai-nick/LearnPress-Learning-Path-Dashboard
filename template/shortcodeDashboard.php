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
if($cUserID == ''){
	$out .= '<div class="text-centered"><h1 class="text-centered">Step 1: Create An Account</h1>';
	$out .= '<p>If you havent registered for an account yet click <a href="'.get_site_url().'/register">
	here</a> to create one, if you have already made an account click <a href="'.get_site_url().'/login">
	here</a> to go to the login page</p></div>';
	wp_reset_postdata();
	echo $out;
} else {
	$userPath = get_user_meta($cUserID, '_lpr_learning_path', true);
	//$out .='<p>'.$userPath[0].'</p>';
	//$out .='<p>'.($userPath).'</p>';
	if ($posts->have_posts()){
		if($userPath == ''){
			$out .= '<div class="learning_path row text-center"><h1>Step 2: Choose A Learning Path</h1>';
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
			$out .= '</div>';
		} else {
			while ($posts->have_posts()):
				$posts->the_post();
				$postID = get_the_ID();
				if ($userPath == $postID){
					$out .= '<div class="learning_path row text-center">
					<h2>Your Current Path: '.get_the_title().'</h2>';
					//$out .= '<p>Current Path</p>';
					$out .='<button class="btn-danger add-to-lp remove-lp-path" data-id=""
					data-nonce="'.wp_create_nonce('learning_path_add_path_to_user').'" data-user="'.$cUserID.'">
					change your path</button>';
					$out .='<p> ' .get_the_content().'</p>';
					$out .='<h3>Step 3: Pass The Following Courses</h3>';
					$out .='<div class="col-md-2"></div>';
					$courseID = get_post_meta($postID, '_lp_learning_path_course', false);
					//$arrayLen = sizeof($courseID[0]);
					//$out .= '<div><p>'.$courseID[0][0].'</p></div>';
					foreach($courseID[0] as $i){
						$courseObj = get_post($i);
						$out .='<div class="col-md-4 centered"><h3><a href="'.get_the_permalink($i).'">'.$courseObj->post_title.'</a></h3>';
						$out .='<div class="img-responsive">'.get_the_post_thumbnail($courseObj).'</div><br><br>';
						$out .='<p>'.$courseObj->post_content.'</p>';
						/*
						$userGrade = $courseObj->get_course_result_html($cUserID);
						if($userGrade){
							$out .='<div><p>Course Status: <strong>'.$userGrade.'</strong></p></div></div>';
						} else {
							$out .='<div><p>Course Status: <strong> Not Enrolled </strong></p></div></div>';
						}
						*/
					}
					$out .= '</div>';
				}
			endwhile;
		}
		$out.='</div>';
		//wp_reset_postdata();
	}
	wp_reset_postdata();
	echo $out;
	}
?>
