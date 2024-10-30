<?php
# Register and load the widget
//include '/curl/HubspotRequest.php';

function Hub2Word_load_widget() {
    register_widget('Hubpost_Widget');
}

add_action('widgets_init', 'Hub2Word_load_widget');

# Creating the widget

class Hubpost_Widget extends WP_Widget {
//    private $blogInfo;

    /**
     * Sets up the widgets name etc
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'hub2word_widget',
            'description' => 'Widget to show the latest blog posts from your HubSpot account.',
        );
        parent::__construct('hub2word_widget', 'Recent Hubspot Blog Posts', $widget_ops);
    }

    # Creating widget front-end

    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $hsblog = $instance['hsblog'];
        $limit = $instance['limit'];
        $author = $instance['author'];
        $date = $instance['date'];
        $description = $instance['description'];
        $feat_img = $instance['feat_img'];

        # before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if($hsblog){
            //get posts from HubSpot
            $reqUrl = "https://api.hubapi.com/content/api/v2/blog-posts";
            $response = HubspotRequest::getHubspotBlogPosts($reqUrl, $hsblog, $limit);
            if ($response) {
                $lists = json_decode($response['response'], true);

                // echo "<pre>";
                // var_dump($lists['objects']);
                // echo "</pre>";
                if ($lists['total'] > 0) {
                    $html = '<div class="Hub2Word-recent-posts"><ul  class="Hub2Word-recent-list">';
                    foreach ($lists['objects'] as $blog_post) {
                        $html .= '<li class="Hub2Word-recent-post">';
                        if ($feat_img) { $html .=     

                                    '<div class="Hub2Word-feat-img"><img src="' . $blog_post['featured_image'] . '" style="height: auto;max-width:100px;"></div>'; }

                        $html .= '  <div class="Hub2Word-post-body">    
                                        <a class="Hub2Word-post-link" href="' . $blog_post['absolute_url'] . '" target="_blank">' . $blog_post['name'] . '</a>
                                        <div class="Hub2Word-entry-meta ">
                                            <div class="Hub2Word-meta-post">
                                                <ul class="Hub2Word-entry-meta">
                                                    <li class="Hub2Word-meta-categories">';

                        if ($date) { $html .=            '<li class="Hub2Word-meta-date">' . date("M d, Y", $blog_post['publish_date']/1000) . '</li>'; }
                        if ($author) {$html .=           '<li class="Hub2Word-meta-author">By ' . $blog_post['blog_author']['display_name'] . '</li>'; }
                                                        
                        $html .=                    '</li>
                                                </ul>
                                            </div>
                                        </div>';
                        if ($description) {$html .= 
                                        '<div class="Hub2Word-entry-content">
                                            <p>' . wp_strip_all_tags($blog_post['post_summary']) . '</p>
                                        </div>
                                    </div>'; }
                        $html .='</li>';
                    }
                    $html .= '</ul></div>';

                    echo __("$html", 'hub2word');

                    echo $args['after_widget'];
                }
            }
        }
    }

    # Widget Backend

    public function form($instance) {
        // get blog from hubspot
        $reqUrl = "https://api.hubapi.com/content/api/v2/blogs";
        $blogs = HubspotRequest::getHubspotBlogs($reqUrl);

        $blog_list = json_decode($blogs['response'], true);

        $def_blog = $blog_list['objects'][0];

        $title = isset($instance['title']) ? $instance['title'] : '';
        $hsblog = isset($instance['hsblog']) ? $instance['hsblog'] : '';
        $limit = isset($instance['limit']) ? $instance['limit'] : 5;
        //additional settings
        $author = isset($instance['author']) ? '1' : '0';
        $date = isset($instance['date']) ? '1' : '0';
        $description = isset($instance['description']) ? '1' : '0';
        $feat_img = isset($instance['feat_img']) ? '1' : '0';

        ?> 
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Select a HubSpot Blog'); ?></label>
            <select class="widefat" id="<?php echo $this->get_field_id('hsblog'); ?>" name="<?php echo $this->get_field_name('hsblog'); ?>">
                    <?php foreach($blog_list['objects'] as $blog){ ?>
                        <option value="<?php echo $blog['id']; ?>" <?php if (esc_attr($hsblog) == $blog['id'] ) echo 'selected' ; ?>><?php echo $blog['label']; ?>
                    <?php } ?>
                    </option>
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of posts to show:'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($limit); ?>" size="3">
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('author'); ?>" <?php checked( $instance['author'] ); ?> name="<?php echo $this->get_field_name('author'); ?>" type="checkbox" value="<?php echo $author; ?>">
            <label for="<?php echo $this->get_field_id('author'); ?>"><?php _e('Include Post Author'); ?></label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('date'); ?>" <?php checked( $instance['date'] ); ?> name="<?php echo $this->get_field_name('date'); ?>" type="checkbox" value="<?php echo $date; ?>">
            <label for="<?php echo $this->get_field_id('date'); ?>"><?php _e('Include Post Date'); ?></label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('description'); ?>" <?php checked( $instance['description'] ); ?> name="<?php echo $this->get_field_name('description'); ?>" type="checkbox" value="description">
            <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Include Post Description'); ?></label>
        </p>
        <p>
            <input class="checkbox" id="<?php echo $this->get_field_id('feat_img'); ?>" <?php checked( $instance['feat_img'] ); ?> name="<?php echo $this->get_field_name('feat_img'); ?>" type="checkbox" value="feat_img">
            <label for="<?php echo $this->get_field_id('feat_img'); ?>"><?php _e('Include Featured Image'); ?></label>
        </p>
        <?php
    }

    # Updating widget replacing old instances with new

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['hsblog'] = (!empty($new_instance['hsblog'])) ? strip_tags($new_instance['hsblog']) : $def_blog['id'];
        $instance['limit'] = (!empty($new_instance['limit'])) ? strip_tags($new_instance['limit']) : 4;
        $instance['author'] = $new_instance['author'] ? 1 : 0;
        $instance['date'] = $new_instance['date'] ? 1 : 0;
        $instance['description'] = $new_instance['description'] ? 1 : 0;
        $instance['feat_img'] = $new_instance['feat_img'] ? 1 : 0;
        return $instance;
    }

}

# Class wpb_widget ends here