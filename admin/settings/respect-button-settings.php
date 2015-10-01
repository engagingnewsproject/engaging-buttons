<?php

//  Create link to the menu page.
add_action('admin_menu', 'enp_create_menu');
function enp_create_menu() {
    //create new top-level menu
    add_menu_page('Respect Button', 'Respect Button', 'manage_options', 'enp_respect_button', 'enp_respect_button_page', 'dashicons-megaphone', 100);
}


// Add filters for any fields that need extra work before saving

// We can't register dynamically named variables, so we're going to
// create everything under enp_buttons and parse from there
add_filter( 'pre_update_option_enp_buttons', 'set_enp_buttons_values', 10, 2 );
function set_enp_buttons_values($values) {

    // Save slugs enp_button_slugs = array('respect', 'important', 'recommend');
    update_enp_button_slugs($values);

    // set all the btn_type values to false
    // if they weren't submitted by being checked true by the user
    $values = set_unset_btn_type_values($values);

    // Add a btn_name in there too in there too
    $values = add_enp_button_names($values);

    // Save/Create enp_button_$slug
    update_enp_button_slug_entry($values);

    // Save the entire enp_buttons as is so we have everything in one place if we need it
    return $values;
}


// Create settings fields.
add_action( 'admin_init', 'enp_respect_button_data' );
function enp_respect_button_data() {

    // button type
    register_setting( 'enp_respect_button_settings', 'enp_buttons' );

    // global enp_button settings
    register_setting( 'enp_respect_button_settings', 'enp_button_must_be_logged_in' );
    register_setting( 'enp_respect_button_settings', 'enp_button_allow_data_tracking' );
    register_setting( 'enp_respect_button_settings', 'enp_button_promote_enp' );
}

function buttonCreateHTML($i, $enp_buttons, $enp_btn) {?>
        <table class="form-table">
            <tbody>
                <tr>
                        <th scope="row">
                            <label for="enp-button-type">Button</label>
                        </th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="radio" name="enp_buttons[<?php echo $i;?>][btn_slug]" aria-describedby="enp-button-slug-description" value="respect" <?php checked('respect', $enp_buttons[$i]["btn_slug"]);?> /> Respect
                                </label>
                                <br/>
                                <label>
                                    <input type="radio" name="enp_buttons[<?php echo $i;?>][btn_slug]" aria-describedby="enp-button-slug-description" value="recommend" <?php checked('recommend', $enp_buttons[$i]["btn_slug"]);?> /> Recommend
                                </label>
                                <br/>
                                <label>
                                    <input type="radio" name="enp_buttons[<?php echo $i;?>][btn_slug]" aria-describedby="enp-button-slug-description" value="important" <?php checked('important', $enp_buttons[$i]["btn_slug"]);?>/> Important
                                </label>

                                <p id="enp-button-slug-description"class="description">Which button do you want to use on your site?</p>

                                <p class="description">Have an idea for other button text options? Let us know! ____@engagingnewsproject.org
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enp-button-content-type">How to Use this Button</label>
                        </th>
                        <td>
                            <fieldset>
                                <?  $registered_content_types = registeredContentTypes();
                                    $checklist_html = '';

                                    foreach($registered_content_types as $content_type) {
                                        $checklist_html .= '<label>
                                                                <input type="checkbox" name="enp_buttons['.$i.'][btn_type]['.$content_type['slug'].']" value="1" '.checked(true, $enp_buttons[$i]['btn_type'][$content_type['slug']], false).' aria-describedby="enp-button-content-type-description"/> '.$content_type['label_name'].'
                                                            </label>
                                                            <br/>';
                                    }

                                    echo $checklist_html;?>
                            </fieldset>
                            <p id="enp-button-content-type-description" class="description">Which kinds of content do you want the button(s) to display for?</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
<? }

/**
* Step 3: Create the markup for the options page
*/
function enp_respect_button_page() { ?>

<div class="wrap enp-respect-button-options">

    <form method="post" action="options.php">
        <h1>Respect Button Settings</h1>

        <?php if(isset( $_GET['settings-updated'])) { ?>
        <div class="updated">
            <p>Settings updated successfully</p>
        </div>
        <?php }


        $enp_btns = new Enp_Button();
        $enp_btns = $enp_btns->get_btns();
        echo '<strong>Enp_Buttons object</strong><br/>';
        var_dump($enp_btns);
        echo '<br/><br/>';
        foreach($enp_btns as $enp_btn) {
            echo '<h1>'.$enp_btn->get_btn_name().'</h1>';
        }

        $enp_btn = new Enp_Button('respect');
        // RIGHT NOW, this is an array. We want it to be an object.
        echo '<br/><strong>Enp_Button object</strong><br/>';
        var_dump($enp_btn);


        echo '<br/><strong>Enp_Button Name</strong><br/>';
        var_dump($enp_btn->get_btn_name());


        $enp_btn = new Enp_Button('recommend');
        // RIGHT NOW, this is an array. We want it to be an object.
        echo '<br/><strong>Enp_Button object</strong><br/>';
        var_dump($enp_btn);


        echo '<br/><strong>Enp_Button Slug</strong><br/>';
        var_dump($enp_btn->get_btn_slug());



        echo '<br/><strong>Registered Content Types</strong><br/>';
        var_dump(registeredContentTypes());


        $btn_must_be_logged_in = get_option('enp_button_must_be_logged_in');
        $btn_allow_data_tracking = get_option('enp_button_allow_data_tracking');
        $btn_promote_enp = get_option('enp_button_promote_enp');

        // return all buttons and build off of current options
        $enp_buttons = get_option('enp_buttons');

        $i = 0;
        foreach($enp_buttons as $enp_button) {
            buttonCreateHTML($i, $enp_buttons, $enp_btn);
            $i++;
        }
        // if $i still equals 0, then we don't have ANY buttons, and we still need to
        // create one
        if($i === 0) {
            buttonCreateHTML($i, $enp_buttons, $enp_btn);
        }
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        Respect Button Plugin Setting
                    </th>
                    <td>
                        <fieldset>
                            <label for="enp_button_must_be_logged-in">
                                <input type="checkbox" name="enp_button_must_be_logged_in" <?php checked(true, $btn_must_be_logged_in);?> value="1" /> Users must be logged in to click the button(s)
                            </label>
                            <label for="enp_button_allow_data_tracking">
                                <input type="checkbox" name="enp_button_allow_data_tracking" aria-describedby="enp-button-allow-data-tracking-description" <?php checked(true, $btn_allow_data_tracking);?> value="1" /> Allow data collection
                                <p id="enp-button-allow-data-tracking-description" class="description">This allows the <a href="http://engagingnewsproject.org">Engaging News Project</a>, an academic nonprofit at the University of Texas at Austin, to record data on the buttons so they can continue to provide free, open-source plugins and research. No personal information is recorded. Learn more about what data is tracked and how it is used here.</p>
                            </label>
                            <label for="enp_button_promote_enp">
                                <input type="checkbox" name="enp_button_promote_enp" aria-describedby="enp-button-promote-enp-description" <?php checked(true, $btn_promote_enp);?> value="1" /> Display "Respect Button Powered by the Engaging News Project"
                                <p id="enp-button-promote-enp-description" class="description">Small text displayed beneath the WordPress comments section.</p>
                            </label>
                        </fieldset>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php settings_fields( 'enp_respect_button_settings' ); ?>
        <?php do_settings_sections( 'enp_respect_button_settings' ); ?>
        <?php submit_button(); ?>
    </form>

    <p>The Respect Button plugin is made by the <a href="http://engagingnewsproject.org">Engaging News Project</a>, a nonprofit at the University of Texas at Austin that researches COMMERCIALLY-VIABLE and DEMOCRATICALLY-BENEFICIAL ways to improve ONLINE NEWS</p>
</div>
<?php
}
