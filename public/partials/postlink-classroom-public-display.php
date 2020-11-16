<?php
ob_start();
if ( !is_user_logged_in() )  {
    wp_safe_redirect( home_url( "/login" ) );
    exit;
}
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/public/partials
 */
wp_head();
get_header();
$default_role = Postlink_Classroom_Public::get_current_user();
?>
<!-- Main student posts wrapper start from here -->
<div id="alimentor_widgets">
<?php the_content(); ?>
</div>
<?php

$user = wp_get_current_user();

if ( $user->roles[0] === $default_role || $user->roles[0] === 'administrator' ) { ?>
        <div id="main_wrapper">
            <div id="post_contents">
                <div class="loaddata">
                <h1 class="published_post"></h1>
                    <!-- Dtat will be load here -->
                </div>
            </div>
            <div class="psidebar">
                <?php get_sidebar(); ?>
            </div>
        </div>
<?php }else{
    echo '<span class="warn">Please <a href="/login">login</a> to view your posts</span>';
} ?>
<!-- Main student posts wrapper end -->
<?php
get_footer();
wp_footer();
?>