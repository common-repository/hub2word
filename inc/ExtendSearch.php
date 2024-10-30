<?php

/**
 * Search HubSpot Blogs
 * @return HTML
 */

if(get_option('include_search')){

    $H2W_Run = false;

    function Hub2Word_search($query) {
      if ($query->is_search && !is_admin()) {

        global $wp, $wp_query;

        $query_type = array();

        if(get_option('search_blogs')){
            $query_type[] = 'BLOG_POST';
        }
        if(get_option('search_lpages')){
            $query_type[] = 'LANDING_PAGE';
        }
        if(get_option('search_pages')){
            $query_type[] = 'SITE_PAGE';
        }

        $queryString = '';
        if (count($query_type) > 0) {
            $queryString = '';
            foreach ($query_type as $_index => $queryStr) {
                if ($_index > 0) {
                    $queryString .= "&type=";
                }
                $queryString .= $queryStr;
            }
        }

        $portalId = get_option('hub_id');

        $reqUrl = "http://api.hubapi.com/contentsearch/v2/search";
        $results = HubspotRequest::getHubspotSearch($reqUrl, $portalId, $query, $queryString);
     
        $blogSearch = json_decode($results['response']);
        $blogSearches = $blogSearch->results;

        $i = 1;

        $wp_post = array();

          foreach ($blogSearches as $blog) {
            
            if(isset($blog->publishedDate)){

                $title = strip_tags($blog->title);

                if($blog->type == 'BLOG_POST'){
                    $type = 'post';
                } elseif($blog->type == 'LANDING_PAGE' || $blog->type == 'SITE_PAGE'){
                    $type = 'post'; //@todo temp fix because page type doesn't use Hub2Word_external_link in the results view
                }

                $post[$i] = new stdClass();
                $post[$i]->ID = $blog->id;
                $post[$i]->post_author = $blog->authorFullName;
                $post[$i]->post_date = date("Y-m-d H:i:s", $blog->publishedDate/1000); 
                $post[$i]->post_date_gmt = date("Y-m-d H:i:s", $blog->publishedDate/1000);
                $post[$i]->post_title = wp_strip_all_tags($title);
                $post[$i]->post_content = $blog->description;
                $post[$i]->post_status = 'publish';
                $post[$i]->post_name = $blog->url;
                $post[$i]->post_type = $type;
                $post[$i]->filter = 'raw'; // important!
                 
                // Convert to WP_Post object
                $wp_post[$i] = new WP_Post( $post[$i] );

                // Add the fake post to the cache
                wp_cache_set( $post[$i]->ID, $wp_post[$i], 'posts', 60 );

                  update_post_meta( $post[$i]->ID, 'Hub2Word_external_link', $blog->url );

                if($blog->featuredImageUrl){
                  update_post_meta( $post[$i]->ID, 'Hub2Word_feat_image', $blog->featuredImageUrl );
                  update_post_meta( $post[$i]->ID, 'Hub2Word', 'Yes' );
                }

                $wp_query->post_count++;
                $wp_query->found_posts++;

                $i++;

            }

          }

          $GLOBALS['wp_post'] = $wp_post;
          $GLOBALS['wp_query'] = $wp_query;
          $wp->register_globals();

          $H2W_Run = true;

      }

    }
    add_action('pre_get_posts','Hub2Word_search'); 

    if($H2W_Run){

        function Hub2Word_posts_override(){
            if ( is_search() ) {
                global $wp_post, $wp_query;

                if($wp_post){

                  foreach ($wp_post as $key) {

                      if ($wp_query->post_count==0)
                        $wp_query->posts[] = $key->ID;
                      else
                        array_unshift($wp_query->posts, $key->ID);

                  }
                  
                }
            }
        }
        add_action( 'found_posts', 'Hub2Word_posts_override' );

        /**
         * Parse post Featured Image and replace it with HubSpot Featured Image.
         *
         * @wp-hook post_link
         * @param   string $link
         * @param   object $post
         * @return  string
         */

        function Hub2Word_image_url($post) {
            $url = get_post_meta($post, 'Hub2Word_feat_image', true);
            if (!$url)
                $url = '';

            return $url;
        }

        // function Hub2Word_replace_attachment_url($att_url, $att_id) {
        //     $h2w = get_post_meta($post->ID, 'Hub2Word', true);
        //     if ($h2w == 'Yes')
        //         return $att_url;

        //     if ($att_id == get_post_thumbnail_id(get_the_ID())) {
        //         $url = Hub2Word_image_url(get_the_ID());
        //         if ($url)
        //             $att_url = $url;
        //     }
        //     return $att_url;
        // }
        // add_filter('wp_get_attachment_url', 'Hub2Word_replace_attachment_url', 10, 2);

        function Hub2Word_replace_attachment_image_src($image, $att_id) {
            $h2w = get_post_meta($post->ID, 'Hub2Word', true);
            if ($h2w == 'Yes')
                return $image;

            if ($att_id == get_post_thumbnail_id(get_the_ID())) {
                $url = Hub2Word_image_url(get_the_ID());
                if ($url) {
                    return array(
                        $url,
                        0,
                        0,
                        false
                    );
                }
            }
            return $image;
        }
        add_filter('wp_get_attachment_image_src', 'Hub2Word_replace_attachment_image_src', 10, 2);

        /**
         * Parse post link and replace it with HubSpot Link value.
         *
         * @wp-hook post_link
         * @param   string $link
         * @param   object $post
         * @return  string
         */
        function Hub2Word_external_post_link( $link, $post )
        {
            $meta = get_post_meta( $post->ID, 'Hub2Word_external_link', TRUE );
            $url  = esc_url( filter_var( $meta, FILTER_VALIDATE_URL ) );

            return $url ? $url : $link;
        }

        add_filter( 'post_link', 'Hub2Word_external_post_link', 10, 2 );

        /**
         * Parse page link and replace it with HubSpot Link value.
         *
         * @wp-hook page_link
         * @param   string $link
         * @param   object $post
         * @return  string
         */
        function Hub2Word_external_page_link( $link, $post )
        {
            $meta = get_post_meta( $post->ID, 'Hub2Word_external_link', TRUE );
            $url  = esc_url( filter_var( $meta, FILTER_VALIDATE_URL ) );

            return $url ? $url : $link;
        }

        add_filter( 'page_link', 'Hub2Word_external_page_link', 10, 2 );

    } //endif H2W not run

}