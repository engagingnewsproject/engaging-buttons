<?
/*
*   Front End Display Functions
*   Add functionality to the front-end of the website
*
*   since v0.0.1
*/





/*
*
*   Get all buttons. Doesn't really save much time, but
*   could be useful for adding a filter or hook later
*
*/

function enp_get_all_btns() {
    $enp_btns = new Enp_Button();
    $enp_btns = $enp_btns->get_btns();

    return $enp_btns;
}

/*
*
*   Get a button by slug. Doesn't really save any time, but
*   could be useful for adding a filter or hook later
*
*/
function enp_get_btn($slug) {
    $enp_btns = new Enp_Button($slug);

    return $enp_btns;
}


/*
*
*   Get all Buttons, and append them to appropriate content
*
*/
function enp_btn_append_btns( $content ) {
    // don't specify a button slug so we can get all buttons
    $enp_btns = enp_get_all_btns();

    if(!empty($enp_btns[0])) {
        $enp_btn_HTML = '<ul class="enp-btns">';

        foreach($enp_btns as $enp_btn) {
            $enp_btn_HTML .= enp_btn_append_btn_HTML($enp_btn);
        }

        $enp_btn_HTML .= '</ul>';

        $content .= $enp_btn_HTML;
    }

    return $content;
}
add_filter( 'the_content', 'enp_btn_append_btns' );



/*
*
*   ENP Btn HTML for displaying on front-end
*
*/
function enp_btn_append_btn_HTML($enp_btn) {
    $enp_btn_HTML = '<li id="'.$enp_btn->get_btn_slug().'-wrap" class="enp-btn-wrap enp-btn-wrap--'.$enp_btn->get_btn_slug().'">
                                <a href="#" id="'.$enp_btn->get_btn_slug().'" class="enp-btn enp-btn--'.$enp_btn->get_btn_slug().'">
                                    <span class="enp-btn__name enp-btn__name--'.$enp_btn->get_btn_slug().'">'
                                        .$enp_btn->get_btn_name().
                                    '</span> '
                                    .( $enp_btn->get_btn_count() > 0 ? '<span class="enp-btn__count enp-btn__count--'.$enp_btn->get_btn_count().'">'
                                                                         .$enp_btn->get_btn_count().
                                                                      '</span>'
                                                                      : '').
                                '</a>
                            </li>';

    return $enp_btn_HTML;
}

?>
