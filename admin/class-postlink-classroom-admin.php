<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/admin
 * @author     Postlink <demo@gmail.com>
 */
class Postlink_Classroom_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'register_project_templates' )
			);

		} else {

			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);

		}
		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);

		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);

		// Add your templates to this array.
		$this->templates = array(
			'postlink-classroom-public-display.php' =>'Postlink Posts',
			'postlink-classroom-upload-display.php' =>'Postlink Upload',
			'postlink-classroom-download-display.php' =>'Postlink Download'
		);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Postlink_Classroom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Postlink_Classroom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/postlink-classroom-admin.css', array(), $this->version, 'all' );
		

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Postlink_Classroom_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Postlink_Classroom_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'devjquery', plugin_dir_url( __FILE__ ) . 'js/jquery.min.js', array(), $this->version, true );
		wp_enqueue_script('devsweetalert', plugin_dir_url(__FILE__) . 'js/sweetalert.js', array(), $this->version, true);
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/postlink-classroom-admin.js', array(), $this->version, true );
		wp_localize_script($this->plugin_name, 'admin_ajax_url', array(
			'ajax_url' => admin_url('admin-ajax.php')
		));
	}


	// Make postlink_classroom options
	function postlink_classroom_opt()
	{
		add_menu_page( //Main menu register
			"Postlink Classroom", //page_title
			"Postlink Classroom", //menu title
			"manage_options", //capability
			"postlink-classroom-opt", //menu_slug
			array($this, "postlink_classroom_view"), //callback function
			'dashicons-schedule',
			65
		);
		add_submenu_page( //sub menu register
			"postlink-classroom-opt", //parent_slug
			"Students", //page title
			"Students", //menu title
			"manage_options", //capability
			"postlink-classroom-opt",  //menu-slug
			array($this, "postlink_classroom_view") //Callback function same with parent
		);
	}

	function postlink_classroom_view(){
		require_once MY_PLUGIN_PATH. 'admin/partials/postlink-classroom-admin-display.php';
	}
		/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}

	
	public function register_project_templates( $atts ) {

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
	
		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 
	
		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');
	
		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );
	
		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );
	
		return $atts;
	
	}

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		
		// Get global post
		global $post;

		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}

		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 

		// Just changing the page template path
		// WordPress will now look for page templates in the subfolder 'templates',
		// instead of the root
		$file = MY_PLUGIN_PATH.'public/partials/'. get_post_meta( 
			$post->ID, '_wp_page_template', true 
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}
		// Return template
		return $template;
	}

	
// Configered students settings page
	function configured_students_page(){
		$perpage_post = intval($_POST['post_shows']);
		$user = sanitize_text_field($_POST['users']);
		$posts_slug = sanitize_text_field($_POST['posts_slug']);

		global $wpdb; //Define wpdb global variable
		$postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; //Define postlinkClassroom table with wp prefix

		$user_data= $wpdb->get_var("SELECT student_id FROM $postlinkClassroom");
		if($wpdb->num_rows>0){
			$wpdb->update( 
				$postlinkClassroom, 
				array(
					"post_per_page" => $perpage_post,
					"user_role" => "$user",
					"posts_slug" => "$posts_slug"
				),
				array("student_id" => $user_data), 
				array( "%d", "%s", "%s" ),
				array("%d")
			);
			die();
		}
		$wpdb->insert(
			$postlinkClassroom,
			array(
				"post_per_page" => $perpage_post,
				"user_role" => "$user",
				"posts_slug" => "$posts_slug"
			),
			array(
				"%d",
				"%s",
				"%s"
			)
		);
		die();
	}

	function get_all_projects(){
		global $wpdb;
		$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
		$wp_user_table = $wpdb->prefix .'users';

		if (current_user_can( 'administrator' )) {
			$projects = $wpdb->get_results("SELECT p.*,u.* FROM $postlinkProjects p JOIN $wp_user_table u ON p.student_id = u.ID ORDER BY p.project_id DESC");

			if($wpdb->num_rows>0){
				$output = "";
				$sl = 1;
				foreach($projects as $project){
					$output .=	'<tr>';
					$output .=	'<td>'.$sl.'</td>';
					$output .=	'<td class="pro_name">'.$project->project_name.'</td>';
					$output .=	'<td class="usr_name">'.$project->display_name.'</td>';
					$output .=	'<td>';
					$output .=	'<input class="edit_inp" data-value="'.$project->project_id.'" data-sid="'.$project->student_id.'" type="number" placeholder="'.$project->downloaded.' Times">';
					$output .=	'</td>';
					$output .=	'<td>';
					$output .=	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
					$output .=	'</td>';
					$output .=	'<td  class="action">';
					$output .=	'<button class="btn del_btn" data-value="'.$project->project_id.'" data-sid="'.$project->student_id.'">Delete</button>';
					$output .=	'</td>';
					$output .=	'</tr>';
					$sl++;
				}
				echo $output;
			}else{
				echo "<td colspan='5' class='warn'>No project found!</td>";
			}
		}else{
			return "Not allowed!";
		}
	}//ENDS get_all_projects

	//Download
	function download_action(){
		if(isset($_POST['project_id'])){
			global $wpdb;
			$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
			$project_id = intval($_POST['project_id']);
		
			$project = $wpdb->get_var("SELECT project_file FROM $postlinkProjects WHERE project_id = $project_id");

			$update = $wpdb->query( "UPDATE $postlinkProjects SET downloaded = downloaded+1 WHERE project_id = $project_id");

			if($update){
				$count = $wpdb->get_var("SELECT downloaded FROM $postlinkProjects WHERE project_id = $project_id");
				echo wp_json_encode( array("count" => $count, "project" => $project));
				wp_die();
			}else{
				die();
			}
		}
		wp_die();
	}
	//Edit requests
	function edit_requests(){
		if(isset($_POST['values'], $_POST['student_id'], $_POST['project_id'])){
			$values = intval($_POST['values']);
			$student_id =  intval($_POST['student_id']);
			$project_id =   intval($_POST['project_id']);

			if($values){
				$value=$values;
			}else{
				$value='0';
			}

			global $wpdb;
			$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';

			$update = $wpdb->query( "UPDATE $postlinkProjects SET downloaded = $value WHERE project_id = $project_id AND student_id = $student_id");
			if($update){
				$count = $wpdb->get_var("SELECT downloaded FROM $postlinkProjects WHERE project_id = $project_id AND student_id = $student_id");
				echo $count;
				wp_die();
			}else{
				die();
			}
		}
	}

	//Delete project
	function delete_project_from_admin(){
		if(isset($_POST['project_id'],$_POST['student_id'])){
			global $wpdb;
			$student_id =  intval($_POST['student_id']);
			$project_id =  intval($_POST['project_id']);
			$users =  new WP_User($student_id);
			$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
			$upload_dir   = wp_upload_dir();

			$project = $wpdb->get_var("SELECT project_name FROM $postlinkProjects WHERE student_id = $student_id AND project_id = $project_id");

			$file_path = $upload_dir['path'].'/devjoo_postlink_plugin/'.$users->user_login.'/'.$project;//define user directory

			if($project){
				$wpdb->query("DELETE FROM $postlinkProjects WHERE student_id = $student_id AND project_id = $project_id");

				foreach(glob($file_path . '/*') as $file) { //delete project ffiles
					if(is_dir($file)) {
						delete_files($file);
					}else{
						unlink($file);
					};
				} rmdir($file_path);

				echo "Project has deleted!";
				wp_die();
			}
		}
	}
	
	// add_category
	function add_category(){
		if(isset($_POST['category'])){
			if($_POST['category'] != ""){
				$category = sanitize_text_field( strtolower($_POST['category']) );
				global $wpdb;
				$postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';

				$wpdb->query("SELECT category FROM $postlinkCategories WHERE category = '$category'");
				
				if($wpdb->num_rows>0){
					echo wp_json_encode( array("exist" => "Already exist!"));
					wp_die();
				}else{
					$updates = $wpdb->insert($postlinkCategories,
					array("category" => $category), array("%s"));
					if($updates){
						echo wp_json_encode( array("success" => "Aded successful"));
						wp_die();
					}
				}
			}else{
				echo wp_json_encode( array("exist" => "Empty field Not allowed!"));
				wp_die();
			}
		}
	}

	function get_categories(){//Get categories lists
		global $wpdb;
		$postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';
		$categories = $wpdb->get_results("SELECT * FROM $postlinkCategories ORDER BY category_id DESC");
		
		if($wpdb->num_rows>0){
			$output = "";
			$sl = 1;
			foreach($categories as $category){
				$output .= ' <tr>';
				$output .= '<td>'.$sl.'</td>';
				$output .= '<td>'.$category->category;
				$output .= '<button data-value="'.$category->category_id.'" class="cat_del">Delete</button>';
				$output .= '</td>';
				$output .= '</tr>';
				$sl++;
			}
			echo $output;
			wp_die();
		}else{
			echo "<td colspan='3' class='warn'>No category found!</td>";
		}
	}

	function delete_my_category(){
		if(isset($_POST['cat_id'])){
			$cat = intval($_POST['cat_id']);
			global $wpdb;
			$postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';

			$deleted = $wpdb->query("DELETE FROM $postlinkCategories WHERE category_id = $cat");

			if($deleted){
				echo wp_json_encode( array("success" => "Deleted successful"));
				wp_die();
			}else{
				wp_die();
			}
			wp_die();
		}
	}
}
