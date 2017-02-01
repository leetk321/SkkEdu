jQuery(function($){
	$('a._AgreementDelete').click(function(event){
		event.preventDefault();
		if (!confirm(xe.lang.msg_delete_agreement)) return;

		var type_srl = $('input[name="type_srl"]').val();
		var target_index = $(event.target).attr('ivalue');
		var targetTR = $(event.target).closest('tr'); 

		exec_xml(
			'memberextend',
			'procMemberextendAdminDeleteAgree',
			{target_index:target_index, type_srl:type_srl},
			function(ret){
				targetTR.remove();
				location.reload();
			},
			['error','message','tpl']
		);
	});
});