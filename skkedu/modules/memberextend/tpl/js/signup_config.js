jQuery(function($){
	var suForm = $('table.__join_form'); // 회원가입 양식

	function changeTable($i)
	{
			if($i.is(':checked')){
				$i.parent('td').next('td').next('td')
					.find('>._subItem').show().end()
					.find(':radio, [type="number"]')
						.removeAttr('disabled')
						.end()
					.find(':radio[value=option]').attr('checked', 'checked')
						.end()
					.prev('td')
					.find(':input[value=Y]').removeAttr('disabled').attr('checked', 'checked');
				
			} else {
				$i.parent('td').next('td').next('td')
					.find('>._subItem').hide().end()
					.find(':radio, [type="number"]').attr('disabled','disabled').removeAttr('checked')
						.next('label').css('fontWeight','normal').end()
						.end()
					.prev('td')
					.find(':input[value=Y]').removeAttr('checked').attr('disabled', 'disabled');
			}

	}

	suForm.find(':checkbox[name="usable_list[]"]').each(function(){
		var $i = $(this);

		$i.change(function(){
			changeTable($i);
		});
	});
	
	
	$('a.modalAnchor._extendFormEdit').bind('before-open.mw', function(event){
			var memberextendFormSrl = $(event.target).parent().attr('id');
			var checked = $(event.target).closest('tr').find('input:radio:checked').val();
	
			exec_xml(
				'memberextend',
				'getMemberextendAdminInsertJoinForm',
				{member_join_form_srl:memberextendFormSrl, type_srl:type_srl},
				function(ret){
					var tpl = ret.tpl.replace(/<enter>/g, '\n');
					$('#extendForm').html(tpl);
	
					if (checked)$('#extendForm #radio_'+checked).attr('checked', 'checked');
				},
				['error','message','tpl']
			);
	
	});
	
	$('a._extendFormDelete').click(function(event){
		event.preventDefault();
		if (!confirm(xe.lang.msg_delete_extend_form)) return;

		var memberFormSrl = $(event.target).parent().attr('id');
		var targetTR = $(event.target).closest('tr'); 

		exec_xml(
			'memberextend',
			'procMemberextendAdminDeleteJoinForm',
			{member_join_form_srl:memberFormSrl, type_srl:type_srl},
			function(ret){
				targetTR.remove();
			},
			['error','message','tpl']
		);
	});
	
	$('.__redirect_url_btn').click(function(e){
		$(this).parent().find('input[name=redirect_url]').val('');
		$(this).parent().find('input[type=text]').val('');
	});
	
	$('input[name="enable_agreement"]').click(function(){
		if($(this).val() == 'Y'){
			$('#enable_agreement_default').find('input').removeAttr('disabled');
			$('#enable_agreement_default').show();
			
		}
		else if($(this).val() == 'N') {
			$('#enable_agreement_default').find('input').attr('disabled', 'disabled');
			$('#enable_agreement_default').hide();
		}
	});
});