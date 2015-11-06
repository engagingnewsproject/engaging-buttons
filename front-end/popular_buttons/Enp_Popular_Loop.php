<?
class Enp_Popular_Loop extends Enp_Popular_Buttons {

    public function popular_loop($posts_per_page = 5) {
        if($this->have_popular()) {

            $enp_popular_html = '';

            do_action( 'enp_popular_loop_before', $this );

            // run comments/posts html loop
            $enp_popular_html = $this->process_popular_html($posts_per_page);

            do_action( 'enp_popular_loop_after', $this );

            return apply_filters('enp_popular_loop_wrap', $enp_popular_html, $this);
        }
    }

    /*
    *   Processes both post and comment html. ALL HTML is generated through added filters.
    *   $label = 'posts' or 'comments'
    *   $singular_label = 'post' or 'comment'
    *   So, the filter 'enp_popular_'.$label.'_loop_before_html' would be either
    *   'enp_popular_posts_loop_before_html' or 'enp_popular_comments_loop_before_html'
    */
    public function process_popular_html($posts_per_page = 5) {
        $enp_popular_html = '';
        $singular_label = $this->get_singular_label();
        $label = $this->label;

        do_action( 'enp_popular_'.$label.'_loop_before', $this );
        $enp_popular_html = apply_filters( 'enp_popular_'.$label.'_loop_before_html', $enp_popular_html, $this );

        $i = 0;
        foreach($this->{'popular_'.$this->label} as $pop) {
            $enp_popular_item_html = '';
            $pop_id = $pop[$singular_label.'_id'];
            $pop_count = $pop['btn_count'];

            do_action( 'enp_popular_'.$singular_label.'_before', $pop_id, $pop_count );

            $enp_popular_html .= apply_filters('enp_popular_'.$singular_label.'_html', $enp_popular_item_html, $pop_id, $pop_count, $this );

            do_action( 'enp_popular_'.$singular_label.'_after' ,$pop_id, $pop_count );

            // we only want 5 as a default, with the option to increase $posts_per_page later
            $i++;
            if($posts_per_page <= $i) {
                break;
            }
        }

        do_action( 'enp_popular_'.$label.'_loop_after', $this );
        $enp_popular_html = apply_filters( 'enp_popular_'.$label.'_loop_after_html', $enp_popular_html, $this );

        return apply_filters('enp_popular_'.$label.'_loop_wrap', $enp_popular_html, $this);
    }

    public function have_popular() {
        if($this->{'popular_'.$this->label} === false || empty($this->{'popular_'.$this->label}) ) {
            return false;
        } else {
            return true;
        }
    }

}
?>
