<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/includes
 * @author     Postlink <demo@gmail.com>
 */
class Postlink_Classroom_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb; //Define wpdb global variable
		$postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; //Define postlinkClassroom table with wp prefix
		$postlinkProjects = $wpdb->prefix . 'postlinkProjects_v1'; //Define postlinkProjects_v1 table with wp prefix
		$postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';//Define postlinkCategories table with wp prefix

		$postlinkClassroomSql = "CREATE TABLE IF NOT EXISTS `$postlinkClassroom` ( 
			`student_id` INT NOT NULL AUTO_INCREMENT,
			`post_per_page` INT NOT NULL,
			`user_role` VARCHAR(20) NOT NULL,
			`posts_slug` VARCHAR(50) NOT NULL,
			`category` VARCHAR(50) NOT NULL,
			PRIMARY KEY  (`student_id`)) ENGINE = InnoDB";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($postlinkClassroomSql); //Action for create table postlinkClassroom

			$postlinkProjectsSql = "CREATE TABLE IF NOT EXISTS `$postlinkProjects` ( 
				`project_id` INT NOT NULL AUTO_INCREMENT,
				`student_id` INT NOT NULL,
				`project_name` VARCHAR(30) NOT NULL,
				`category` VARCHAR(50) NOT NULL,
				`project_desc` VARCHAR(70) NOT NULL,
				`project_image` VARCHAR(1000) NOT NULL,
				`project_file` VARCHAR(1000) NOT NULL,
				`project_type` VARCHAR(50) NOT NULL,
				`project_size` VARCHAR(50) NOT NULL,
				`downloaded` INT NOT NULL,
				`create_on` TIMESTAMP NOT NULL,
				PRIMARY KEY  (`project_id`)) ENGINE = InnoDB";
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($postlinkProjectsSql); //Action for create table postlinkClassroom

				$postlinkCategoriesSQL = "CREATE TABLE IF NOT EXISTS `$postlinkCategories` ( 
					`category_id` INT NOT NULL AUTO_INCREMENT,
					`category` VARCHAR(50) NOT NULL,
					PRIMARY KEY  (`category_id`)) ENGINE = InnoDB";
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($postlinkCategoriesSQL);

	}

}
