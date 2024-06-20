<?php
	#Parameters
    $id = 'pic_gallery' . $block['id'];
    if( !empty($block['anchor']) ) {
        $id = esc_attr($block['anchor']);
    }
    $className = 'pic_gallery';
    if( !empty($block['className']) ) {
        $className .= ' ' . $block['className'];
    }
    if( !empty($block['align']) ) {
        $className .= ' align' . $block['align'];
    }

	//Get array of image. Size is for preview gallery. Can be one of the built-in sizes ('thumbnail', 'medium', 'large', 'full' or a custom size)
	$galleryPreview = '';
	$galleryModal = '';
	$img_int = 0;
	$images = get_field( 'picture_gallery' );
	$img_count = count($images);
	$size = 'thumbnail';
	if ( $images ) :
		foreach ( $images as $image ) :
			$img_int += 1;
			$img_id = $image['ID'];
			$img_src = $image['url'];
			$img_caption = $image['caption'];
			$img_alt = $image['alt'];
			$img_title = $image['title'];
			$img_descr = $image['description'];

		//Construct gallery content each array entry, to be inserted further down.
		$galleryPreview .= "<div id = 'gallery-thumb-<$img_int' class = 'pic-gallery-thumb'>\n";
		$galleryPreview .= wp_get_attachment_image( $img_id, $size,"", ["class" => "gallery_img_thumbnail", "onclick" => "openGalleryModal();gallerySlide($img_int)"]);
		$galleryPreview .= "</div>\n";

		$galleryModal .= "<div id = 'gallery-modal-inner-$img_id' class = 'pic-gallery-modal-inner pic-gallery-modal-slide-$id'>\n";
		$galleryModal .= "<div id = 'gallery-modal-header-$img_id' class = 'gallery-modal-header gallery-modal-header-$id'>\n";
			$galleryModal .= "<span class = 'gallery-modal-num'>$img_int/$img_count</span>\n";
			$galleryModal .= "<button class = 'close cursor' onclick = 'closeGalleryModal()'><span class='dashicons dashicons-no-alt'></span></button>\n";
		$galleryModal .= "</div>\n";
		$galleryModal .= "<img src = '$img_src' loading='lazy'>\n";
		$galleryModal .= "<span class = 'galleryModalPrev' onclick = 'plusSlides(-1)'>&#10094;</span>\n<span class = 'galleryModalNext' onclick = 'plusSlides(1)'>&#10095;</span>";
		if ( !empty($img_title) ) {
			$galleryModal .= "<div class = 'gallery-modal-title'>\n<p id = 'gallery-modal-title-$img_int'>$img_title</p></div><br>\n";
		}
		if ( !empty($img_caption) ) {
			$galleryModal .= "<div class = 'gallery-modal-caption'>\n<p id = 'gallery-modal-caption-$img_int'>$img_caption</p></div><br>\n";
		}
		if ( !empty($img_alt) ) {
			$galleryModal .= "<div class = 'gallery-modal-alt'>\n<p id = 'gallery-modal-alt-$img_int'>$img_alt</p></div><br>\n";
		}
		if ( !empty($img_descr) ) {
			$galleryModal .= "<div class = 'gallery-modal-descr'>\n<p id = 'gallery-modal-descr-$img_int'>$img_descr</p></div><br>\n";
		}
		$galleryModal .="</div>\n";
		endforeach;
	endif;
?>
<!-- Integrate into divs and sidebar set by BBMRI Theme. -->
<div id="<?php echo $id; ?>" class="content-row content-row-<?php echo esc_attr($className); ?>">
	<div class="contentPart-main contentPart-main-<?php echo esc_attr($className); ?> trans <?php if (get_field('sidebar', get_queried_object_id())): ?>sidebar_active<?php endif;?>">
		<div id="gallery-inner-<?php echo $id ?>" class="pic-gallery-inner">
			<?php echo $galleryPreview; ?>
		</div>
		<div id = "gallery-modal-<?php echo $id ?>" class = "pic-gallery-modal" onclick = "closeGalleryModalBackground(event)">
			<!--<div class = "gallery-modal-inner">-->
				<?php echo $galleryModal; ?>
			<!--</div>-->
		</div>
	<!--Handle gallery modal page javascript-->
	<script>
		function openGalleryModal() {
			document.getElementById("gallery-modal-<?php echo $id  ?>").style.display = "inline-block";
		}
		function closeGalleryModal() {
			document.getElementById("gallery-modal-<?php echo $id ?>").style.display = "none";
		}
		function closeGalleryModalBackground(event) {
			var modalBody = document.getElementById('gallery-modal-<?php echo $id ?>')
			if (event.target === modalBody) {
				closeGalleryModal();
			}
		}
		var gallerySlideIndex = 1;
		showGalleryModal(gallerySlideIndex);
		
		function plusSlides(n) {
			showGalleryModal( gallerySlideIndex += n );
		}
		function gallerySlide(n) {
			showGalleryModal( gallerySlideIndex = n);
		}
		function showGalleryModal(n) {
			var i;
			var slides = document.getElementsByClassName("pic-gallery-modal-slide-<?php echo $id ?>");
			if ( n > slides.length ) { gallerySlideIndex = 1 }
			if ( n < 1 ) { gallerySlideIndex = slides.length }
			for ( i = 0; i < slides.length; i++ ) {
				slides[i].style.display = "none";
			}
			slides[gallerySlideIndex-1].style.display = "inline-block";

			//Adjust positioning of nav buttons.
			var img = slides[gallerySlideIndex - 1].querySelector('img');
			var prevButton = slides[gallerySlideIndex - 1].querySelector('.galleryModalPrev');
			var nextButton = slides[gallerySlideIndex - 1].querySelector('.galleryModalNext');

			if (img.complete) {
				positionNavButtons(img, prevButton, nextButton);
			} else {
				img.onload = function() {
					positionNavButtons(img, prevButton, nextButton);
				};
			}
		}
		function positionNavButtons(img, prevButton, nextButton) {
			const imgRect = img.getBoundingClientRect();
			const imgMidpoint = img.offsetTop + ( imgRect.height / 2 );
			prevButton.style.top = `${imgMidpoint - ( prevButton.clientHeight / 2 )}px`;
			nextButton.style.top = `${imgMidpoint - ( nextButton.clientHeight / 2 )}px`;
			console.log(nextButton.style.top);
		}
	</script>
	</div>
	<div class="contentPart-sidebarDummy"></div>
	</div>
</div>
