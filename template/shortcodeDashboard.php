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

$posts = new WP_Query('post_type=lp_learning_path_cpt');
$out = '<div class="container-fluid">';
$cUser = learn_press_get_current_user();
$cUserID = get_current_user_id();
$userPath = get_user_meta($cUserID, '_lpr_learning_path', true);
//$out .='<p>'.$userPath[0].'</p>';
//$out .='<p>'.($userPath).'</p>';
if ($posts->have_posts()){
	while ($posts->have_posts()):
		$posts->the_post();
		$postID = get_the_ID();
		$out .= '<div class="learning_path row text-center">
		<h2>Path Name: '.get_the_title().'</h2>';
		if($cUserID){
			if($userPath != $postID){
			$out .='<button class="add-to-lp" data-id="'.$postID.'"
			data-nonce="'.wp_create_nonce('learning_path_add_path_to_user').'" data-user="'.$cUserID.'">Take this path</button>';
			} else {
				$out .= '<p>Current Path</p>';
				$out .='<button class="add-to-lp remove-lp-path" data-id=""
			data-nonce="'.wp_create_nonce('learning_path_add_path_to_user').'" data-user="'.$cUserID.'">quit this path</button>';
			} 
		}
		$out .='<p> ' .get_the_content().'</p><div class="col-md-2"></div>';
		$courseID = get_post_meta($postID, '_lp_learning_path_course', false);
		//$arrayLen = sizeof($courseID[0]);
		foreach($courseID[0] as $i){
			$courseObj = LP_Course::get_course($i);
			$out .='<div class="col-md-4 centered"><h3><a href="'.get_the_permalink($i).'">'.$courseObj->post->post_title.'</a></h3>';
			$out .='<div class="img-responsive">'.$courseObj->get_image().'</div><br><br>';
			$out .='<p>'.$courseObj->post->post_content.'</p>';
			$userGrade = $cUser->get_course_grade($i);
			if($userGrade){
				$out .='<div><p>Course Status: <strong>'.$userGrade.'</strong></p></div></div>';
			} else {
				$out .='<div><p>Course Status: <strong> Not Enrolled </strong></p></div></div>';
			}
		}
		$out .= '</div>';
	endwhile;
	$out.='</div>';
	//wp_reset_postdata();
}
wp_reset_postdata();
echo $out;

?>
