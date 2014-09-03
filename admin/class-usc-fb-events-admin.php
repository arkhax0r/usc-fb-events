<?php
/**
 * USC FB Events.
 *
 * @package   USC_FB_Events_Admin
 * @author    Paul Craig <pcraig3@uwo.ca>
 * @license   GPL-2.0+
 * @copyright 2014
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-usc-fb-events.php`
 *
 * @package USC_FB_Events_Admin
 * @author  Paul Craig <pcraig3@uwo.ca>
 */
class USC_FB_Events_Admin {

    /**
     * Instance of this class.
     *
     * @since    0.1.0
     *
     * @var      object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     *
     * @since    0.1.0
     *
     * @var      string
     */
    protected $plugin_screen_hook_suffix = null;

    protected $plugin_pages = null;

    protected $plugin_submenu = null;

    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since   0.9.2
     */
    private function __construct() {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
            return;
        } */

        /*
         * Call $plugin_slug from public plugin class.
         */
        $plugin = USC_FB_Events::get_instance();
        $this->plugin_slug = $plugin->get_plugin_slug();

        /*
        // Load admin style sheet and JavaScript.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // Add the options page and menu item.add_manage_events_page
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

        // Add an action link pointing to the options page.
        $plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
        add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

        /*
         * Define custom functionality.
         *
         * Read more about actions and filters:
         * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
         */
        //add_action( '@TODO', array( $this, 'action_method_name' ) );
        //add_filter( '@TODO', array( $this, 'filter_method_name' ) );

        $this->plugin_pages = array(
            "manage_events_page",
        );

        $this->plugin_submenu = "events_page_";

        $this->add_manage_events();
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.1.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        /*
         * @TODO :
         *
         * - Uncomment following lines if the admin class should only be available for super admins
         */
        /* if( ! is_super_admin() ) {
            return;
        } */

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     *
     * @since     0.1.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_styles() {

        if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
            return;
        }

        $screen = get_current_screen();

        if ( $this->plugin_screen_hook_suffix == $screen->id ) {
            wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), USC_FB_Events::VERSION );
        }

    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *
     * @since     0.3.0
     *
     * @return    null    Return early if no settings page is registered.
     */
    public function enqueue_admin_scripts() {

        if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
            return;
        }

        $screen = get_current_screen();
        if ( $this->plugin_screen_hook_suffix == $screen->id ) {
            wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', 'admin_filterjs' ), USC_FB_Events::VERSION );
        }

    }

    public function add_manage_events() {

        add_action( 'admin_enqueue_scripts', array( $this, 'add_manage_events_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'add_manage_events_scripts' ) );

        if ( ! class_exists( 'AdminPageFramework' ) )
            include_once( dirname( dirname( dirname( __FILE__ ) ) ) . '/admin-page-framework/library/admin-page-framework.min.php' );

        include_once('views/manage-events.php');

        // Instantiate the class object.
        new Manage_Events;

    }

    /**
     * Register and enqueue specific CSS files only if this page is one we created.
     *
     * @since     0.9.3
     *
     * @return    null    Return early if no custom pages are registered.
     */
    public function add_manage_events_styles() {

        if ( ! isset( $this->plugin_pages ) ) {
            return;
        }

        $screen = get_current_screen();
        $total = count( $this->plugin_pages );
        $on_page = false;

        for($i = 0; $i < $total; $i++) {
            if( $this->plugin_submenu . $this->plugin_pages[$i] === $screen->id )
                $on_page = true;
        }

        if ( $on_page ) {

            wp_enqueue_style( $this->plugin_slug . '-public-styles', plugins_url( '/' . $this->plugin_slug . '/public/assets/css/public.css' ), array(), USC_FB_Events::VERSION );
            wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), USC_FB_Events::VERSION );
        }
    }

    /**
     * Register and enqueue specific JavaScript files only if this page is one we created.
     *
     * @since     0.9.3
     *
     * @return    null    Return early if no custom pages are registered.
     */
    public function add_manage_events_scripts() {

        if ( ! isset( $this->plugin_pages ) ) {
            return;
        }

        $screen = get_current_screen();
        $total = count( $this->plugin_pages );
        $on_page = false;

        for($i = 0; $i < $total; $i++) {
            if( $this->plugin_submenu . $this->plugin_pages[$i] === $screen->id )
                $on_page = true;
        }

        if ( $on_page ) {

            wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery', 'admin_filterjs' ), USC_FB_Events::VERSION );
            wp_enqueue_script( 'tinysort', plugins_url( 'assets/js/jquery.tinysort.min.js', __FILE__ ), array( 'jquery' ), USC_FB_Events::VERSION );
            wp_enqueue_script( 'filterjs', plugins_url( 'assets/js/filter.js', __FILE__ ), array( 'jquery', 'tinysort', 'jquery-ui-core' ), USC_FB_Events::VERSION );
            wp_enqueue_script( 'admin_filterjs', plugins_url( 'assets/js/admin-filter.js', __FILE__ ), array( 'jquery', 'tinysort', 'jquery-ui-core', 'filterjs' ), USC_FB_Events::VERSION );
            wp_enqueue_script( 'jquery-ui-datepicker' );

            // declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
            wp_localize_script( 'admin_filterjs', "ajax", array( 'url' => admin_url( 'admin-ajax.php' ) ) );
        }
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    0.1.0
     *
    public function add_plugin_admin_menu() {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         *
        $this->plugin_screen_hook_suffix = add_options_page(
            __( 'USC FB Events Settings', "manage_events_page" ),
            __( 'USC FB Events Settings', "manage_events_page" ),
            'manage_options',
            "manage_events_page",
            array( $this, 'display_plugin_admin_page' )
        );
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    0.1.0
     *
    public function display_plugin_admin_page() {
    //include_once( 'views/admin.php' );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    0.1.0
     *
     * @TODO: Obviously, this has to be addressed eventually
     */
    public function add_action_links( $links ) {

        return array_merge(
            array(
                'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
            ),
            $links
        );

    }

    /**
     * NOTE:     Actions are points in the execution of a page or process
     *           lifecycle that WordPress fires.
     *
     *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
     *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
     *
     * @since    0.1.0
     */
    public function action_method_name() {
        // Define your action hook callback here
    }

    /**
     * NOTE:     Filters are points of execution in which WordPress modifies data
     *           before saving it or sending it to the browser.
     *
     *           Filters: http://codex.wordpress.org/Plugin_API#Filters
     *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
     *
     * @since    0.1.0
     */
    public function filter_method_name() {
        // Define your filter hook callback here
    }

}