<?php
/*
Plugin Name: BBMRI Plugin
Description: Handle URLs created by SFL to avoid issues using DomainFactory CDN, as well as provide JS for anchors.
Version: 1.1.94
Author: Leiner Stefan
*/

// Enqueue JavaScript file for the plugin

function inject_script() {
    wp_enqueue_script('url-replacer-script', plugins_url('/js/url-replace.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'inject_script');