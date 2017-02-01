jQuery(document).ready(function($) {
	$('nav#menu').mmenu({
	slidingSubmenus: false
	}, {
	transitionDuration: 100
	});

//	The menu on the right

	var $menu = $('nav#menu-right');
	$menu.mmenu({
		offCanvas	: {
			position	: 'right'
		},

		classes		: 'mm-light',
		dragOpen	: true,
		counters	: false,
		searchfield	: false,
		header		: {
			add			: true,
			update		: true,
			title		: 'Account'
		}
	});
});

jQuery(document).ready(function($){

	// hide #back-top first
	$("#back-top").hide();
	
	// fade in #back-top
	$(function () {
		$(window).scroll(function () {
			if ($(this).scrollTop() > 100) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});

		// scroll body to 0px on click
		$('#back-top a').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
		});
	});

});