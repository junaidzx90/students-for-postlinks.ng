<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
removefolder();
function removefolder() { 
	$upload_dir   = wp_upload_dir();
	$dir = $upload_dir['path'].'/devjoo_postlink_plugin/';

	$it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
	$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	foreach($it as $file) {
		if ($file->isDir()) rmdir($file->getPathname());
		else unlink($file->getPathname());
	}
	rmdir($dir);

	global $wpdb; //Define wpdb global variable
	$postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; 
	$postlinkProjects = $wpdb->prefix . 'postlinkProjects_v1';
	$postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';
	$wpdb->query("DROP TABLE IF EXISTS $postlinkClassroom");
	$wpdb->query("DROP TABLE IF EXISTS $postlinkProjects");
	$wpdb->query("DROP TABLE IF EXISTS $postlinkCategories");
}