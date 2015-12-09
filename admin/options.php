<?php

/**
 * Registers setting menu of Multiple Gallery on Post
 */
function mgop_admin_add_page() {
	
	add_options_page('Multiple Gallery on Post Settings', 'Multiple Gallery on Post', 'administrator', 'mgop_setting_menu_id', 'mgop_options_page');
}
add_action('admin_menu', 'mgop_admin_add_page');

/**
 * Options page html elements
 */
function mgop_options_page() {
	?>
	
	<div class="wrap mgop-wrap">
		<?php screen_icon(); ?>
        <h2>Multiple Gallery on Post Settings</h2>
		<br/>		
		<form action="options.php" method="post" class="mgop-form">
			<?php settings_fields('mgop_options'); ?>
			
			<?php echo mgop_setting_string(); ?>
			
			 <br/>
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>		
		<div class="mgop_logo" style="float:left;margin-left: 20px;"></div>
	</div>
	
	<?php
}

/**
 * Registers plugin setting parameter name of Multiple Gallery on Post
 */
function mgop_admin_init(){
	
	wp_enqueue_script('jquery-ui-sortable');
	
	wp_enqueue_style( 'mgop-admin-style', plugins_url() . '/multiple-gallery-on-post/admin/admin.css' );
	wp_enqueue_script( 'mgop-admin-script', plugins_url() . '/multiple-gallery-on-post/admin/admin.js' );

	register_setting( 'mgop_options', 'mgop_options', 'mgop_options_validate' );
	add_settings_section('mgop_setting_main', 'Main Settings', 'mgop_section_text', 'mgop_setting_menu');
	add_settings_field('plugin_text_string', 'Activate on Post Type', 'mgop_setting_string', 'mgop_setting_menu', 'mgop_setting_main');
}
add_action('admin_init', 'mgop_admin_init');


	function mgop_section_text() {
		// returns nothing
		return;
	}

	function mgop_setting_string() {

		$options = get_option('mgop_options');
		
		$post_types_data = get_post_types(array('show_in_nav_menus' => '1'), 'object');
		
		$post_types = array();
		foreach($post_types_data as $obj){
			$post_types[$obj->name] = $obj->labels->singular_name;
		}
		
		foreach($post_types as $post_name => $post_label){
			?>
			<div class="mgop-filed">
				<div class="mgop-filed-header">
					<input class="mgop-post-type" data-for="content_<?php echo  $post_name; ?>" id="mgop_post_<?php echo  $post_name; ?>" name="mgop_options[<?php echo  $post_name; ?>][active]" type="checkbox" value="<?php echo  $post_name; ?>" <?php echo ($options[$post_name]['active'] ? 'checked' : ''); ?> /><label for="mgop_post_<?php echo  $post_name; ?>" class="checkbox-label"><?php echo  $post_label; ?></label>
				</div>
				<div id="content_<?php echo  $post_name; ?>" class="mgop-filed-content<?php echo ($options[$post_name]['active'] ? '' : ' inactive'); ?>">
					<ul id="list_<?php echo  $post_name; ?>" class="sortable-list">
						<?php 
						if(count($options[$post_name]['titles'])){
							foreach($options[$post_name]['titles'] as $slug => $label){ 
						?>
							<li>
								<div class="drag-icon"></div>
								<div class="sortable-list-item-wrapper">
									<div class="field-item">
										<label>Metabox Title</label>
										<input type="text" name="mgop_options[<?php echo  $post_name; ?>][titles][]" value="<?php echo $label['title']; ?>" class="mgop-textfield" />
									</div>				
									<div class="field-item">
										<label>Metabox Slug</label>
										<input type="text" name="" value="<?php echo $slug; ?>" class="mgop-textfield" disabled />
									</div>
									<div class="field-item">
										<label>Output Location</label>
										<select name="mgop_options[<?php echo  $post_name; ?>][position][]" class="mgop-selectfield">
											<option <?php echo ($label['position'] == 'after' ? 'selected' : ''); ?> value="after">After post content</option>
											<option <?php echo ($label['position'] == 'before' ? 'selected' : ''); ?> value="before">Before post content</option>
											<option <?php echo ($label['position'] == 'shortcode' ? 'selected' : ''); ?> value="shortcode">By Shortcode</option>
										</select>
									</div>
									<div class="field-item">
										<label>Available Shortcodes</label>
										<div class="mgop-contentfield">
											<code><?php echo mgop_create_shortcode($slug, 'ul'); ?></code><br/>
											<code><?php echo mgop_create_shortcode($slug, 'ol'); ?></code><br/>
											<code><?php echo mgop_create_shortcode($slug, 'div'); ?></code>
										</div>
									</div>
									<a href="#" class="delete-this"><span>delete</span></a>
								</div>
							</li>
						<?php 
							}
						}else{ ?>
						<li>
							<div class="drag-icon"></div>
							<div class="sortable-list-item-wrapper">							
								<div class="field-item">
									<label>Metabox Title</label>
									<input type="text" name="mgop_options[<?php echo  $post_name; ?>][titles][]" value="Gallery on Post" class="mgop-textfield" />
								</div>	
								<div class="field-item">
									<label>Output Location</label>
									<select name="mgop_options[<?php echo  $post_name; ?>][position][]" class="mgop-selectfield">
										<option value="after">After post content</option>
										<option value="before">Before post content</option>
										<option value="shortcode">By Shortcode</option>
									</select>
								</div>
							</div>
							<a href="#" class="delete-this"><span>delete</span></a>
						</li>
						<?php } ?>
					</ul>				
					<a href="#" data-rel="<?php echo  $post_name; ?>" class="mgop-add-new">Add New</a>
				</div>
			</div>		
			<?php
		}
		?>
		
		<script>
			jQuery(document).ready( function($) {
				
				mgop_apply_delete = function (el){
					el.find('.delete-this').bind('click', function(event){
						event.preventDefault();
						if($(this).parent().parent().parent().children().size() > 1){
							$(this).parent().parent().fadeOut('fast', function(){
								$(this).remove();
							});
						}else{
							alert('Sorry, you cannot remove all the title.');
						}
					});
				}
				
				$('.sortable-list').sortable({handle: '.drag-icon'});
				mgop_apply_delete($('.sortable-list'));
				
				$('.mgop-add-new').bind('click', function(event){
					event.preventDefault();
					
					var post_name = $(this).attr('data-rel');
					var html = $('\
						<li>\
							<div class="drag-icon"></div>\
							<div class="sortable-list-item-wrapper">\
								<div class="field-item">\
									<label>Metabox Title</label>\
									<input type="text" name="mgop_options['+ post_name +'][titles][]" value="Gallery on Post" class="mgop-textfield" />\
								</div>\
								<div class="field-item">\
									<label>Output Location</label>\
									<select name="mgop_options['+ post_name +'][position][]" class="mgop-selectfield">\
										<option value="after">After post content</option>\
										<option value="before">Before post content</option>\
										<option value="shortcode">By Shortcode</option>\
									</select>\
								</div>\
								<a href="#" class="delete-this"><span>delete</span></a>\
							</div>\
						</li>\
					');
					mgop_apply_delete(html);
					$('#list_' + post_name).append(html);
					
					
				});
				
				$('.mgop-post-type').change(function(event){
					
					var data_for = $(this).attr('data-for');
					if($(this).is(':checked')){
						$('#' + data_for).removeClass('inactive');
					}else{
						$('#' + data_for).addClass('inactive');
					}
					
				});
				
			});
		</script>
		
		<?php

	}

/**
 * Validate the setting page submission before saved by WordPress
 */
function mgop_options_validate($input) {
	$options = $input;
	
	if(!$options['validated'] && is_array($options)){
	
		foreach($options as $key => $label){
			$temp = array();
			$index = 0;
			foreach($label['titles'] as $title){
				
				$slug = sanitize_title( $title );
				while( true ){
					if( isset($temp[$slug]) ){
						$slug .= '-1';
					}else{
						break;
					}
				}
				
				$temp[$slug] = array(
					'title' => $title,
					'position' => $label['position'][$index]
				);
				
				$index ++;
			}
			$options[$key]['titles'] = $temp;
		}
		$options['validated'] = '1';
	}
	
	return $options;
}


?>