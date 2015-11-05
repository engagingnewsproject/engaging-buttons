<?
class Enp_Popular_Loop extends Enp_Popular_Buttons {

    public function popular_loop() {
        if($this->have_popular()) {

            $enp_popular_html = '';
            $this->singular_label = $this->get_singular_label();

            do_action( 'enp_popular_loop_before', $this );

            // run comments or posts loop
            $enp_popular_html = $this->{'popular_'.$this->label.'_loop'}();

            do_action( 'enp_popular_loop_after', $this );

            return $enp_popular_html;
        }

    }

    public function popular_comments_loop() {
        $enp_popular_html = '';

        do_action( 'enp_popular_comments_loop_before', $this );

        $enp_popular_html = apply_filters( 'enp_popular_comments_loop_before_html', $enp_popular_html, $this );

        foreach($this->{'popular_'.$this->label} as $pop) {

            $pop_id = $pop[$this->singular_label.'_id'];
            $pop_count = $pop['btn_count'];

            do_action( 'enp_popular_comment_before', $enp_popular_html, $pop_id, $pop_count );

            $enp_popular_html .= apply_filters('enp_popular_comment_html', get_comment_text( $pop_id ), $pop_id, $pop_count);

            do_action( 'enp_popular_comment_after', $enp_popular_html, $pop_id, $pop_count );

            //$enp_popular_html .= '<a href="'.get_permalink($pop_id).'">'.get_the_title($pop_id).'</a> has '.$pop_count.' clicks';
        }

        do_action( 'enp_popular_comments_loop_after', $enp_popular_html, $this );
        $enp_popular_html = apply_filters( 'enp_popular_comments_loop_after_html', $enp_popular_html, $this );

        return apply_filters('enp_popular_comments_loop_wrap', $enp_popular_html, $this);
    }

    public function popular_posts_loop() {
        $enp_popular_html = '<ul>';

        do_action( 'enp_popular_posts_loop_before', $this );
        $enp_popular_html = apply_filters( 'enp_popular_posts_loop_before_html', $enp_popular_html, $this );


        foreach($this->{'popular_'.$this->label} as $pop) {

            $pop_id = $pop[$this->singular_label.'_id'];
            $pop_count = $pop['btn_count'];
            $enp_popular_post_html = '';
            do_action( 'enp_popular_post_before', $pop_id, $pop_count );

            $enp_popular_post_html .= '<li><a href="'.get_permalink($pop_id).'">'.get_the_title($pop_id).' ('.$pop_count.')</a></li>';
            $enp_popular_html .= apply_filters('enp_popular_post_html', $enp_popular_post_html, $pop_id, $pop_count);

            do_action( 'enp_popular_post_after' ,$pop_id, $pop_count );

        }

        $enp_popular_html .= '</ul>';
        do_action( 'enp_popular_posts_loop_after', $this );
        $enp_popular_html = apply_filters( 'enp_popular_comments_loop_after_html', $enp_popular_html, $this );

        return apply_filters('enp_popular_comments_loop_wrap', $enp_popular_html, $this);
    }

    public function have_popular() {
        if($this->{'popular_'.$this->label} === false) {
            return false;
        } else {
            return true;
        }
    }

    public function get_pop_by_id() {
        return $this->{'popular_'.$this->label.'_by_id'};
    }

}


// with WP_Query
            /*
            $query_args = array(''
                                'post__in' => $this->get_pop_by_id(), // returns array of popular post ids
                            );
            $pop_posts_query = new WP_Query( $query_args );
            $pop_html = '';
            // The Loop
            if ( $pop_posts_query->have_posts() ) :
                $pop_html = '<ul>';
                while ( $pop_posts_query->have_posts() ) : $pop_posts_query->the_post();

                    $pop_html .= '<li><a href="'.get_permalink().'">' . get_the_title() . '</a></li>';

                endwhile;
                $pop_html .= '<ul>';
            endif;

            wp_reset_postdata();
            return $pop_html;*/
?>
