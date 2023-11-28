<?php
/*
Plugin Name: BBMRI Plugin
Description: Handle URLs created by SFL to avoid issues using DomainFactory CDN. 301 Redirects are also handled here, as well as Login Page mods.
Version: 1.4.3
Author: Leiner Stefan
*/

// If Page is Intranet (intranet-2 refers to Page in German Menu), then check if user is authenticated and inject links
function intranet_stuff() {
    if ( is_page('intranet') || is_page('intranet-2') ) {
        if (!is_user_logged_in()) {
            auth_redirect();
        } else {
            echo "<script>console.log('INTRANET DETECTED');</script>";
            wp_enqueue_script('url-replacer-script', plugins_url('/js/url-replace.js', __FILE__), array('jquery'), '1.0', true);
        }
    }
}
add_action('template_redirect', 'intranet_stuff');

//Modify Login Page. Source: https://codex.wordpress.org/Customizing_the_Login_Form
function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/svg/icon_from_logo.svg);
		height:80px;
		width:80px;
		background-size: 80px 80px;
		background-repeat: no-repeat;
        	padding-bottom: 0px;
        }
    </style>
<?php }
add_action('login_enqueue_scripts', 'my_login_logo');
function my_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'my_login_logo_url');

function my_login_logo_url_title() {
    return 'BBMRI.at';
}
add_filter('login_headertext', 'my_login_logo_url_title');



//Define an array for all URLs. Tested but deactivated until launch.
function bbmri_redirect() {
    $redirects = array(
        '/documents(.*)' => 'https://old.bbmri.at/documents$1',
        '/webdav(.*)' => 'https://old.bbmri.at/webdav$1',
        '/news/-/asset_publisher/(.*)' => 'https://old.bbmri.at/news/-/asset_publisher/$1',
        '/archive' => 'https://old.bbmri.at/archive',
        '/news/' => 'https://bbmri.at/news-events/',
        '/catalog/' => 'https://bbmri.at/for-researchers/',
        '/biobank-cohorts' => 'https://bbmri.at/for-researchers/biobank-cohorts/',
        '/quality-management' => 'https://bbmri.at/for-researchers/quality-management/',
        '/calculator1' => 'https://bbmri.at/for-researchers/cost-calculator/',
        '/services' => 'https://bbmri.at/for-researchers/services/',
        '/biomaterial-mta' => 'https://bbmri.at/for-researchers/biomaterial-mta-ic/',
        '/about-bbmri.at1' => 'https://bbmri.at/home/about/',
        '/key-activites' => 'https://bbmri.at/home/goals-key-activities/',
        '/partners' => 'https://bbmri.at/home/partners/',
        '/governance' => 'https://bbmri.at/home/governance/',
        '/bbmri.at-covid-19' => 'https://bbmri.at/home/covid-19/',
        '/links' => 'https://bbmri.at/home/links/',
        '/for-patients' => 'https://bbmri.at/for-citizens/',
        '/biobanken' => 'https://bbmri.at/for-citizens/about-bbmri-at/',
        '/about-biobanks' => 'https://bbmri.at/for-citizens/about-biobanks/',
        '/citizen-expert-panel' => 'https://bbmri.at/for-citizens/citizen-expert-panel/',
        '/faq' => 'https://bbmri.at/for-citizens/faq/',
        '/web/guest//login' => 'https://bbmri.at/intranet/',
        '/web/guest/login' => 'https://bbmri.at/intranet/',
        '/publications' => 'https://bbmri.at/news/publications/',
        '/projects' => 'https://bbmri.at/news/projects/',
        '/videos' => 'https://bbmri.at/news/videos/',
        '/courses' => 'https://bbmri.at/news/courses/',
        '/jobs' => 'https://bbmri.at/news/jobs/',
        '/contact' => 'https://bbmri.at/contact/',
    );

    //Read requested URL
    $requested_url = $_SERVER['REQUEST_URI'];

    //Handle wildcards. 
    foreach ($redirects as $from => $to) {
        $pattern = str_replace('/', '\/', $from);
        $pattern = preg_replace('/\(\.\*\)/', '(.*)', $pattern);

        //Match URL, include captured value from wildcard and perform a 301 redirect. 
        if (preg_match('/^' . $pattern . '$/', $requested_url, $matches)) {
            $redirect_url = preg_replace('/\$1/', $matches[1], $to); 
            wp_redirect($redirect_url, 301);
            exit();
        }
    }
}
add_action('template_redirect', 'bbmri_redirect');

?>
