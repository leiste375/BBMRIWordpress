Simple File List:
    SFL not playing nice with DF CDN. Working links injected via Plugin/JS.

301 Redirects handled via plugin.

News Sidebar, hardcoded via Theme:
    BBMRI Theme -> page.php, Ln42: 5
    Category set page-wise via GUI

CSS set via Dashboard: Appearance -> Customizer -> Additional CSS

Youtube Embeddings:
    Settings ACF:
        Field Group:            Youtube
        Fields:
            Field Type:         Text
            Field Name:         youtube_url
            Numerical fields for yt_width & yt_height. Restrictions set via plugin effectively don't work(?)
        Rules:                  Block equals Youtube

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

Login Button added to header.php
	<div class="loginbutton loginbutton_mobile">
		<!-- Login/Logout is added to Language button. -->
		<?php if ( is_user_logged_in() ) {
			echo '<a href="../wp-login.php?action=logout">Logout</a>';
		} else {
			echo '<a href="../login/">Login</a>';
		} ?>
    </div>
