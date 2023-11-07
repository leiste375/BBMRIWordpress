<?php
/*
Plugin Name: SFL URL Replace
Description: Handle URLs created by SFL to avoid issues using DomainFactory CDN. URLs are hardcoded. 
Version: 1.0
Author: Leiner Stefan
*/

// Enqueue JavaScript file for the plugin
function inject_script() {
    // Enqueue the JavaScript file for the plugin
    wp_enqueue_script('url-replacer-script', plugins_url('/js/url-replace.js', __FILE__), array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'inject_script');