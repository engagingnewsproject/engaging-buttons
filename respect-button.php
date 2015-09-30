<?php
   /*
   Plugin Name: Respect Button
   Description: A plugin for giving respect to posts, pages, and comments.
   Version: 0.0.1
   Author: Engaging News Project
   Author URI: http://engagingnewsproject.org
   License: ASK US
   */

// Disallows this file to be accessed via a web browser
if ( ! defined( 'WPINC' ) ) {
    die;
}

//Automatically Load all the PHP files we need
$classesDir = array (
    plugin_dir_path( __FILE__ ) .'admin/functions/',
    plugin_dir_path( __FILE__ ) .'admin/options/',
);

foreach ($classesDir as $directory) {
    foreach (glob($directory."*.php") as $filename){
        include $filename;
    }
}

?>
