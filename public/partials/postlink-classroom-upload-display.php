<?php
ob_start();
if ( !is_user_logged_in() )  {
    wp_safe_redirect( home_url( "/login" ) );
    exit;
}
wp_head();
$user = wp_get_current_user();
$default_role = Postlink_Classroom_Public::get_current_user();
get_header();
global $wpdb; //Define wpdb global variable
$postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; //Define postlinkClassroom table with wp prefix
?>
<div class="up_contaier">
    <div class="up_row">
        <!-- Left sidebar -->
        <div class="col_3 leftwidget">
        <?php
            if ( $user->roles[0] === $default_role ) {?>
                <div class="user">
                        <?php 
                            global $wpdb;
                            $current_user = wp_get_current_user();
                            $student_name = $current_user->display_name;
                            $avater = get_avatar_url( $current_user->ID );
                        ?>
                        <img class="profile_img" src="<?php echo $avater; ?>" alt="profile-image">
                        <div class="user_wrapp">
                            
                            <?php
                                $student_post_slug = "";
                                $posts_slug= $wpdb->get_var("SELECT posts_slug FROM $postlinkClassroom");

                                if($posts_slug){//set my posts slug
                                    $student_post_slug = $posts_slug;
                                }else{
                                    $student_post_slug = "my-posts";
                                }
                            ?>
                            <span class="name"><?php echo  $student_name; ?></span>
                            <a href="<?php echo home_url( '/'.$student_post_slug ) ?>" target="_devjoo">
                                <span class="user_rcnt_pst">View recent post</span>
                            </a>
                        </div>
                </div>
            <?php } ?>
            <div class="alimentor_areas">
                <?php the_content(); ?>
            </div>
        </div>
        <!--//// Left sidebar -->
        <?php
             if ( $user->roles[0] === $default_role ) { ?>
                <div class="col_6">
                    <!-- Upload section -->
                    <div class="upload_section">
                        <form id="uploadForm" method="post" enctype="multipart/form-data">
                            
                            <div class="form_area">
                                <div class="c_row">
                                    <div id="form-control" class="title">
                                        <span class="existName"></span>
                                        <h6>Project name</h6>
                                        <input name="project_name" maxlength="30" type="text" class="project_name" placeholder="Unique name expected">
                                    </div>  
                                    <div id="form-control" class="categories_select">
                                        <h6>Select category</h6>
                                        <select name="category" id="categories">
                                            <option value="">Select category</option>
                                            <?php
                                                global $wpdb;
                                                $postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';
                                                $categories = $wpdb->get_results("SELECT * FROM $postlinkCategories ORDER BY category_id DESC");

                                                if($wpdb->num_rows>0){
                                                    foreach($categories as $category){
                                                        echo '<option value="'.$category->category.'">'.ucfirst($category->category).'</option>';
                                                    }
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <h6 class="txt_areas_ttl">Project description</h6>
                                <textarea maxlength="70" class="up_inp file_desc" name="file_desc" placeholder="Write a brief description of the project" rows="2"></textarea>
                                <p class="short_desc">Write in 150 characters</p>
                                
                                <div class="icon_mark">
                                    <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/mark.png'?>" />
                                </div>
                                <input type="hidden" name="upload_file_nonce" value="<?php echo wp_create_nonce( "upload_file_nonce_val" ) ?>">
                                <button class="image_upload_btn">Select project image
                                    <input name="image_inp" id="image_inp" type="file" class="image_inp" />
                                </button>
                            </div>

                        
                            <div class="progress">
                                <span  class="progressbar"></span>
                            </div>
                            <div class="file_catch">
                                <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/mark.png'?>" alt="mark">
                                <span class="file_path"></span>
                            </div>
                            <div class="upload-btn-wrapper">
                                <button class="upbtn">Publish project
                                    <input name="fileUpInp" id="fileUpInp" type="file" class="fileinputbox" />
                                </button>
                                <input type="submit" id="btnSubmit" value="Submit" class="uppublishbtn" />
                            </form>
                        </div>
                    </div>
                    <div class="up_warning"></div>
                    <!--/// Upload section -->

                    <!-- Uploaded file -->
                    
                    <div id="loader-icon">
                        <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/LoaderIcon.gif'?>" />
                    </div>
                    <div id="targetLayer"></div>
                    <!--/// Uploaded file -->

                </div>
            <?php }else{
                echo '<span class="warn">Please <a href="/login">login</a> to upload your project</span>';
            } ?>
        <div class="col_3 rightwidget">
            <!--Dynamic Sidebar -->
            <?php get_sidebar() ?>
        </div>
    </div>
</div>
<?php
get_footer();
wp_footer();