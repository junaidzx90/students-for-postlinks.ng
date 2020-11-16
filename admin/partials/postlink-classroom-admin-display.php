<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.postlink.ng/
 * @since      1.0.0
 *
 * @package    Postlink_Classroom
 * @subpackage Postlink_Classroom/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="tabs">
  <ul id="tabs-nav">
    <li><a href="#tab1">General Settings</a></li>
    <li><a href="#tab2">Projects</a></li>
    <li><a href="#tab3">About Pages</a></li>
  </ul> <!-- END tabs-nav -->
  <div id="tabs-content">
    <?php
    global $wpdb; //Define wpdb global variable
    $postlinkClassroom = $wpdb->prefix . 'postlinkClassroom_v1'; //Define postlinkClassroom table with wp prefix
    $posts;
    $user;
    $user_data= $wpdb->get_results("SELECT * FROM $postlinkClassroom");
    if($wpdb->num_rows>0){
      foreach($user_data as $data){
        $posts =  $data->post_per_page;
        $user = $data->user_role;
      }
    }
?>
    <div id="tab1" class="tab-content">
      <h2>General Settings</h2>
      <hr>
      <div class="rows">
        <div class="col-3">
          <div class="form-group">
            <label class="lab" for="posts-display">How many posts will be shown per load?</label>
            <br>
            <select class="inp-des per-page-inp" id="posts-display">
              <?php if(isset($posts)){
                  echo '<option selected="selected" disabled value="'.$posts.'">'.$posts.' Posts Selected</option>';
                }else{
                  echo '<option selected="selected" disabled>Default</option>';
                }
                ?>
              <option value="2">2 Posts per load</option>
              <option value="5">5 Posts per load</option>
              <option value="10">10 Posts per load</option>
            </select>
          </div>
          <div class="form-group">
            <label class="lab" for="select-user">Which users can see their posts?</label>
            <br>
            <select class="inp-des user-selects" id="select-user">
              <?php if(isset($user)){
                  echo '<option selected="selected" disabled value="'. $user.'">'.$user.' Selected</option>';
                }else{
                  echo '<option selected="selected" disabled>Default</option>';
                }
                echo wp_dropdown_roles();
                ?>
            </select>
          </div>
          <div class="form-group">
            <label class="lab" for="posts_slug">Posts slug for upload page</label><br>
            <?php
                  $posts_slug= $wpdb->get_var("SELECT posts_slug FROM $postlinkClassroom");
                ?>
            <input type="text" name="posts_slug" id="posts_slug"
              placeholder="<?php echo (isset($posts_slug)?$posts_slug:'my-posts') ?>">
          </div>
          <button class="save-btn">Save</button>
          <span class="warning"></span>
        </div>
        <div class="col-3">
          <input type="text" class="category" placeholder="Category name">
          <button class="add-btn">Add</button><br>
          <span class="warning2"></span>
          <div class="categories">
            <table class="category_table">
              <thead>
                <tr>
                  <td class="sl">SL</td>
                  <td>Categories</td>
                </tr>
              </thead>
              <tbody>
              <!-- Category will be here -->
              </tbody>
            </table>
            
          </div>
        </div>
      </div>
    </div>
    <!-- Manage projects -->
    <div id="tab2" class="tab-content">
      <h2>Projects management</h2>
      <hr>
      <input type="text" placeholder="Search here" class="search_project">
      <table class="project_table">
        <thead>
          <tr class="head">
            <th>SL</th>
            <th>Project Name</th>
            <th>Student Name</th>
            <th>Downloaded</th>
            <th class="action" colspan="2">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php echo Postlink_Classroom_Admin::get_all_projects() ?>
        </tbody>
      </table>
    </div>
    <!-- About pages -->
    <div id="tab3" class="tab-content">
      <!-- Empty for now -->
      <h3>This plugin contains three page templates</h3>
      <ol class="plugin_desc">
        <li>
          <h4>Postlink Posts</h4>
          <ul>
            <li>
              This template displays students posts, it shows five posts by default, if there are more than five posts,
              the <b>loadmore</b> button will be displayed to view other posts.
            </li>
          </ul>
        </li>
        <li>
          <h4>Postlink Upload</h4>
          <ul>
            <li>
              This template allows students to uploads their own projects.
              This template is very meticulously designed to give students mobile applications and a small description
              of it and instantly can see the details of the project where they will be able to see all the data of
              their project. By default it shows five projects, if there are more than five the <b>loadmore</b> button
              will be displayed.
            </li>
            <li>And also allows them to delete their published project..</li>

            <li>The default sidebar is displayed on the right side of this template. And on the left side, it has a
              profile image with a link to see published posts.</li>

            <li>Super admin can add eliminator widgets here to make this left side more beautiful.</li>

            <li>This template has been created in such a way that it can also be used as a profile page.</li>
          </ul>
        </li>
        <li>
          <h4>Postlink Download</h4>
          <ul>
            <li>This template allows everyone to download projects published by students.</li>
            <li>This template allows downloading all of the published projects.
              And anyone can search for a specific student or specific project to see data.
              By default it shows twelve projects, if there are more than twelve the <b>showmore</b> button will be
              displayed.
              <b>Only super admin can edit downloads number of the project from frontend or backend.</b></li>
          </ul>
        </li>
        <small class="credit"><b>This plugin created by <a
              href="https://www.fiverr.com/junaidzx90">junaidzx90</a></b></small>
      </ol>
    </div>
  </div> <!-- END tabs-content -->
</div> <!-- END tabs -->