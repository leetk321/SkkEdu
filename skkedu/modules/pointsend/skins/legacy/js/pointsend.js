(function($) {
	$(function(){
		var $form = $('#fo_gift');
		$('.x_modal-body .btnSign').click(function(event){
			var checked = ($(this).data('checked')) == 'Y';
			if(checked) {
				$form.find('input[name=is_subtraction]').val('N');
				$(this).html('+');
				$(this).removeClass('x_btn-danger');
				$(this).addClass('x_btn-success');
				$(this).data('checked', 'N');
			} else {
				$form.find('input[name=is_subtraction]').val('Y');
				$(this).html('-');
				$(this).removeClass('x_btn-success');
				$(this).addClass('x_btn-danger');
				$(this).data('checked', 'Y');
			}
		});
		$('#input_send_point').bind('keydown keyup change', function(event)
		{
			var pass = false;
			// 키보드 상단에 있는 숫자키 허용
			if(event.keyCode >= 48 && event.keyCode <= 57) {
				pass = true;
			} else if(event.keyCode >= 96 && event.keyCode <= 105) {
				// 키보드 우측에 있는 숫자키 허용
				pass = true;
			} else if(event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 13 || event.keyCode == 116)
			{
				// 백스페이스, 탭, 엔터, F5키 허용
				pass = true;
			}

			if(!pass) {
				event.preventDefault();
			}

			var point = parseInt(this.value);
			if(!point) point = 0;

			if(cf_useFee == 'Y')
			{
				var fee = cf_fee / 100;
				var fee_point = parseInt(cf_feePoint);
				var cur_fee = 0;

				if(point) {
					if(fee_point)
					{
						cur_fee = (point >= fee_point) ? point * fee: 0;
					}
					else
					{
						cur_fee = point * fee;
					}
				}

				var feeResult = point ? addCommas(parseInt(cur_fee)) : 0;
				$('#curFee').html(feeResult);
			}

			var remainingPoint = parseInt(cf_curPoint) - point;
			$('#afterPoint').html(addCommas(remainingPoint));
		});
	});

	function addCommas(number) {
		number += '';
		x = number.split('.');
		x1 = x[0];
		x2 = x.length > 1 ? '.' + x[1] : '';
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
			x1 = x1.replace(rgx, '$1' + ',' + '$2');
		}
		return x1 + x2;
	}
})(jQuery);