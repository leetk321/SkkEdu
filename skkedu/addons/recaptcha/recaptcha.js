(function($){
	$(function() {
		var rc = null;
		var bregist = null;
		var load_recaptcha = false;
		var submitBtn_position;
		
		function reCaptchaXE() {
			$('form').each(function(i){
				var isSubmitHook = false;
				if(!$(this).attr('onsubmit') ||  $(this).attr('onsubmit').indexOf('procFilter') < 0){
					var recact = $(this).find('input[name=act]').val();
					for(var i = 0; i<reCaptchaTargetAct.length; i++){
						if(reCaptchaTargetAct[i] == recact){
							isSubmitHook = true;
							break;
						}
					}
				}
				if(isSubmitHook){
					if(!$(this).find('input[name=error_return_url]')) 
						$(this).append('<input type="hidden" name="error_return_url" value="'+current_url+'" />');
					$(this).submit(function(event){
						event.preventDefault();
						var sfe = this;
						bregist = function(){
							sfe.submit();
						}
						var params = new Array();
						params['recaptcha'] = 'setCaptchaSession';
						params['mid'] = current_mid;
						window.oldExecXml('', '', params, rc.show,new Array('error','message','sitekey','about_recaptcha','cmd_cancel'));
					});
				}
			});
			
			var body = $(document.body);
			if(!rc){
				rc = $('<div id="recaptcha_layer" style="position:fixed;top:0;left:0;display:none;width:100%;height:100%;z-index:999999;">').appendTo(document.body);
				
				var $div = $('<div style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:#000;filter: alpha(opacity=70);opacity: .7;"></div><div id="recaptcha_window" style="z-index:1000;position:absolute;margin:-128px 0 0 -166px;top:50%;left:50%;background:#fff;border:3px solid #ccc;"><form method="post" action=""><div style="position:relative;margin:10px;width:304px;overflow:hidden;"><div id="recaptcha_input"></div></div><label id="captchaAbout" for="recaptcha" style="display:block;border-top:1px dashed #c5c5c5;border-bottom:1px dashed #c5c5c5;padding:10px 5px 10px 5px;margin:0 10px;font-size:12px;color:#5f5f5f;"></label><div style="margin:0 10px 0 10px;padding:10px;text-align:center"><button type="button" class="cancel" style="height:31px;line-height:31px;padding:0 15px;margin:0 2px;font-size:12px;font-weight:bold;color:#fff;overflow:visible;border:1px solid #575757;background:#747474;border-radius:3px;-moz-border-radius:3px;-webkit-border-radius:3px;cursor:pointer;box-shadow:0 0 3px #444 inset;-moz-box-shadow:0 0 3px #444 inset;-webkit-box-shadow:0 0 3px #444 inset;"></button></div></form></div>').appendTo(rc);
				
				$div.find('button.cancel').click(function(){
					rc.rcancel();
				});
				
				rc.rcancel = function(recaptchacode){
					$('#recaptcha_layer').hide();
				};				
				rc.show = function(ret_obj) {
					$('#recaptcha_layer').show();
					
					if(!load_recaptcha){
						if(submitBtn_position){
							$('#recaptcha_window').css('top', submitBtn_position + 'px');
							$('#recaptcha_layer').css('position', 'absolute');
						}
						
						$("#captchaAbout").html(ret_obj['about_recaptcha']);
						$("#recaptcha_layer button.cancel").html(ret_obj['cmd_cancel']);
						$div.find('input[type=text]').val('').focus();
						
						grecaptcha.render($('#recaptcha_input').get(0), {'sitekey' : ret_obj['sitekey'], 'theme': 'light', 'callback': rc.rcheck});
						
						load_recaptcha = true;
					}
				};
				rc.rcheck = function(recaptchacode){
					var params = new Array();
					params['recaptcha'] = 'captchaCompare';
					params['mid'] = current_mid;
					params['recaptchacode'] = recaptchacode;
					window.oldExecXml('','',params, function(ret_obj) {
						if(ret_obj['message']=='success') {
							bregist();
						}else{
							rc.rcancel();
							alert(ret_obj['message']);
						}
					}, new Array('error', 'message'));
				};
				rc.exec = function(module, act, oparams, callback_func, response_tags, callback_func_arg, fo_obj) {				
					var doCheck = false;
					$.each(reCaptchaTargetAct || {}, function(key,val){ if (val == act){ doCheck = true; return false; } });
					if (doCheck){
						bregist = function(){
							window.oldExecXml(module, act, oparams, callback_func, response_tags, callback_func_arg, fo_obj);
						}
						var params = new Array();
						params['recaptcha'] = 'setCaptchaSession';
						params['mid'] = current_mid;
						window.oldExecXml('', '', params, rc.show, new Array('error','message','sitekey','about_recaptcha','cmd_cancel'));
					} else {
						window.oldExecXml(module, act, oparams, callback_func, response_tags, callback_func_arg, fo_obj);
					}
					return true;
				};
			}
			return rc;
		}
		$(window).ready(function(){
			if(!window.oldExecXml) {
				window.oldExecXml = window.exec_xml;
				window.exec_xml = reCaptchaXE().exec;
			}
			$('input[type="submit"],button[type="submit"]').focus(function() {
				submitBtn_position = $(this).offset().top;
			});
		});
	});
})(jQuery);