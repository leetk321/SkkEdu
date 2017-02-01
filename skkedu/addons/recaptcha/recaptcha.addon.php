<?php
    if(!defined("__XE__")) exit;

    /**
    * @file recaptcha.addon.php
    * @author CONORY (http://www.conory.com)
    * @brief reCAPTCHA 애드온
    **/
	
	$logged_info = Context::get('logged_info');
	if(!$addon_info->sitekey || !$addon_info->secretkey ||  $logged_info->is_admin == 'Y') return;
	
	//회원일 경우 조건에 의해 제외
	$mact = array('dispCommunicationSendMessage','procCommunicationSendMessage');
	if(Context::get('is_logged') && $addon_info->auth_target != 'Y' && !in_array($this->act, $mact) && !Context::get('recaptcha')) return;
	
	if(!$GLOBALS['_recaptcha_tacts'] || !$GLOBALS['_recaptcha_dacts'])
	{
		$display_acts = array();
		$target_acts = array();
		if($addon_info->apply_write != 'N')
		{
			$display_acts[] = 'dispBoardWrite';
			$display_acts[] = 'dispBoardWriteComment';
			$display_acts[] = 'dispBoardReplyComment';
			$target_acts[] = 'procBoardInsertDocument';
			$target_acts[] = 'procBoardInsertComment';
		}
		if($addon_info->apply_join != 'N')
		{
			$display_acts[] = 'dispMemberSignUpForm';
			$target_acts[] = 'procMemberInsert';
		}
		if($addon_info->apply_find_account != 'N')
		{
			$display_acts[] = 'dispMemberFindAccount';
			$display_acts[] = 'dispMemberResendAuthMail';
			$target_acts[] = 'procMemberFindAccount';
			$target_acts[] = 'procMemberResendAuthMail';
		}
		if($addon_info->apply_message != 'N')
		{
			$display_acts[] = 'dispCommunicationSendMessage';
			$target_acts[] = 'procCommunicationSendMessage';
		}
		
		$GLOBALS['_recaptcha_tacts'] = $target_acts;
		$GLOBALS['_recaptcha_dacts'] = $display_acts;
		
		unset($target_acts);
		unset($display_acts);
	}
	
	if($called_position == 'before_module_init' && !$_SESSION['recaptcha_authed'])
	{
		//target_acts 차단(인증세션이 없는 상태)
		if(in_array($this->act, $GLOBALS['_recaptcha_tacts']))
		{
			$this->error = 'msg_invalid_request';
			return;
		}
		
		//recaptcha 자바스크립트 삽입
		if((Context::get('document_srl') && $addon_info->apply_write != 'N' || in_array($this->act, $GLOBALS['_recaptcha_dacts'])) && Context::getResponseMethod() == 'HTML')
		{
			Context::loadFile(array('./common/js/jquery.min.js', 'head', NULL, -100000), true);
			Context::loadFile(array('./common/js/xe.min.js', 'head', NULL, -100000), true);	
			Context::loadFile(array('./addons/recaptcha/recaptcha.js', 'body', '', null), true);
			Context::addHtmlHeader('<script>var reCaptchaTargetAct = ["' . implode('","', $GLOBALS['_recaptcha_tacts']).'"];</script>');
			
			if(Context::getLangType()=='jp') $lang_code = 'ja';
			else $lang_code = Context::getLangType();
			
			Context::addHtmlHeader('<script src="https://www.google.com/recaptcha/api.js?hl='.$lang_code.'"></script>');
		}
		
		//recaptcha 인증요청
		if(Context::getRequestMethod() == 'XMLRPC' && Context::get('recaptcha'))
		{
			if(Context::get('recaptcha') == 'setCaptchaSession')
			{
				Context::loadLang(_XE_PATH_ . 'addons/recaptcha/lang');
				Context::close();
				header("Content-Type: text/xml; charset=UTF-8");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				printf("<response>\r\n <error>0</error>\r\n <message>success</message>\r\n <sitekey><![CDATA[%s]]></sitekey>\r\n<about_recaptcha><![CDATA[%s]]></about_recaptcha>\r\n  <cmd_cancel><![CDATA[%s]]></cmd_cancel>\r\n </response>",$addon_info->sitekey,Context::getLang('about_recaptcha'),Context::getLang('cmd_cancel'));
				exit;
			}
			else if(Context::get('recaptcha') == 'captchaCompare' && Context::get('recaptchacode'))
			{
				require_once('recaptchalib.php');
				
				$reCaptcha = new ReCaptcha($addon_info->secretkey);
				$resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"],Context::get('recaptchacode'));
				
				if($resp->success)
				{
					$_SESSION['recaptcha_authed'] = true;
				}
				else
				{
					$error = true;
				}
				
				Context::loadLang(_XE_PATH_ . 'addons/recaptcha/lang');
				Context::close();
				header("Content-Type: text/xml; charset=UTF-8");
				header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
				printf("<response>\r\n <error>0</error>\r\n <message>%s</message>\r\n </response>",$error?sprintf(Context::getLang('about_recaptcha_error'),$resp->errorCodes):'success');	
				exit;
			}
			else
			{
				$this->error = 'msg_invalid_request';
				return;
			}
		}
	}
	//캡챠 인증 세션 제거 (매번 인증을 하기위해)
	else if($called_position == 'after_module_proc' && $addon_info->captcha_auth != 'Y' && $_SESSION['recaptcha_authed'])
	{
		if(Context::getResponseMethod() == 'HTML')
		{
			Context::loadFile(array('./common/js/jquery.min.js', 'head', NULL, -100000), true);
			Context::loadFile(array('./common/js/xe.min.js', 'head', NULL, -100000), true);	
			Context::loadFile(array('./addons/recaptcha/recaptcha.js', 'body', '', null), true);
			Context::addHtmlHeader('<script>var reCaptchaTargetAct = ["' . implode('","', $GLOBALS['_recaptcha_tacts']).'"];</script>');
			
			if(Context::getLangType()=='jp') $lang_code = 'ja';
			else $lang_code = Context::getLangType();
			
			Context::addHtmlHeader('<script src="https://www.google.com/recaptcha/api.js?hl='.$lang_code.'"></script>');
		}
		
		unset($_SESSION['recaptcha_authed']);
	}