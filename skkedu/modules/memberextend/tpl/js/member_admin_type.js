jQuery(function($){
	
	$('._addType').click(function (event){
		var $tbody = $('._typeList');
		var index = 'new'+ new Date().getTime();

		$tbody.find('._template').clone(true)
			.removeClass('_template')
			.find('input').removeAttr('disabled').end()
			.find('input:radio[name="_isUse"]').attr('name', index+'_isUse').end()
			.find('input[name="type_srls[]"]').val(index).end()
			.show()
			.appendTo($tbody)
			.find('.lang_code').xeApplyMultilingualUI();

		return false;
	});
	
	$('._deleteType').click(function (event){
		event.preventDefault();
		var $target = $(event.target).closest('tr');
		var type_srl = $(event.target).attr('href').substr(1); 
		if(!confirm(xe.lang.typeDeleteMessage)) return;

		if (type_srl.indexOf("new") >= 0){
			$target.remove();
			return;
		}

		//ajax연동 php코드 미완성
		exec_xml(
			'memberextend',
			'procMemberextendAdminDeleteType',
			{type_srl:type_srl},
			function(){location.reload();},
			['error','message','tpl']
		);

	});

});
