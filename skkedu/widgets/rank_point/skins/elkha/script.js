/**
 * 김무건 || Elkha (elkha1914@hotmail.com)
 * http://elkha.kr
 * 2010-08-19
 **/

(function($){
	$(document).ready(function(){
		_best = $(".ePoint tr:first-child td.point").text();
		$(".ePoint td.point span em").each(function(){
			_self = $(this).text();
			_percent = Math.floor( _self / _best * 100 );
            if(_percent < 95) {
                _percent = _percent + 5;
            }
			$(this).animate({"width":_percent + "%"}, 1000);
		});
	});
})(jQuery);
