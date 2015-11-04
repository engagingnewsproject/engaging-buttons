<?
/*
* Enp_Button_Popular Class
* Get post IDs by most clicked button slugs
*
* since v 0.0.3
*/

/*
USAGE: Used to get popular posts so you can loop through them
      to use however you want

$args = array(
              'btn_slug'=>'Respect',
              'btn_type'=>'comment'
            );

$pop_posts = new Enp_Popular_Buttons($args); // object{
                                                    'btn_slug' => 'respect';
                                                    'btn_name' => 'Respect';
                                                    'btn_past_tense_name' => 'Respected';
                                                    'btn_type' => 'comment';
                                                    'popular_posts' =array(                    // this array is useful for basic foreach loops
                                                                        array('post_id'=>1,
                                                                              'btn_count'=>89
                                                                              ),
                                                                        array('post_id'=>19,
                                                                              'btn_count'=>59
                                                                              ),
                                                                        array('post_id'=>6,
                                                                              'btn_count'=>16
                                                                              )
                                                                        );
                                                    'popular_posts_by_id' = array([1]=>1, // this array is useful for wp_query loops
                                                                                  [2]=>19,
                                                                                  [3]=>6)
                                                    }

echo '<h2>Most '.$pop_posts->get_btn_past_tense_name().' '.$pop_posts->get_btn_past_tense_name().'</h2>';
foreach($pop_posts->popular_posts as $pop) {
    $post_id = $pop['post_id'];
    $btn_clicks = $pop['btn_count'];
    echo '<h3><a href="'.get_permalink($post_id).'">'.get_the_title($post_id).'</a></h3>';
}

// with WP_Query
$query_args = array(
                    'post_type' => $pop_posts->get_btn_types();
                    'post__in' => $pop_posts->get_pop_posts_by_id(); // returns array of popular posts
$pop_posts_query = new WP_Query( $args );

// The Loop
if ( $pop_posts_query->have_posts() ) :
    while ( $pop_posts_query->have_posts() ) : $pop_posts_query->the_post();

        echo '<li>' . get_the_title() . '</li>';

    endwhile;
endif;

wp_reset_postdata();
*/

class Enp_Popular_Buttons {

    public function __construct($args = array()) {
         $default_args = array(
            'btn_slug' => false, // set to slug string or array of strings, "respect", "recommend", "important". also accepts array
            'btn_type' => 'all_post_types', // slug of the post type. post, page, comment, or cpt slug
            'comments' => false // flag to get comments of a post type
        );
        // merge the default args
        $args = array_merge($default_args, $args);

        // set our label ('comments' or 'posts')
        $this->label = $this->get_label_type($args);

        if($args['btn_slug'] === false) {
            // If btn_slug == false, get active button slugs, pass to the construct,
            // and return array of all popular post objects by slug
            $this->get_all_popular_buttons($args);
        } else {
            $this->set_popular_button($args);
        }

        // remove the label from the object, since we don't need to pass it in public
        unset($this->label);

    }

    /*
    *   Main setter function. Pass it the filled in args
    *   and this will hook it all up
    */
    protected function set_popular_button($args) {
        $this->btn_slug = $args['btn_slug'];
        $this->btn_type = $args['btn_type'];
        $this->{'popular_'.$this->label} = $this->set_popular_posts($args);
        $this->{'popular_'.$this->label.'_by_id'} = $this->set_popular_posts_by_id($args);
    }

    /*
    *   Grab the array straight from the database.
    *   All the hard work has already been done
    */
    protected function set_popular_posts($args) {
        try{

            if($this->btn_slug === false) {
                throw new Exception('Enp_Popular_Buttons: No btn_slug set.');
            }

            $pop_posts = false;

            // figure out which option value we need to grab
            // if all posts type and we want posts
            if($args['btn_type'] === 'all_post_types' && $this->label === 'posts') {
                $pop_posts = get_option( 'enp_button_popular_'.$args['btn_slug'] );
            }
            // we have btn_slug and individual button type that's not comments, so return that
            elseif($args['btn_type'] !== 'all_post_types' && $this->label === 'posts') {
                $pop_posts = get_option( 'enp_button_popular_'.$args['btn_slug'].'_'.$args['btn_type'] );
            }
            // if we have a specific btn_type and want comments
            elseif($args['btn_type'] !== 'all_post_types' && $this->label === 'comments') {
                $pop_posts = get_option( 'enp_button_popular_'.$args['btn_slug'].'_'.$args['btn_type'].'_comments' );
            }
            // if we want all comments regardless of post type
            elseif($args['btn_type'] === 'all_post_types' && $this->label === 'comments') {
                $pop_posts = get_option( 'enp_button_popular_'.$args['btn_slug'].'_comments' );
            } else {
                throw new Exception('Enp_Popular_Buttons: We could not find any values to return.');
            }

            return $pop_posts;

        } catch(Exception $e) {
            // return our exception
            echo $e->getMessage();
        }


    }

    /*
    *   Process the popular_posts array and just grab
    *   all the Ids into one array
    */
    protected function set_popular_posts_by_id($args) {
        $pop_posts_by_id = array();
        // check to make sure the popular_$label field has values
        if(!empty($this->{'popular_'.$this->label})) {
            // set the dynamic array key label
            if($this->label === 'comments') {
                $singular_label = 'comment';
            } else {
                $singular_label = 'post';
            }
            // loop through popular_$label and push all the IDs into an array
            foreach($this->{'popular_'.$this->label} as $pop_post) {
                $pop_posts_by_id[] = $pop_post[$singular_label.'_id'];
            }
        }
        // return the array
        return $pop_posts_by_id;
    }

    protected function get_label_type($args) {
        if($args['btn_type'] === 'comment' || $args['comments'] === true) {
            $label = 'comments';
        } else {
            $label = 'posts';
        }

        return $label;
    }
    /*
    *   If no slug was passed, we need to join all of them into
    *   an array of popular button objects
    *   USAGE: $pop_btns = new Enp_Popular_Buttons();
    *          var_dump($pop_btns->get_all_popular_buttons());
    */
    public function get_all_popular_buttons($args = array()) {
        $btn_slugs = get_option('enp_button_slugs');

        $pop_posts_objs = array();

        try {
            if(empty($btn_slugs)) {
                throw new Exception('Enp_Button: There are no button slugs saved in the database. Please go to Engaging Buttons settings and create and save a button.');
            }
        } catch(Exception $e) {
            // return our exception
            echo $e->getMessage();
            return false;
        }


        $i = 0;
        foreach($btn_slugs as $slug) {
            $args['btn_slug'] = $slug;
            $pop_posts_objs[] = new Enp_Popular_Buttons($args);
            $i++;
        }

        return $pop_posts_objs;
    }

}

?>
