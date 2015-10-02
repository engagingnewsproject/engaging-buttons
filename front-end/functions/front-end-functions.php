<?
/*
*   Front End Display Functions
*   Add functionality to the front-end of the website
*
*   since v0.0.1
*/

function enp_button_append_button( $content ) {

    $enp_btns = new Enp_Button();
    $enp_btns = $enp_btns->get_btns();

    if(!empty($enp_btns)) {
        $enp_btn_HTML = '<ul class="enp-btns">';

        foreach($enp_btns as $enp_btn) {
            $enp_btn_HTML .= '<li class="enp-btn enp-btn-'.$enp_btn->get_btn_slug().'">
                                <span class="enp-btn-name enp-btn-'.$enp_btn->get_btn_slug().'-name">'
                                    .$enp_btn->get_btn_name().
                                '</span> '
                                .( $enp_btn->get_btn_count() > 0 ? '<span class="enp-btn-count enp-btn-'.$enp_btn->get_btn_count().'-count">'
                                                                     .$enp_btn->get_btn_count().
                                                                  '</span>'
                                                                  : '').
                                '</li>';
        }

        $enp_btn_HTML .= '</ul>';

        $content .= $enp_btn_HTML;
    }

    return $content;
}
add_filter( 'the_content', 'enp_button_append_button' );


?>
