<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/includes
 * @author     Postlink <demo@gmail.com>
 */
class Postlink_Classroom {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Postlink_Classroom_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	protected $templates;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'POSTLINK_CLASSROOM_VERSION' ) ) {
			$this->version = POSTLINK_CLASSROOM_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'postlink-classroom';
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->templates = array();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Postlink_Classroom_Loader. Orchestrates the hooks of the plugin.
	 * - Postlink_Classroom_i18n. Defines internationalization functionality.
	 * - Postlink_Classroom_Admin. Defines all hooks for the admin area.
	 * - Postlink_Classroom_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-postlink-classroom-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-postlink-classroom-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-postlink-classroom-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-postlink-classroom-public.php';

		$this->loader = new Postlink_Classroom_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Postlink_Classroom_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Postlink_Classroom_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Postlink_Classroom_Admin( $this->get_plugin_name(), $this->get_version() );
		if (isset($_GET['page']) == 'postlink-classroom-opt' ) {
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 9999);
			$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', true );
		}
		// // Hook for configured_students_page
		$this->loader->add_action("wp_ajax_configured_students_page", $plugin_admin, "configured_students_page");
		$this->loader->add_action("wp_ajax_nopriv_configured_students_page", $plugin_admin, "configured_students_page");

		// // Hook for delete_project_from_admin
		$this->loader->add_action("wp_ajax_delete_project_from_admin", $plugin_admin, "delete_project_from_admin");
		$this->loader->add_action("wp_ajax_nopriv_delete_project_from_admin", $plugin_admin, "delete_project_from_admin");
		
		// // Hook for download_action
		$this->loader->add_action("wp_ajax_download_action", $plugin_admin, "download_action");
		$this->loader->add_action("wp_ajax_nopriv_download_action", $plugin_admin, "download_action");

		// // Hook for get_categories
		$this->loader->add_action("wp_ajax_get_categories", $plugin_admin, "get_categories");
		$this->loader->add_action("wp_ajax_nopriv_get_categories", $plugin_admin, "get_categories");
		// // Hook for delete_category
		$this->loader->add_action("wp_ajax_delete_my_category", $plugin_admin, "delete_my_category");
		$this->loader->add_action("wp_ajax_nopriv_delete_my_category", $plugin_admin, "delete_my_category");

		// // Hook for add_category
		$this->loader->add_action("wp_ajax_add_category", $plugin_admin, "add_category");
		$this->loader->add_action("wp_ajax_nopriv_add_category", $plugin_admin, "add_category");

		// Action hook for menu pages
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'postlink_classroom_opt' );


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Postlink_Classroom_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 999 );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// Hook for postlink_students_posts show
		$this->loader->add_action("wp_ajax_postlink_students_posts", $plugin_public, "postlink_students_posts");
		$this->loader->add_action("wp_ajax_nopriv_postlink_students_posts", $plugin_public, "postlink_students_posts");
	
		// Hook for upload page
		$this->loader->add_action("wp_ajax_upload_media", $plugin_public, "upload_media");
		$this->loader->add_action("wp_ajax_nopriv_upload_media", $plugin_public, "upload_media");
		
		// Hook for show uploaded project data
		$this->loader->add_action("wp_ajax_show_self_projects_data", $plugin_public, "show_self_projects_data");
		$this->loader->add_action("wp_ajax_nopriv_show_self_projects_data", $plugin_public, "show_self_projects_data");

		// Hook for checking project data
		$this->loader->add_action("wp_ajax_checking_file", $plugin_public, "checking_file");
		$this->loader->add_action("wp_ajax_nopriv_checking_file", $plugin_public, "checking_file");

		// Hook for delete_project data
		$this->loader->add_action("wp_ajax_delete_project", $plugin_public, "delete_project");
		$this->loader->add_action("wp_ajax_nopriv_delete_project", $plugin_public, "delete_project");

		// Hook for show_all_project_data data
		$this->loader->add_action("wp_ajax_show_all_project_data", $plugin_public, "show_all_project_data");
		$this->loader->add_action("wp_ajax_nopriv_show_all_project_data", $plugin_public, "show_all_project_data");

		// Hook for download_action data
		$this->loader->add_action("wp_ajax_download_action", $plugin_public, "download_action");
		$this->loader->add_action("wp_ajax_nopriv_download_action", $plugin_public, "download_action");

		// Hook for edit_requests data
		$this->loader->add_action("wp_ajax_edit_requests", $plugin_public, "edit_requests");
		$this->loader->add_action("wp_ajax_nopriv_edit_requests", $plugin_public, "edit_requests");

		// Hook for live_searching_students_name data
		$this->loader->add_action("wp_ajax_live_searching_students_name", $plugin_public, "live_searching_students_name");
		$this->loader->add_action("wp_ajax_nopriv_live_searching_students_name", $plugin_public, "live_searching_students_name");
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Postlink_Classroom_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
