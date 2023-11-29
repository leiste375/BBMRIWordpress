Simple File List:\
	&emsp;SFL not playing nice with DF CDN. Working links injected via Plugin/JS.
	&emsp;File Types restricted. See upload field, or plugin config for a complete list. Individual uploads restricted to 10 files & 50MB. 
 	&emsp;User Role "below" Editor redirected to Intranet Page after login via plugin. Non-authenticated users are redirected to login page when accessing intranet page. Fileshare Table only shown to logged in users, when redirect is circumvented.
	&emsp;News Archive publicly available. 
 
301 Redirects handled via plugin.

News Sidebar, hardcoded via Theme:\
	&emsp;BBMRI Theme -> page.php, Ln42: 5\
 	&emsp;Category set page-wise via GUI

CSS set via Dashboard: Appearance -> Customizer -> Additional CSS

Youtube Embeddings:\
	&emsp;Settings ACF:\
		&emsp;&emsp;Field Group: Youtube\
	        &emsp;&emsp;Field Type: Text\
	        &emsp;&emsp;Field Name: youtube_url\
		&emsp;&emsp;Numerical fields for yt_width & yt_height. Restrictions set via plugin effectively don't work(?)\
		&emsp;&emsp;Rules: Block equals Youtube\
```
    Custom ACF Block defined in wp-contents/bbmri_theme/functions.php:
        add_action('acf/init', 'youtube_embed');
            function youtube_embed() {
                if( function_exists('acf_register_block_type') ) {
                    acf_register_block_type(array(
                        'name'					=>	'youtube_embed',
                        'title'					=>	_('Youtube'),
                        'description'			=>	_('Embed Youtube Videos'),
                        'supports'				=>	array('editor'),
                        'mode'					=>	'auto',
                        'render_template' 		=>	'custom-blocks/youtube_embed.php',
                        'category'				=>	'formatting',
                        'icon'					=>	'video-alt3',
                        'keywords'				=>	array('youtube'),
                        'example'				=>	array(
                            'attributes'			=>	array(
                                'mode'					=>	'preview',
                                'data'					=>	array(
                                    'preview_image_help'	=>	'../wp_content/themes/bbmri_theme/custom-blocks/previmg/videos_display.jpg',
                                )
                            )
                        )
                    ));
                }
            }
```
Login Button added to header.php (same code inserted twice, for desktop & mobile respectively)
```
	<div class="loginbutton loginbutton_mobile">
		<!-- Login/Logout is added to Language button. -->
		<?php if ( is_user_logged_in() ) {
			echo '<a href="/wp-login.php?action=logout">Logout</a>';
		} else {
			echo '<a href="/login/">Login</a>';
		} ?>
    </div>
```
