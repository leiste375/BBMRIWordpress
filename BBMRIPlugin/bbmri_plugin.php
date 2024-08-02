<?php
/*
Plugin Name: BBMRI Plugin
Description: Handle URLs created by SFL to avoid issues using DomainFactory CDN. 301 Redirects are also handled here, as well as Login Page mods.
Version: 1.8.3
Author: MUG Internal
*/

/* If Page is Intranet (intranet-2 refers to german page), then check if user is authenticated and inject links. 
enqueue_script is necessary to execute JS after page has loaded. Page check inside function is necessary as slug is not read outside.*/
function intranet_stuff() {
    if ( is_page('intranet') || is_page('intranet-2') ) {
        if (!is_user_logged_in()) {
            auth_redirect();
        } else {
            wp_enqueue_script('url-replacer-script', plugins_url('/js/url-replace.js', __FILE__), array('jquery'), '1.8.2', true);
        }
    }
}
add_action('template_redirect', 'intranet_stuff');

add_filter( 'login_redirect', function( $redirect_to, $requested_redirect_to, $user ) {
    if ( ! $requested_redirect_to ) {
        $referrer = wp_get_referer();

        // Make sure the referring page is not a variation of the wp-login page or was the admin (aka user is logging out).
        if ( $referrer && ! str_contains( $referrer, 'wp-login' ) && ! str_contains( $referrer, 'wp-admin' ) ) {
            $redirect_to = $referrer;
        }
    }
    return $redirect_to;
}, 10, 3 );

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
        .wpml-login-ls {
            display: flex;
            justify-content: center;
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
	'/documents(.*)' => '/home/intranet',
	'/webdav(.*)' => '/home/intranet',
	'/news/-/asset_publisher/(.*)' => '/news-events',
	'/web/guest/archive' => '/news-events/news-archive',
	'/bbmri.at/news/' => '/news-events/',
	'/bbmri.at/web/guest/' => '/',
	'/web/guest/catalog' => '/for-researchers/',
	'/web/guest/biobank-cohorts' => '/for-researchers/biobank-cohorts/',
	'/web/guest/quality-management/' => '/for-researchers/quality-management/',
	'calculator1' => '/for-researchers/cost-calculator/',
	'/web/guest/services/' => '/for-researchers/services/',
	'/web/guest/biomaterial-mta' => '/for-researchers/biomaterial-mta-ic/',
	'/about-bbmri.at1' => '/home/about/',
	'/web/guest/key-activites/' => '/home/goals-key-activities/',
	'/web/guest/partners/' => '/home/partners/',
	'/web/guest/governance/' => '/home/governance/',
	'/bbmri.at-covid-19' => '/home/covid-19/',
	'/web/guest/links/' => '/home/links/',
	'/for-patients' => '/for-citizens/',
	'/biobanken' => '/for-citizens/about-bbmri-at/',
	'/de/biobanken' => '/de/fuer-buerger/ueber-biobanken/',
	'/bbmri.at/about-biobanks/' => '/for-citizens/about-biobanks/',
	'/web/guest/citizen-expert-panel' => '/for-citizens/citizen-expert-panel/',
	'/web/guest/faq' => '/for-citizens/faq/',
	'/web/guest//login' => '/home/intranet/',
	'/web/guest/login' => '/home/intranet/',
	'/web/guest/publications' => '/news/publications/',
	'/web/guest/projects' => '/news/projects/',
	'/web/guest/videos' => '/news/videos/',
	'/web/guest/courses' => '/news/courses/',
	'/web/guest/jobs' => '/news/jobs/',
	'/web/guest/contact' => '/contact/',
	'/de/projects(.*)' => '/de/news-2/projekte/',
	'/projects(.*)' => '/news-events/projects/',
	'/login(.*)' => '/wp-login.php',
	'/fr/group(.*)' => '/wp-login.php',
	'/de/events(.*)' => '/de/news-2',
	'/events(.*)' => '/news-events/',
	'/de/cohorts(.*)' => '/de/fuer-forscherinnen/biobank-cohorts/',
	'/cohorts(.*)' => '/for-researchers/biobank-cohorts/',
	);

	//Read requested URL
	$requested_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	foreach ( $redirects as $pattern => $target ) {
		if ( $requested_url === home_url($target)) {
			continue;
        }
        if ( preg_match('~' . $pattern . '~', $requested_url) ) {
            //Redirect cohort permalinks to anchor on cohorts page.
            if ( strpos( $pattern, '/de/cohorts' ) === 0 || strpos( $pattern, '/cohorts' ) === 0) {
                $post_title = trim($matches[1], '/');
                $post_id = url_to_postid($requested_url);
                if ( $post_id ) {
                    $target = $target . '#' . $post_id;
                }
            }
			wp_redirect(home_url($target), 301);
			exit; 
		}
    }
}
add_action('template_redirect', 'bbmri_redirect');

//Redirect users if not logged in before File Access Manager executes. Action needs to be hooked directly into wp_loaded.
function sfla_auth_check() {
    if (strpos($_SERVER['REQUEST_URI'], '/ee-get-file/') !== false) {
        $user = wp_get_current_user();
        if ( !empty($user->ID) ) {
        } else {
            auth_redirect();
            exit;
        }
    }
}
?>
