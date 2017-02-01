(function($){$(function(){ 
	$.each($('table tbody > .notice'), function(index, value) {
		var document_srl = null;
		$(this).find('input').each(function() {
			document_srl = $(this).val();
        });
		$(this).find('.no').each(function() {
			$(this).html('<a href="'+addon_exnotice_var+'&document_srl='+document_srl+'&is_ex_notice='+1+'"><strong>공지</strong></a>');
        });        
		$(this).find('.notice').each(function() {
			$(this).html('<a href="'+addon_exnotice_var+'&document_srl='+document_srl+'&is_ex_notice='+1+'"><strong>공지</strong></a>');
        });      
	});
}); })(jQuery);
