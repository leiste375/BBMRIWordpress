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
			<div class="contentPart-main contentPart-main-<?php echo esc_attr($className); ?> trans <?php if (get_field('sidebar', get_queried_object_id())): ?>sidebar_active<?php endif; 
		?>">
		<div class="ytembed-inner">
			<p>
			<?php
			#Read field input. 
            $yt = get_field('youtube_url');
			#Match youtube embed code. 
            if (preg_match("/iframe/i", $yt)) {
                echo $embedcode = $yt;
			} else {
                #Regex to match all possible combinations, extract Youtube ID and finally construct the correct embed code. 
                #Source: https://stackoverflow.com/questions/2936467/parse-youtube-video-id-using-preg-match/6382259#6382259
                $embedcode1 = '<iframe width="560" height="315" src="https://www.youtube.com/embed/';
                $embedcode2 = '" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $yt, $yt_code);
                echo $embedcode = $embedcode1 .= $yt_code[1] .= $embedcode2;
			} ?>
			</p>
		</div>
		<div class="contentPart-sidebarDummy"></div>
		</div>
	</div>