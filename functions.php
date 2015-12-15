<?php

/**
 * Create Multiple Gallery On Post shortcode text from given mgop_slug and mgop_markup_type.
 *
 * @since 0.2
 *
 * @param string $mgop_slug Required. Metabox slug which would like to create shortcode to.
 * @param string $mgop_markup_type Optional. The markup type that HTML generated as. The value must be 'div', 'ol', or 'ul'.
 * @return string|empty string on no value.
 */
function mgop_create_shortcode( $mgop_slug, $mgop_markup_type = 'ul' ){

	if(! $mgop_markup_type){
		$mgop_markup_type = 'ul';
	}
	
	if($mgop_slug){
		return '[mgop_gallery slug="'. $mgop_slug .'" markup="'. $mgop_markup_type .'"/]';
	}
	
}


/**
 * Retrieve Multiple Gallery on Post metabox value of given metabox slug and post ID.
 *
 * @since 0.1
 *
 * @param string $mgop_slug Optional. Metabox slug would like to retrieved.
 * @param string $post_id Optional. Post ID would like to retrieved.
 * @return array|empty array on no value.
 */
function mgop_get_metadata( $mgop_slug = '', $post_id = ''){
	
	if( ! $post_id ){
		global $post;
		$post_id = $post->ID;
	}	
	$metavalue = get_post_meta( $post_id, 'mgop_media_value', true );	
	if( $mgop_slug ){	
		return isset( $metavalue['mgop_mb_' . $mgop_slug] ) ? $metavalue['mgop_mb_' . $mgop_slug] : array();	
	}else{		
		return ( is_array( $metavalue ) ) ? $metavalue : array();		
	}
	
}

/**
 * Create html elements of given metavalue
 *
 * @since 0.1
 *
 * @param string $mgop_slug Required.
 * @param array $mgop_title Optional.
 * @param array $post_id Optional.
 * @param array $metavalue Optional.
 * @param array $mgop_markup_type Optional.
 * @return string|empty string on no value.
 */
function mgop_gallery_theme( $mgop_slug, $mgop_title, $post_id = '', $metavalue = '', $mgop_markup_type = 'ul' ){	
	
	if( !$mgop_title ){
		
		global $post;
		
		$options = get_option('mgop_options');
		if( isset($options[$post->post_type]) && $options[$post->post_type]['active'] ){
			if( count($options[$post->post_type]['titles']) && isset($options[$post->post_type]['titles'][$mgop_slug]) && is_array($options[$post->post_type]['titles'][$mgop_slug]) ){
				$mgop_title = $options[$post->post_type]['titles'][$mgop_slug]['title'];
			}
		}
	}	
	
	if( !$metavalue ){
		$metavalue = mgop_get_metadata($mgop_slug, $post_id);
	}
	
	if(! $mgop_markup_type){
		$mgop_markup_type = 'ul';
	}

	if(is_array($metavalue) && count($metavalue)){	

		if($mgop_markup_type == 'ul' || $mgop_markup_type == 'ol'){
			$template_list = '
							<li class="mgop-elements-item">
								<a href="@mgop_image_full_url" title="@mgop_image_caption" class="mgop-elements-item-link">
									<img src="@mgop_image_thumb_url" width="@mgop_image_thumb_width" height="@mgop_image_thumb_height" alt="@mgop_image_alt" />
									<span class="mgop-elements-item-description">@mgop_image_description</span>
								</a>
							</li>
			';
		}elseif($mgop_markup_type == 'div'){
			$template_list = '
								<a href="@mgop_image_full_url" title="@mgop_image_caption" class="mgop-elements-item-link">
									<img src="@mgop_image_full_url" height="200" alt="@mgop_image_alt" />
									<span class="mgop-elements-item-description">@mgop_image_description</span>
								</a>
			';
		}
		
		if($mgop_markup_type == 'ul'){
			$template_wrap = '
				<div class="mgop-element mgop_@mgop_slug">
					<div class="mgop-element-content">
						<h3>@mgop_title</h3>
						<ul class="gallery">
							@mgop_list
						</ul>
					</div>
				</div>
			';
		}elseif($mgop_markup_type == 'ol'){
			$template_wrap = '
				<div class="mgop-element mgop_@mgop_slug">
					<div class="mgop-element-content">
						<h3>@mgop_title</h3>
						<ol class="mgop-elements">
							@mgop_list
						</ol>
					</div>
				</div>
			';
		}elseif($mgop_markup_type == 'div'){
			$template_wrap = '
				<div class="mgop-element mgop_@mgop_slug">
					<div class="mgop-element-content">
						<div class="gallery">
							@mgop_list
						</div>
					</div>
				</div>
			';
		}
		
		$mgop_list = '';
		$attachments = array();
		foreach($metavalue as $post_id){
			$img = get_post($post_id);
			$img->full = wp_get_attachment_image_src($post_id, 'full');
			$img->thumb = wp_get_attachment_image_src($post_id, 'thumbnail');
			$img->post_alt = trim(strip_tags( get_post_meta($post_id, '_wp_attachment_image_alt', true) ));
			
			$args = array(
				'@mgop_image_full_url' => $img->full[0],
				'@mgop_image_full_width' => $img->full[1],
				'@mgop_image_full_height' => $img->full[2],
				'@mgop_image_thumb_url' => $img->thumb[0],
				'@mgop_image_thumb_width' => $img->thumb[1],
				'@mgop_image_thumb_height' => $img->thumb[2],
				'@mgop_image_caption' => $img->post_excerpt,
				'@mgop_image_alt' => $img->post_alt,
				'@mgop_image_description' => $img->post_content,
			);
			$mgop_list .= strtr($template_list, $args);
		}		
		
		$mgop_slug = 'mgop_mb_' . $mgop_slug;
		$args = array(
			'@mgop_slug' => $mgop_slug,
			'@mgop_title' => $mgop_title,
			'@mgop_list' => $mgop_list,
		);
		
		return strtr($template_wrap, $args);
		
	}
}


// Register the shortcodes
// Since 0.2
if(! function_exists('mgop_run_gallery_shortcode')){

	function mgop_run_gallery_shortcode($atts, $content = null) {
	    extract(shortcode_atts(array(
	        "slug" => '',
	        "markup" => '',
	    ), $atts));		
		$the_content = mgop_gallery_theme($atts['slug'], '', '', '', $atts['markup']);
		return $the_content;
	}
	add_shortcode("mgop_gallery", "mgop_run_gallery_shortcode");
}


// Add filter to post content in order to append or prepend the galleries
// Since 0.1
if(! function_exists('mgop_add_filter')){
	add_filter('the_content', 'mgop_add_filter');

	function mgop_add_filter($content){
		
		global $post;		
		
		$slug_after = array();
		$slug_before = array();
		
		$options = get_option('mgop_options');
		if(isset($options[$post->post_type]) && $options[$post->post_type]['active']){
			if(count($options[$post->post_type]['titles'])){
				foreach($options[$post->post_type]['titles'] as $mgop_slug => $mgop_item){
					if($mgop_item['position'] == 'after'){
						$slug_after[$mgop_slug] = $mgop_item['title'];
					}elseif($mgop_item['position'] == 'before'){
						$slug_before[$mgop_slug] = $mgop_item['title'];
					}
				}
			}
		}
		
		if(count($slug_after) || count($slug_before)){
			
			$metadata = mgop_get_metadata(null, $post->ID);
			if(count($slug_after)){
				foreach($slug_after as $slug => $title){
					if( isset($metadata['mgop_mb_' . $slug]) ){
						$content .= mgop_gallery_theme($slug, $title, '', $metadata['mgop_mb_' . $slug]);
					}
				}
			}
			if(count($slug_before)){
				$temp = '';
				foreach($slug_before as $slug => $title){
					if( isset($metadata['mgop_mb_' . $slug]) ){
						$temp .= mgop_gallery_theme($slug, $title, '', $metadata['mgop_mb_' . $slug]);
					}
				}
				$content = $temp . $content;
			}
			
		}
		
		return $content;
		
	}
}

	
// Register style and scripts
// Since 0.3
if(! function_exists('mgop_register_plugin_styles')){
	
	add_action( 'wp_enqueue_scripts', 'mgop_register_plugin_styles' );
	function mgop_register_plugin_styles(){
		wp_register_style( 'mgop-theme-style', plugins_url() . '/multiple-gallery-on-post/style/style.css' );
		wp_enqueue_style( 'mgop-theme-style' );
		
		wp_enqueue_script( 'mgop-theme-script', plugins_url() . '/multiple-gallery-on-post/style/style.js' );
	}
	
}

?>
