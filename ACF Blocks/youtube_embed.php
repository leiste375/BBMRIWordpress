<?php
	#Parameters
    $id = 'youtube_embed_video' . $block['id'];
    if( !empty($block['anchor']) ) {
        $id = $block['anchor'];
    }
    $className = 'youtube_embed_video';
    if( !empty($block['className']) ) {
        $className .= ' ' . $block['className'];
    }
    if( !empty($block['align']) ) {
        $className .= ' align' . $block['align'];
    }
    ?>
	<!-- Integrate into divs and sidebar set by BBMRI Theme. -->
<div id="<?php echo esc_attr($id); ?>" class="content-row content-row-<?php echo esc_attr($className); ?>">
	<div class="contentPart-main contentPart-main-<?php echo esc_attr($className); ?> trans <?php if (get_field('sidebar', get_queried_object_id())): ?>sidebar_active<?php endif; ?>">
	<div class="ytembed-inner"> <p> <?php
		#Childproofing field input. Why is this necessary and not handled by a plugin dedicated to custom blocks.
	        $yt = get_field('youtube_url');
		if (!empty(get_field('yt_height'))) {
	        	$yt_height = get_field('yt_height');
	        } else {
	                $yt_height = "315";
	        } if (!empty(get_field('yt_width'))) {
	                $yt_width = get_field('yt_width');
	        } else {
	                $yt_width = "560";
	        }
		#If iframe code is detected, overwrite width and height.
	        if (preg_match("/iframe/i", $yt)) {
			$embedcode_temp = preg_replace_callback('/width="(\d+)"/', function($match) use (&$yt_width) {
				return 'width="' . $yt_width . '"';
			}, $yt);
			$embedcode_final = preg_replace_callback('/height="(\d+)"/', function($match) use (&$yt_height) {
				return 'height="' . $yt_height . '"';
			}, $embedcode_temp);
	                echo $embedcode_final;
		} else {
	                #Regex to match all possible combinations, extract Youtube ID and finally construct the correct embed code. 
	                #Source: https://stackoverflow.com/questions/2936467/parse-youtube-video-id-using-preg-match/6382259#6382259
			$pattern_url = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i';
	                preg_match($pattern_url, $yt, $yt_code);
	                echo $embedcode = "<iframe width=\"$yt_width\" height=\"$yt_height\" src=\"https://www.youtube.com/embed/$yt_code[1]\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>";
			} ?> 
	</p> </div>
	<div class="contentPart-sidebarDummy"></div>
	</div> 
</div>
