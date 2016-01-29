<?php

/*
*   This is all just fallback code for if someone submits the form without javascript
*   It will create all the CSS to save to the database
*
*/
function enp_create_button_css($button_style = false) {
    $button_color = get_option('enp_button_color');
    $clicked_color = get_option('enp_button_color_clicked');
    $active_color = get_option('enp_button_color_active');
    $button_style = get_option('enp_button_style');

    if(empty($button_color) || $button_color === false) {
        // they don't want any custom CSS. Git on outta here.
        return false;
    }

    if(empty($clicked_color) || $clicked_color === false) {
        enp_hex_check_and_return_color($button_color, -0.14);
    }

    if(empty($active_color) || $active_color === false) {
        enp_hex_check_and_return_color($button_color, 0.15);
    }

    if($button_style === false || empty($button_style)) {
        $button_style = 'ghost';
    }

    if($button_style === 'ghost') {
        $css = '.enp-btns-wrap .enp-btn {
                    background: transparent;
                    color: '.$button_color.';
                }

                .enp-btns-wrap .enp-btn,
                .enp-btns-wrap .enp-btn:focus,
                .enp-btns-wrap .enp-btn:hover,
                .enp-btns-wrap .enp-btn--user-clicked {
                    border: 2px solid '.$button_color.';
                }

                .enp-btns-wrap .enp-btn:hover,
                .enp-btns-wrap .enp-btn--user-clicked {
                    background: '.$button_color.';
                    color: #ffffff;
                }

                .enp-btns-wrap.enp-icon-state .enp-icon {
                    fill: '.$button_color.';
                }

                .enp-btns-wrap .enp-btn:hover .enp-icon,
                .enp-btns-wrap .enp-btn--user-clicked .enp-icon {
                    fill: #ffffff;
                }
                ';
    } else {
        $css = '.enp-btns-wrap .enp-btn {
                    background: '.$button_color.';
                }
                .enp-btns-wrap .enp-btn:hover,
                .enp-btns-wrap .enp-btn--user-clicked {
                    background: '.$clicked_color.';
                }
                .enp-btns-wrap .enp-btn:active {
                    background: '.$active_color.';
                }
                ';
    }

    return $css;
}

/**
 * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.7
 * @param str $hex Colour as hexadecimal (with or without hash);
 * @percent float $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() )
 * @return str Lightened/Darkend colour as hexadecimal (with hash);
 */
 // NOTE: This doesn't seem to work very consistently...
function enp_color_luminance( $hex, $percent ) {
	// validate hex string
	$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
	$new_hex = '#';

	if ( strlen( $hex ) < 6 ) {
		$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
	}

	// convert to decimal and change luminosity
	for ($i = 0; $i < 3; $i++) {
		$dec = hexdec( substr( $hex, $i*2, 2 ) );
		$dec = min( max( 0, $dec + ($dec * $percent) ), 255 );
		$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
	}

	return $new_hex;
}

// validate that it's a valid HEX
function enp_validate_color($hex) {
    $valid_hex_check = false;
    // validate hex string
    $matches = null;
    $color = preg_match('/#([a-fA-F0-9]{3}){1,2}\\b/', $hex, $matches);

    if(!empty($matches)) {
        $valid_hex_check = true;
    }
    return $valid_hex_check;
}

/*
*   The $percent is used as a fallback if the field is empty
*    (it shouldn't be unless javascript is off)
*/
function enp_hex_check_and_return_color($value, $percent) {
    $hex = false;
    // validate the hex value
    if(enp_validate_color($value) === true) {
        $hex = $value;
    } else {
        // the value is invalid, so let's try to create a valid one
        // check to see if there's a main color or not
        $base_button_color = get_option('enp_button_color');
        // check to validate that color
        if(!empty($base_button_color) && enp_validate_color($base_button_color) === true) {
            // change the hex to darken/lighten
            $hex = enp_color_luminance( $base_button_color, $percent);
        }
    }
    return $hex;
}


?>
