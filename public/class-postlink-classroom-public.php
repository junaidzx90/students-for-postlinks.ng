<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/public
 * @author     Postlink <demo@gmail.com>
 */
class Postlink_Classroom_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
	}


	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		// students page css
		if (is_page_template('postlink-classroom-public-display.php' )) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/postlink-classroom-public.css', array(), $this->version, 'all' );
		}
		// Upload page css
		if (is_page_template('postlink-classroom-upload-display.php' )) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/postlink-classroom-upload-display.css', array(), $this->version, 'all' );
		}
		// download page css
		if (is_page_template('postlink-classroom-download-display.php' )) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/postlink-classroom-download-display.css', array(), $this->version, 'all' );
		}
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		if (is_page_template('postlink-classroom-public-display.php' ) || is_page_template('postlink-classroom-upload-display.php' ) || is_page_template('postlink-classroom-download-display.php' )) {
			wp_enqueue_script( 'devjquery', plugin_dir_url( __FILE__ ) . 'js/jquery.min.js', array(), $this->version, true );
			wp_enqueue_script( 'devjquery.form', plugin_dir_url( __FILE__ ) . 'js/jquery.form.js', array(), $this->version, true );
			wp_enqueue_script('devsweetalert', plugin_dir_url(__FILE__) . 'js/sweetalert.js', array('devjquery'), $this->version, true);
		}

		if (is_page_template('postlink-classroom-download-display.php' )) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/postlink-classroom-download.js', array( 'jquery' ), $this->version, true );
			wp_localize_script($this->plugin_name, '_ajax_url', array(
				'ajax_url' => admin_url('admin-ajax.php')
			));
		}

		if (is_page_template('postlink-classroom-upload-display.php' )) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/postlink-classroom-uploads.js', array( 'jquery' ), $this->version, true );
			wp_localize_script($this->plugin_name, '_ajax_url', array(
				'ajax_url' => admin_url('admin-ajax.php')
			));
		}

		if (is_page_template('postlink-classroom-public-display.php' )) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/postlink-classroom-public-display.js', array( 'jquery' ), $this->version, true );
			wp_localize_script($this->plugin_name, '_ajax_url', array(
				'ajax_url' => admin_url('admin-ajax.php')
			));
		}
	}


	function get_post_perpage(){//This function for checking per-page posts
		global $wpdb; //Define wpdb global variable
		$postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; //Define postlinkClassroom table with wp prefix

		$post_per = $wpdb->get_var("SELECT post_per_page FROM $postlinkClassroom");
		if ($wpdb->num_rows > 0) {
			return $post_per;
			wp_die();
		}else{
			return 5;
			wp_die();
		}
	}

	public static function get_current_user(){//This function for checking user role
		global $wpdb; //Define wpdb global variable
		$postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; //Define postlinkClassroom table with wp prefix

		$user_role = $wpdb->get_var("SELECT user_role FROM $postlinkClassroom");
		if ($wpdb->num_rows > 0) {
			return $user_role;
			wp_die();
		}else{
			return "student";
			wp_die();
		}
	}

	// Loadmore data function
	function postlink_students_posts(){
		global $current_user;      
		$user = wp_get_current_user();

		$default = $this->get_post_perpage();
		$default_role = $this->get_current_user();
		$paged = $_POST['paged'];
		if ( $user->roles[0] === $default_role || $user->roles[0] === 'administrator' ) {
			$args = array(
				'posts_per_page' => $default,
				'paged' => $paged,
				'post_status' => 'publish',
				'author' => $current_user->ID,
				'orderby' => 'ID',
				'order' => 'DESC',
			);
		
			$students_posts = new WP_Query($args);
			$totalpost = $students_posts->found_posts;
			$output="";
			if ($students_posts->have_posts()) :
				while ($students_posts->have_posts()) : $students_posts->the_post();
					$output .= '<div class="publish-content">';
					$output .= '<div class="content_top">';
					$output .= get_the_post_thumbnail();
					$output .= '<span class="student_name">'.get_the_author_meta( 'display_name' ).'</span>';
					$output .= '<span class="create_date">Created: '.get_the_date( 'D M j' ).'</span>';
					$output .= '</div>';
					$output .= '<hr>';
					$output .= '<h3 class="title"><a href="'.get_the_permalink().'" rel="nofollow">'.get_the_title().'</a></h3>';

					$output .= substr(get_the_excerpt(),0,300);
					
					$output .= '<a class="seemore" href="'.get_the_permalink().'" rel="nofollow">Read More...</a>';
					$output .= '</div>';
				endwhile;
				if($totalpost> $default){
					$output .= '<button  class="pst-loadmore">See more</button>';
				}
				echo $output;
				wp_die();
			else :
				wp_reset_postdata();
				wp_die();
			endif;
		}else{
			wp_die();
		}
	}

	//Upload files
	function upload_media(){
		$noncess = $_POST['upload_file_nonce'];
		if(wp_verify_nonce( $noncess,  "upload_file_nonce_val")){
			if(isset($_POST['fileUpInp']) || isset($_POST['image_inp']) || isset($_POST['project_name']) || isset($_POST['category']) || isset($_POST['file_desc'])){
				global $wpdb;
				$current_user = wp_get_current_user();
				$upload_dir   = wp_upload_dir();
				$original_file = $_FILES['fileUpInp']['name'];
				$fileDomain = pathinfo($original_file, PATHINFO_FILENAME);
				$project_names = sanitize_text_field($_POST['project_name']);//project name
				$category = sanitize_text_field($_POST['category']);//category
				$description = sanitize_textarea_field($_POST['file_desc']);//project description

				if ( isset( $current_user->user_login ) && ! empty( $upload_dir['path'] ) ) {
					$file_path = $upload_dir['path'].'/devjoo_postlink_plugin/'.$current_user->user_login.'/'.$project_names;
					if ( ! file_exists( $file_path ) ) {
						wp_mkdir_p( $file_path );
					}
					$file_name = $_FILES['fileUpInp']['tmp_name'];
					$targetPath = $file_path.'/'.$_FILES['fileUpInp']['name'];
					$fileType = strtolower(pathinfo($targetPath,PATHINFO_EXTENSION));
					$size = $_FILES["fileUpInp"]["size"];
					$fileSize = $this->bytesToReadable($size);


					$project_file_url = $upload_dir['url'].'/devjoo_postlink_plugin/'.$current_user->user_login.'/'.$project_names.'/'.$_FILES['fileUpInp']['name'];//db store file url

					if(is_uploaded_file($_FILES['fileUpInp']['tmp_name'])) {
						if(move_uploaded_file($file_name,$targetPath)) {
							$project_image_url = $upload_dir['url'].'/devjoo_postlink_plugin/'.$current_user->user_login.'/'.$project_names.'/'.$_FILES['image_inp']['name'];

							$target_path4Icon = $file_path.'/'.$_FILES['image_inp']['name'];
							if(is_uploaded_file($_FILES['image_inp']['tmp_name'])) {
								move_uploaded_file($_FILES['image_inp']['tmp_name'],$target_path4Icon);
								$pIcon = $project_image_url;//original icon
							}
						

							//store in database
							$user_id = $current_user->ID;//user id
							$project_name = $project_names;//project name
							$project_file = $project_file_url; //project file
							$project_desc = $description;//project description
							$project_image = $pIcon;//project icon/image
							$project_type = $fileType;//project icon/image
							$project_size = $fileSize;//project icon/image

							$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
							// Select maximum existing id for skipping indexing
							$last = $wpdb->get_var("SELECT project_id FROM $postlinkProjects WHERE project_id=(select max(project_id) FROM $postlinkProjects)");

							if(!$last){
								$pid = 1;
							}else{
								$pid = $last+1;
							}

							$updates = $wpdb->insert($postlinkProjects,
							array("project_id" => $pid,"student_id" => $user_id, "project_name" => $project_name, "category" => $category, "project_desc" => $project_desc, "project_image"=>$project_image, "project_file"=>$project_file, "project_type" => $project_type, "project_size" => $project_size), array("%d", "%d", "%s","%s", "%s", "%s","%s","%s","%s"));

							if ( !$updates ) {//remove if not update db
								foreach(glob($file_path . '/*') as $file) { 
									if(is_dir($file)) {
										delete_files($file);
									}else{
										unlink($file);
									};
								} rmdir($file_path);

								echo wp_json_encode(array("error" =>"There is some critical error! Try to write unique things"));
								wp_die();
							}
							echo wp_json_encode("GOOD JOB");
							wp_die();
						}
					}
				}
				wp_die();
			}else{
				echo wp_json_encode(array("error" =>"Required all fields!"));
				wp_die();
			}
		}else{
			echo wp_json_encode(array("error" =>"Inavalid request"));
			wp_die();
		}
	}
	//end uploads
    function time_format($date, $format)
    {
        $create = date_create($date);
        $date_result = date_format($create, $format);
        return $date_result;
	}
	
	function bytesToReadable($bytes)
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
		for ($i = 0; $bytes > 1024; $i++) $bytes /= 1024;
		return round($bytes, 2) . ' ' . $units[$i];
	}


	function checking_file(){//Checking files before submitting
		global $wpdb;
		if(isset($_POST['filename'])){
			$filename = $_POST['filename'];
			$exten = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
			$expect = ["apk"];
			if (!in_array($exten, $expect)) {
				echo wp_json_encode( array("inavalid"=>"Invalid file!") );
				wp_die();
			}
		}
		if(isset($_POST['existName'])){
			$name = sanitize_text_field( $_POST['existName'] );
			$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
			$wpdb->get_var("SELECT project_name FROM $postlinkProjects WHERE project_name = '$name'");
			if($wpdb->num_rows>0){
				echo wp_json_encode( array("exist"=>"Already exist!") );
				wp_die();
			}else{
				echo wp_json_encode( array("success"=>"valid") );
				wp_die();
			}
		}
	}

	//Delete project
	function delete_project(){
		if(isset($_POST['project_id'])){
			global $wpdb;
			$current_user = wp_get_current_user();
			$student_id = $current_user->ID;
			$project_id = intval($_POST['project_id']);
			$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
			$upload_dir   = wp_upload_dir();

			$project = $wpdb->get_var("SELECT project_name FROM $postlinkProjects WHERE student_id = $student_id AND project_id = $project_id");

			$file_path = $upload_dir['path'].'/devjoo_postlink_plugin/'.$current_user->user_login.'/'.$project;//define user directory

			if($project){
				$wpdb->query("DELETE FROM $postlinkProjects WHERE student_id = $student_id AND project_id = $project_id");

				foreach(glob($file_path . '/*') as $file) { //delete project ffiles
					if(is_dir($file)) {
						delete_files($file);
					}else{
						unlink($file);
					};
				} rmdir($file_path);

				echo $project_id;
				wp_die();
			}
		}
	}

	//show create data
	function show_self_projects_data(){
		global $wpdb;
		$current_user = wp_get_current_user();
		$student_id = $current_user->ID;
		$default_role = $this->get_current_user();
		$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';

		if ( $current_user->roles[0] === $default_role ) {
			if(isset($_POST['limit'], $_POST['start'])){
				$projects = $wpdb->get_results("SELECT * FROM $postlinkProjects WHERE student_id = $student_id ORDER BY project_id DESC LIMIT ".$_POST['start'].",".$_POST['limit']."");

				if($wpdb->num_rows>0){
					foreach($projects as $project){
						$element .=	'<div class="uploaded" data-id="'.$project->project_id.'">';
						$element .=    '<div class="project_desc">';
						$element .=        '<div class="project_top">';
						$element .=            '<span class="project_menu">°°°</span>';
						$element .=        '</div>';
						$element .=        '<div class="uploaded_dropdown">';
						$element .=            '<span class="uploaded_item"><small class="delete-project" data-id="'.$project->project_id.'">Delete</small></span>';
						$element .=        '</div>';
						$element .=       ' <p>'.ucfirst($project->project_desc).'</p>';
						$element .=    '</div>';
						$element .=    '<div class="file_info">';
						$element .=        '<table>';
						$element .=                '<tr>';
						$element .=						'<td class="thead">NAME</td>';
						$element .=                    '<td class="pname">'.$project->project_name.'</td>';
						$element .=                '</tr>';
						$element .=                '<tr>';
						$element .=						'<td class="thead">Category</td>';
						$element .=                    '<td class="category">'.$project->category.'</td>';
						$element .=                '</tr>';
						$element .=                '<tr>';
						$element .=						'<td class="thead">SIZE</td>';
						$element .=                    '<td>'.$project->project_size.'</td>';
						$element .=                '</tr>';
						$element .=                '<tr>';
						$element .=						'<td class="thead">TYPE</td>';
						$element .=                    '<td>'.$project->project_type.'</td>';
						$element .=                '</tr>';
						$element .=                '<tr>';
						$element .=						'<td class="thead">UPLOADED</td>';
						$element .=                    '<td>'.$this->time_format($project->create_on, 'h:i a - d/m/y').'</td>';
						$element .=                '</tr>';
						$element .=                '<tr>';
						$element .=						'<td class="thead">DOWNLOADED</td>';
						$element .=                    '<td>'.($project->downloaded > 0?$project->downloaded: 0).' Times</td>';
						$element .=                '</tr>';
						$element .=        '</table>';
						$element .=    '</div>';
						$element .='</div>';
					}

					$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $postlinkProjects WHERE student_id = $student_id");
					if($rowcount>$_POST['limit']){
						$element .= '<button class="show-loadmore">Show more</button>';
					}
					echo $element;
					wp_die();
				}else{
					echo '<div class="warns" style="display:block">No project found!</div>';
					wp_die();
				}
			}
		}else{
			echo "login to see";
			wp_die();
		}
	}//end

	// show_all_project_data
	function show_all_project_data(){
		global $wpdb;
		$current_user = wp_get_current_user();
		$student_id = $current_user->ID;
		$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';

		if(isset($_POST['limit'], $_POST['start'])){

			$projects = $wpdb->get_results("SELECT * FROM $postlinkProjects ORDER BY project_id DESC LIMIT ".$_POST['start'].",".$_POST['limit']."");

			if($wpdb->num_rows>0){
				foreach($projects as $project){
					$studentID = $project->student_id;
					$student = get_userdata( $studentID );
					$student_name = $student->display_name;
					$output .= '<div class="project">';
					$output .= '<div class="project_img_wrapp">';
					$output .= 	'<img class="project_img" src="'.$project->project_image.'" alt="">';
					$output .= '</div>';
					$output .= 	'<h4>'.$project->project_name.'</h4>';
					$output .= 	'<h5 class="student_name">'.$student_name.'</h5>';
					$output .= 	'<div class="down_count">';
					$output .= 		'<img src="'.plugin_dir_url( __FILE__ )."img/down1.png".'" alt="">';
					$output .= 		'<span class="dcounts"> '.($project->downloaded > 0?$project->downloaded: 0).' </span>';
					if(current_user_can( 'administrator' )){
						$output .=        '<span class="project_menus">°°°</span>';//dropdown start
						$output .=        '<div class="uploaded_dropdowns">';
						$output .=            '<span class="uploaded_items">';
						$output .= 			'<input data-value="'.$project->project_id.'" data-id="'.$project->student_id.'" class="edit_count" type="number" placeholder="'.($project->downloaded > 0?$project->downloaded: 0).'"';
						$output .= 		'</span>';
						$output .=        '</div>';//dropdown end
					}
					$output .= 	'</div>';
					$output .= '<p class="project_dsc">'.ucfirst($project->project_desc).'</p>';
					$output .= 	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
            		$output .= '</div>';
				}
				
				
				$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $postlinkProjects");
				if($rowcount>$_POST['limit']){
					$output .= '<div class="loadmore_project"><button class="load_btn">Load more</button></div>';
				}
				echo $output;
				wp_die();
			}else{
				wp_die();
			}
			wp_die();
		}
	}

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
			$project_id =  intval($_POST['project_id']);

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

	function live_searching_students_name(){//Live search projects
		global $wpdb;
		$postlinkProjects = $wpdb->prefix .'postlinkProjects_v1';
		$wp_user_table = $wpdb->prefix .'users';

		if(isset($_POST['liveSearch'])){
			$svalue = sanitize_text_field( $_POST['svalue'] );
			$nonce = $_POST['noncess'];

			if(wp_verify_nonce($nonce , "livesearch_nonce_val" )){
				if($svalue == ""){
					$projects = $wpdb->get_results("SELECT p.*,u.* FROM $postlinkProjects p JOIN $wp_user_table u ON p.student_id = u.ID WHERE u.display_name LIKE '%$svalue%' ORDER BY p.project_id DESC LIMIT ".$_POST['start'].",".$_POST['limit']."");
				}else{
					$projects = $wpdb->get_results("SELECT p.*,u.* FROM $postlinkProjects p JOIN $wp_user_table u ON p.student_id = u.ID WHERE u.display_name LIKE '%$svalue%' ORDER BY p.project_id DESC");
				}
				
				if($wpdb->num_rows>0){
					foreach($projects as $project){
						$studentID = $project->student_id;
						$student = get_userdata( $studentID );
						$student_name = $student->display_name;
						$output .= '<div class="project">';
						$output .= '<div class="project_img_wrapp">';
						$output .= 	'<img class="project_img" src="'.$project->project_image.'" alt="">';
						$output .= '</div>';
						$output .= 	'<h4>'.$project->project_name.'</h4>';
						$output .= 	'<h5 class="student_name">'.$student_name.'</h5>';
						$output .= 	'<div class="down_count">';
						$output .= 		'<img src="'.plugin_dir_url( __FILE__ )."img/down1.png".'" alt="">';
						$output .= 		'<span class="dcounts"> '.($project->downloaded > 0?$project->downloaded: 0).' </span>';
						if(current_user_can( 'administrator' )){
							$output .=        '<span class="project_menus">°°°</span>';//dropdown start
							$output .=        '<div class="uploaded_dropdowns">';
							$output .=            '<span class="uploaded_items">';
							$output .= 			'<input data-value="'.$project->project_id.'" data-id="'.$project->student_id.'" class="edit_count" type="number" placeholder="'.($project->downloaded > 0?$project->downloaded: 0).'"';
							$output .= 		'</span>';
							$output .=        '</div>';//dropdown end
						}
						$output .= 	'</div>';
						$output .= '<p class="project_dsc">'.ucfirst($project->project_desc).'</p>';
						$output .= 	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
						$output .= '</div>';
					}

					if($svalue == ""){
						$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $postlinkProjects");
						if($rowcount>$_POST['limit']){
							$output .= '<div class="loadmore_project"><button class="load_btn">Load more</button></div>';
						}
					}
					echo $output;
					wp_die();
				}else{
					wp_die();
				}
			}else{
				echo "invalid request";
				wp_die();
			}
		}

		if(isset($_POST['category_search'])){
			$svalue = sanitize_text_field( $_POST['svalue'] );
			if($svalue == ""){
				$projects = $wpdb->get_results("SELECT p.*,u.* FROM $postlinkProjects p JOIN $wp_user_table u ON p.student_id = u.ID WHERE u.display_name LIKE '%$svalue%' ORDER BY p.project_id DESC LIMIT ".$_POST['start'].",".$_POST['limit']."");
			}else{
				$projects = $wpdb->get_results("SELECT * FROM $postlinkProjects  WHERE category LIKE '%$svalue%'");
			}

			if($wpdb->num_rows>0){
				foreach($projects as $project){
					$studentID = $project->student_id;
					$student = get_userdata( $studentID );
					$student_name = $student->display_name;
					$output .= '<div class="project">';
					$output .= '<div class="project_img_wrapp">';
					$output .= 	'<img class="project_img" src="'.$project->project_image.'" alt="">';
					$output .= '</div>';
					$output .= 	'<h4>'.$project->project_name.'</h4>';
					$output .= 	'<h5 class="student_name">'.$student_name.'</h5>';
					$output .= 	'<div class="down_count">';
					$output .= 		'<img src="'.plugin_dir_url( __FILE__ )."img/down1.png".'" alt="">';
					$output .= 		'<span class="dcounts"> '.($project->downloaded > 0?$project->downloaded: 0).' </span>';
					if(current_user_can( 'administrator' )){
						$output .=        '<span class="project_menus">°°°</span>';//dropdown start
						$output .=        '<div class="uploaded_dropdowns">';
						$output .=            '<span class="uploaded_items">';
						$output .= 			'<input data-value="'.$project->project_id.'" data-id="'.$project->student_id.'" class="edit_count" type="number" placeholder="'.($project->downloaded > 0?$project->downloaded: 0).'"';
						$output .= 		'</span>';
						$output .=        '</div>';//dropdown end
					}
					$output .= 	'</div>';
					$output .= '<p class="project_dsc">'.ucfirst($project->project_desc).'</p>';
					$output .= 	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
					$output .= '</div>';
				}
		
				if($svalue == ""){
					$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $postlinkProjects");
					if($rowcount>$_POST['limit']){
						$output .= '<div class="loadmore_project"><button class="load_btn">Load more</button></div>';
					}
				}
				echo $output;
				wp_die();
			}else{
				wp_die();
			}
		}

		if(isset($_POST['recentvalue'])){
			if(isset($_POST['category'])){
				$category = sanitize_text_field( $_POST['category'] );
			}
			$projects = $wpdb->get_results("SELECT * FROM $postlinkProjects WHERE category LIKE '%$category%' AND create_on >= (CURRENT_DATE - INTERVAL 1 MONTH) ORDER BY project_id DESC");

			if($wpdb->num_rows>0){
				foreach($projects as $project){
					$studentID = $project->student_id;
					$student = get_userdata( $studentID );
					$student_name = $student->display_name;
					$output .= '<div class="project">';
					$output .= '<div class="project_img_wrapp">';
					$output .= 	'<img class="project_img" src="'.$project->project_image.'" alt="">';
					$output .= '</div>';
					$output .= 	'<h4>'.$project->project_name.'</h4>';
					$output .= 	'<h5 class="student_name">'.$student_name.'</h5>';
					$output .= 	'<div class="down_count">';
					$output .= 		'<img src="'.plugin_dir_url( __FILE__ )."img/down1.png".'" alt="">';
					$output .= 		'<span class="dcounts"> '.($project->downloaded > 0?$project->downloaded: 0).' </span>';
					if(current_user_can( 'administrator' )){
						$output .=        '<span class="project_menus">°°°</span>';//dropdown start
						$output .=        '<div class="uploaded_dropdowns">';
						$output .=            '<span class="uploaded_items">';
						$output .= 			'<input data-value="'.$project->project_id.'" data-id="'.$project->student_id.'" class="edit_count" type="number" placeholder="'.($project->downloaded > 0?$project->downloaded: 0).'"';
						$output .= 		'</span>';
						$output .=        '</div>';//dropdown end
					}
					$output .= 	'</div>';
					$output .= '<p class="project_dsc">'.ucfirst($project->project_desc).'</p>';
					$output .= 	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
					$output .= '</div>';
				}
				
				echo $output;
				wp_die();
			}else{
				wp_die();
			}
		}

		if(isset($_POST['popularvalue'])){
			if(isset($_POST['category'])){
				$category = sanitize_text_field( $_POST['category'] );
			}
			$projects = $wpdb->get_results("SELECT * FROM $postlinkProjects WHERE downloaded >= (SELECT MIN(downloaded) FROM $postlinkProjects) AND category LIKE '%$category%' ORDER BY downloaded DESC");

			if($wpdb->num_rows>0){
				foreach($projects as $project){
					$studentID = $project->student_id;
					$student = get_userdata( $studentID );
					$student_name = $student->display_name;
					$output .= '<div class="project">';
					$output .= '<div class="project_img_wrapp">';
					$output .= 	'<img class="project_img" src="'.$project->project_image.'" alt="">';
					$output .= '</div>';
					$output .= 	'<h4>'.$project->project_name.'</h4>';
					$output .= 	'<h5 class="student_name">'.$student_name.'</h5>';
					$output .= 	'<div class="down_count">';
					$output .= 		'<img src="'.plugin_dir_url( __FILE__ )."img/down1.png".'" alt="">';
					$output .= 		'<span class="dcounts"> '.($project->downloaded > 0?$project->downloaded: 0).' </span>';
					if(current_user_can( 'administrator' )){
						$output .=        '<span class="project_menus">°°°</span>';//dropdown start
						$output .=        '<div class="uploaded_dropdowns">';
						$output .=            '<span class="uploaded_items">';
						$output .= 			'<input data-value="'.$project->project_id.'" data-id="'.$project->student_id.'" class="edit_count" type="number" placeholder="'.($project->downloaded > 0?$project->downloaded: 0).'"';
						$output .= 		'</span>';
						$output .=        '</div>';//dropdown end
					}
					$output .= 	'</div>';
					$output .= '<p class="project_dsc">'.ucfirst($project->project_desc).'</p>';
					$output .= 	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
					$output .= '</div>';
				}
				
				echo $output;
				wp_die();
			}else{
				wp_die();
			}
		}

		if(isset($_POST['twoselected'])){
			if(isset($_POST['category'])){
				$category = sanitize_text_field( $_POST['category'] );
			}
			$projects = $wpdb->get_results("SELECT * FROM $postlinkProjects WHERE downloaded >= (SELECT MIN(downloaded) FROM $postlinkProjects) AND category LIKE '%$category%' AND create_on >= (CURRENT_DATE - INTERVAL 1 MONTH) ORDER BY downloaded DESC");

			if($wpdb->num_rows>0){
				foreach($projects as $project){
					$studentID = $project->student_id;
					$student = get_userdata( $studentID );
					$student_name = $student->display_name;
					$output .= '<div class="project">';
					$output .= '<div class="project_img_wrapp">';
					$output .= 	'<img class="project_img" src="'.$project->project_image.'" alt="">';
					$output .= '</div>';
					$output .= 	'<h4>'.$project->project_name.'</h4>';
					$output .= 	'<h5 class="student_name">'.$student_name.'</h5>';
					$output .= 	'<div class="down_count">';
					$output .= 		'<img src="'.plugin_dir_url( __FILE__ )."img/down1.png".'" alt="">';
					$output .= 		'<span class="dcounts"> '.($project->downloaded > 0?$project->downloaded: 0).' </span>';
					if(current_user_can( 'administrator' )){
						$output .=        '<span class="project_menus">°°°</span>';//dropdown start
						$output .=        '<div class="uploaded_dropdowns">';
						$output .=            '<span class="uploaded_items">';
						$output .= 			'<input data-value="'.$project->project_id.'" data-id="'.$project->student_id.'" class="edit_count" type="number" placeholder="'.($project->downloaded > 0?$project->downloaded: 0).'"';
						$output .= 		'</span>';
						$output .=        '</div>';//dropdown end
					}
					$output .= 	'</div>';
					$output .= '<p class="project_dsc">'.ucfirst($project->project_desc).'</p>';
					$output .= 	'<button class="download_btn" data-value="'.$project->project_id.'">DOWNLOAD</button>';
					$output .= '</div>';
				}
				
				echo $output;
				wp_die();
			}else{
				wp_die();
			}
		}
	}
}