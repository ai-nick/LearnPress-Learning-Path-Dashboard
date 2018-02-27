<?php 
/*
Plugin Name: LearnPress Learning Path Dashboard
Plugin URI: https://github.com/nickwilliamsnewby/LearnPress-Learning-Path-Dashboard
Description: Dashboard/admin ui for outlining learning paths the student may take
Author: Nicholas Williams
Version: 1.0.0
Author URI: http://williamssoftwaresolutions.com
Tags: learnpress
Text Domain: learnpress
*/

if (!defined('ABSPATH')) {
    exit;
}

if(! defined( 'LP_LPATH_DASH_PATH' ) ) define('LP_LPATH_DASH_PATH', dirname( __FILE__ ) );
if(! defined( 'LP_LPATH_DASH_FILE' ) ) define('LP_LPATH_DASH_FILE', ( __FILE__ ) );


if ( !defined('ABSPATH')) {
    exit;
}


class LP_Addon_LearningPath_Dashboard{

	/**
	 * @var object
	 */
	private static $_instance = false;

	/**
	 * @var string
	 */
	private $_plugin_url = '';

	/**
	 * @var string
	 */
    private $_plugin_path = '';

    protected $_meta_boxes = array();

    protected $_post_type = '';

    protected $_tab_slug = 'learning-path-dashboard';


    function __construct(){
        $this->_post_type = 'lp_learning_path_cpt';
        $this->_tab_slug = sanitize_title( __( 'learning-path-dashboard', 'learnpress' ) );
        $this->_plugin_path = LP_LPATH_DASH_PATH;
        $this->_plugin_url  = untrailingslashit( plugins_url( '/', LP_LPATH_DASH_FILE ) );
        add_action('init', array($this, 'admin_init'));
        //add_filter('learn_press_course_settings_meta_box_args', array($this,'learn_press_learningpaths_add_on'), 15);
        //add_action( 'plugins_loaded', array( __CLASS__, 'load_text_domain' ) );
        //add_filter( 'learn_press_user_profile_tabs', array( $this, 'learningpath_dashboard_tab' ), 100, 2 );
		//add_filter( 'learn_press_profile_tab_endpoints', array( $this, 'profile_tab_endpoints' ) );
        add_action('init', array($this, 'create_learning_path'));
        add_action( 'load-post.php', array( $this, 'add_learning_path_meta_boxes' ), 0 );
        add_action( 'load-post-new.php', array( $this, 'add_learning_path_meta_boxes' ), 0 );
        add_shortcode('lp_learning_path', array($this, 'learning_path_query'));
        //add_action( 'admin_menu', array($this, 'addMyMenu'));
    }
    function admin_init(){
        if(is_admin()){
            wp_enqueue_style('jquery-ui-custom', get_template_directory_uri().'/css/jquery-ui-custom.css');
        }
    }
    function learning_path_query($atts, $content){
        global $post;
        $posts = new WP_Query('post_type=lp_learning_path_cpt');
        $out = '';
        if ($posts->have_posts()){
            while ($posts->have_posts()):
                $posts->the_post();
                $out .= '<div class="learning_path">
                <h2>Path Name: '.get_the_title().'</h2>
                <p> ' .get_the_content().'</p>';
                $courseID = get_post_meta(get_the_ID(), '_lp_learning_path_course', false);
                $arrayLen = sizeof($courseID[0]);
                foreach($courseID[0] as $i){
                    $courseObj = LP_Course::get_course($i);
                    $out .='<h3><a href="'.get_the_permalink($i).'">'.$courseObj->post->post_title.'</a></h3>';
                    $out .='<p>'.$courseObj->post->post_content.'</p>';
                    $out .='<div class="img-responsive">'.$courseObj->get_image().'</div>';
                }
                $out .= '</div>';
                /*
                for($i = 0; $i < $arrayLen; $i++){
                    $courseObj = LP_Course::get_course($i[i][0]);
                    $out .='<p><a href="'.get_the_permalink($i).'">'.$courseObj->post->post_title.'</a></p>';
                }
                */
            endwhile;
            wp_reset_postdata();
        } else {
            return;
        }
        return $out;
    }

    function create_learning_path(){
        register_post_type( 'lp_learning_path_cpt',
        array(
            'labels' => array(
                'name' => 'Learning Path',
                'menu_name' => 'Learning Paths',
                'singular_name' => 'Learning Path',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Learning Path',
                'edit' => 'Edit',
                'edit_item' => 'Edit Learning Path',
                'new_item' => 'New Learning Path',
                'view' => 'View',
                'view_item' => 'View Learning Path',
                'search_items' => 'Search Learning Path',
                'not_found' => 'No Learning Path Found',
                'not_found_in_trash' => 'No Learning Path Found in Trash',
                'all_items' => 'Learning Paths'
            ),
 
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'has_archive'        => 'learning_paths',
            'map_meta_cap'       => true,
            'capability_type'    => 'lp_course',
            'show_in_menu'       => 'learn_press',
            'show_in_admin_bar'  => true,
            'show_in_nav_menus'  => true,
            'supports'           => array(
                'title',
                'editor',
                'revisions',
            ),
            'hierarchical'       => true,
            'rewrite'            => array( 'slug' => 'learning_paths', 'hierarchical' => true, 'with_front' => false )
        )
    );
}
public function sortable_courses(){
    echo '<ul class="sortable  ui-sortable">';
    $course_options = $this->get_courses();
    foreach( $course_options as $p )
       echo "<li><code class='hndle'> -[]- </code> {$p->post_title}</li>";
    echo '</ul>';
    ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) 
        {    
            $( '.sortable' ).sortable({
                opacity: 0.6,
                revert: true,
                cursor: 'move',
                handle: '.hndle',
                placeholder: {
                    element: function(currentItem) {
                        return $("<li style='background:#E7E8AD'>&nbsp;</li>")[0];
                    },
                    update: function(container, p) {
                        return;
                    }
                }
            });
            $( '.sortable' ).disableSelection();
        });
        </script>
    <?php
}
public function add_learning_path_meta_boxes() {
    $prefix                                        = '_lp_';
    new RW_Meta_Box(
        apply_filters( 'learn_press_learning_path_general_meta_box', array(
                'title'      => __( 'General Settings', 'learnpress' ),
                'post_types' =>'lp_learning_path_cpt',
                'context'    => 'normal',
                'priority'   => 'high',
                'fields'     => array(
                    array(
                        'name'        => __( 'Learning Path Courses', 'learnpress' ),
                        'id'          => "_lp_learning_path_course",
                        'type'        => 'post',
                        'post_type'   => LP_COURSE_CPT,
                        //'multiple'    => true,
                        'field_type'  => 'select',
                        'description' => 'Courses that are included in this learning path',
                        'placeholder' => __( 'Course for Path', 'learnpress' ),
                        'clone'       => true,
                        'sort_clone'  => true,
                        'std'         => ''
                    ),
                    array(
                        'name' => __( 'Show Completed Courses', 'learnpress' ),
                        'id'   => "{$prefix}show_completed_path_courses",
                        'type' => 'yes_no',
                        'desc' => __( 'Display courses from this path that have been completed', 'learnpress' ),
                        'std'  => 'no'
                    ),
                    array(
                        'name' => __( 'Show Next Step Only', 'learnpress' ),
                        'id'   => "{$prefix}show_next_step_only",
                        'type' => 'yes_no',
                        'desc' => __( 'Only show the next step in the path ', 'learnpress' ),
                        'std'  => 'no'
                    )
                )
            )
        )
    );
    }
    function get_courses() {
		global $wpdb;
		$post_type    = 'lp_course';
		$query        = $wpdb->prepare( "
			SELECT ID, post_title
			FROM {$wpdb->posts}
			WHERE post_type = %s AND post_status = %s
        ", $post_type, 'publish' );
        $courses = $wpdb->get_results( $query );
        return $courses;
    }
    
    function admin_panel(){
        require_once( dirname( __FILE__ ) . '/template/admin.php' );
    }

	/**
	 * @return bool|LP_Addon_LearningPath_Dashboard
	 */
	static function instance() {
		if ( !self::$_instance ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    static function load_text_domain() {
		if ( function_exists( 'learn_press_load_plugin_text_domain' ) ) {
			learn_press_load_plugin_text_domain( LP_LPATH_DASH_PATH, 'learnpress-learningpath-dashboard' );
		}
	}
}
add_action( 'learn_press_loaded', array( 'LP_Addon_LearningPath_Dashboard', 'instance' ) );