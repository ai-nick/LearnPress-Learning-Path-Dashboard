<?php
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
    

    function __construct(){
        add_filter('learn_press_course_settings_meta_box_args', array($this,'learn_press_learningpaths_add_on'), 15);
        //add_action( 'plugins_loaded', array( __CLASS__, 'load_text_domain' ) );
        add_action( 'admin_menu', array($this, 'addMyMenu'));
    }
    function learn_press_learningpaths_add_on( $meta_boxes ) {
        $learningPath = array(
            'name'          =>  __( 'Learning Paths', 'learn_press' ),
            'id'            =>  "_lpr_course_learingpath",
            'type'          =>  'yes_no',
            'description'   =>  'Learning Path that this Course Belongs to',
            'placeholder'   =>  __( 'School Teacher', 'learn_press' ),
            'std'           =>  ''
        );
        array_unshift( $meta_boxes['fields'], $learningPath );
        return $meta_boxes;
    }

    function addMyMenu(){
        add_submenu_page(
            'learn_press',
            'Learning Paths', //page title
            'Learning Paths', //menu title
            'manage_options', //capability,
            'LearningPaths',//menu slug
            array($this, 'admin_panel') //callback function
        );
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