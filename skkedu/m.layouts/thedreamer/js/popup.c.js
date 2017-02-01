jQuery(document).ready(function($){
	//open popup
	$('#tdSearch').on('click', function(event){
		event.preventDefault();
		$('.cd-popup-c').addClass('is-visible-c');
		$('.tdInput').focus();
	});
	
	//close popup
	$('.cd-popup-c').on('click', function(event){
		if( $(event.target).is('.cd-popup-close') || $(event.target).is('.cd-popup-c') ) {
			event.preventDefault();
			$(this).removeClass('is-visible-c');
		}
	});
	//close popup when clicking the esc keyboard button
	$(document).keyup(function(event){
    	if(event.which=='27'){
    		$('.cd-popup-c').removeClass('is-visible-c');
	    }
    });
});