jQuery(document).ready(function($){
		//open popup
	$('.cd-popup-trigger-b').on('click', function(event){
		event.preventDefault();
		$('.cd-popup-b').addClass('is-visible-b');
	});
	
	//close popup
	$('.cd-popup-b').on('click', function(event){
		if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup-b') ) {
			event.preventDefault();
			$(this).removeClass('is-visible-b');
		}
	});
	//close popup when clicking the esc keyboard button
	$(document).keyup(function(event){
    	if(event.which=='27'){
    		$('.cd-popup-b').removeClass('is-visible-b');
	    }
    });
});