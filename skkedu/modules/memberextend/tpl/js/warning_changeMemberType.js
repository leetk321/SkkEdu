jQuery(function($){
	$(document).ready(function(){
		type_srl = $('input[name=type_srl]').val();
	});
	
	$('input[name=type_srl]').change(function(){
		confirm(xe_memberextend_lang_msg_warining_change_membertype);
	});
});
