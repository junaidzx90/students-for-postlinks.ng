    <?php wp_head() ?>
    <?php get_header() ?>
    <div id="projects">
        <div class="project_search">
            <div class="filter">
                <select class="search category">
                <option value="">Select categories</option>
                <?php
                    global $wpdb;
                    $postlinkCategories = $wpdb->prefix . 'postlinkCategories_v1';
                    $categories = $wpdb->get_results("SELECT * FROM $postlinkCategories");
                    foreach($categories as $category){
                        echo '<option value="'.$category->category.'">'.$category->category.'</option>';
                    }
                ?>
                </select>
                <div class="filter_control">
                    <div class="button-group-pills text-center" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="checkbox" name="options" class="recent_project">
                            <div>Recent apps</div>
                        </label>
                    </div>
                </div>
                <div class="filter_control">
                    <div class="button-group-pills text-center" data-toggle="buttons">
                        <label class="btn btn-default">
                            <input type="checkbox" name="options" class="popular_project">
                            <div>Popular apps</div>
                        </label>
                    </div>
                </div>
            </div>
                
                <div class="search_box">
                    <input placeholder="Search student name" type="text" class="search livesearch">
                    <input type="hidden" name="livesearch_nonce" value="<?php echo wp_create_nonce( "livesearch_nonce_val" ) ?>">
                </div>
        </div>
        <div class="project_item">
            <!-- Content will be here -->
        </div>
        <div id="loader-icon">
                    <img src="<?php echo plugin_dir_url( __FILE__ ).'../img/LoaderIcon.gif'?>" />
        </div>
        <div class="up_warning"></div>
    </div>
    <?php get_footer() ?>
    <?php wp_footer() ?>