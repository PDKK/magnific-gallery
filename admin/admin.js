var file_frame;

jQuery(document).ready(function($){

	$('.mgop-wrapper-sortable').sortable();

	mgop_bind_delete_item = function(el){
		
		el.find('.mgop_remove_item').bind('click', function(event){
			
			event.preventDefault();
			
			$(this).parent().parent().fadeOut('fast', function(){
				$(this).remove();
			});
			
		});
		
	}
	
	mgop_bind_delete_item($('.mgop-wrapper-sortable'));

	$('.mgop_add').bind('click', function( event ){

		event.preventDefault();
		
		var the_for = $(this).attr('data-for');
		var the_title = $(this).attr('title');

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title	: 'Select image to insert to "'+ the_title +'"',
			button	: {
				text: 'Insert to "'+ the_title +'"',
			},
			multiple: true  // Set to true to allow multiple files to be selected
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			
			var selection = file_frame.state().get('selection');
	 
			selection.map( function( attachment ) {

				attachment = attachment.toJSON();
				
				var the_list = $('<li class="mgop_thumnails" title="Drag and drop to sort the item"><div><span class="mgop-movable"></span><a href="#" class="mgop_remove_item" title="Click to delete this item"><span>delete</span></a><img src="'+ attachment.sizes.thumbnail.url +'"><input type="hidden" name="mgop_media['+ the_for +'][]" value="'+ attachment.id +'" /></div></li>').hide();
				
				mgop_bind_delete_item(the_list);
				
				$('#mgop_' + the_for).append(the_list);
				
				the_list.fadeIn();
			});

		});

		// Finally, open the modal
		file_frame.open();
	});
	
});