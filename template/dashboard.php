<?php
/**
 * Template for displaying archive course content
 *
 * @author  Nick Williams
 * @package LearnPress/Templates
 * @version 1.0.0
 */
defined( 'ABSPATH' ) || exit();
global $post;
?>
<li id="learn-press-tab-wishlist-course-<?php echo $post->ID;?>" data-context="tab-wishlist">
	<a href="<?php echo esc_url( get_permalink() );?>" rel="bookmark"><?php echo get_the_title();?></a>
	<?php LP_Addon_Wishlist::instance()->wishlist_button( $post->ID );?>
</li>
