<?php
	
	/**
	 * @class memberextendView
	 * @author skullacy
	 * @brief memberextend View Class
	 */
	class memberextendView extends memberextend
	{
		var $memberextend_config = null;
		var $type_list = null;
		var $active_type_list = null;
		var $skin = null;
		
		function init()
		{
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$this->memberextend_config = $oMemberextendModel->getMemberextendConfig();
			debugPrint($this->memberextend_config);
			Context::set('memberextend_config', $this->memberextend_config);
			//스킨설정, 테마설정기능 미구현
			$this->skin = $this->memberextend_config->skin;
			if(!$this->skin)
			{
				$this->skin = 'default';
			}
			
			$template_path = sprintf('%sskins/%s', $this->module_path, $this->skin);
			$this->setTemplatePath($template_path);
			
			//회원타입 리스트 코드 
			$this->type_list = $oMemberextendModel->getMemberTypes();
			$this->active_type_list = $oMemberextendModel->getMemberTypes('active');
			
			//회원타입 카운트 템플릿으로 전송. (헤더파일에서 타입선택메뉴 표시유무 결정)
			Context::set('type_count', count($this->active_type_list));
			
			//레이아웃 구성 코드 미완.
			$oLayoutModel = &getModel('layout'); /* @var $oLayoutModel layoutModel */
			$layout_info = $oLayoutModel->getLayout($this->memberextend_config->layout_srl);
			if($layout_info)
			{
				$this->module_info->layout_srl = $this->member_config->layout_srl;
				$this->setLayoutPath($layout_info->path);
			}
			
			$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
			$member_config = $oMemberModel->getMemberConfig();
			Context::set('member_config', $member_config);
		}
		
		function dispMemberextendIndex()
		{
			//쿠키값 잔류시 리다이렉트 후 쿠키제거.
			if($_COOKIE['XE_MEMBEREXTEND_REDIRECT_URL'] && $_COOKIE['XE_MEMBEREXTEND_INSERTMEMBER'])
			{
				$redirect_url = $_COOKIE['XE_MEMBEREXTEND_REDIRECT_URL'];
				setcookie('XE_MEMBEREXTEND_REDIRECT_URL', '' ,1);
				setcookie('XE_MEMBEREXTEND_INSERTMEMBER'. '' ,1);
				header("Location: ".$redirect_url);
				exit();
			}
			
			setcookie("XE_MEMBEREXTEND_REDIRECT_URL", $_SERVER['HTTP_REFERER']);
			
			$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
			if($oMemberModel->isLogged()) return $this->stop('msg_already_logged');
			
			if(count($this->active_type_list) == 1)
			{
				foreach($this->active_type_list as $val)
					$redirect_url = getNotEncodedFullUrl('act','dispMemberSignUpForm','member_type',$val->type_srl);
				header("Location: ".$redirect_url);
				exit();
			}
			Context::set('type_list', $this->active_type_list);
			
			
		
			
			
			
			$this->setTemplateFile('index');
		}
		
		
		function dispMemberextendAgreement()
		{
			$logged_info = Context::get('logged_info');
			if($logged_info) return new Object(-1, 'msg_already_logged');
			
			if(!Context::get('member_type')) return new Object (-1, 'msg_invalid_request');
			
			$type_srl = Context::get('member_type');
			$type_property = 'membertype_'.$type_srl;
			
			$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			
			$member_config = $oMemberModel->getMemberConfig('member');
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
						
			$agreement_list = array();
			//공통약관 설정
			if($member_config->agreement)
			{
				$default_agreement->title = Context::getLang('default_agreement');
				$default_agreement->content = $member_config->agreement;
				if($type_srl == 1) $agreement_list[] = $default_agreement;
			}
			
			
			if($type_srl != 1)
			{
				//개별 약관을 사용할 경우
				if($memberextend_config->agreementConfig->{$type_property}->isUse == 'Y')
				{
					//개별 약관, 공통약관을 모두 사용할 경우.
					if($memberextend_config->agreementConfig->{$type_property}->defaultUse == 'Y')
					{
						if($default_agreement) $agreement_list[] = $default_agreement;
					}
					
					if(count($memberextend_config->agreement->{$type_property}->list))
					{
						foreach($memberextend_config->agreement->{$type_property}->list as $key=>$val)
						{
							$agreement_list[] = $val;
						}
					}
				}
				//개별 약관을 사용 안할경우
				else if($memberextend_config->agreementConfig->{$type_property}->isUse == 'N')
				{
					//개별약관을 사용 안하며, 공통 약관도 없을 시 리다이렉트
					if(!$member_config->agreement)
					{
						$_COOKIE['XE_AGREEMENT_'.$type_srl.'CHECK'] = true;
						$redirectUrl = getNotEncodedFullUrl('act','dispMemberSignUpForm','member_type',$type_srl);
						$this->setRedirectUrl($redirectUrl);
					}
					//개별약관을 사용 안하지만, 공통 약관이 존재할 시
					else
					{
						if($default_agreement) $agreement_list[] = $default_agreement;
					}
				}
			}
			
			
			if(count($agreement_list) == 0)
			{
				setcookie('XE_AGREEMENT_'.$type_srl.'CHECK', true);
				$redirectUrl = getNotEncodedFullUrl('act','dispMemberSignUpForm','member_type',$type_srl);
				$this->setRedirectUrl($redirectUrl);
			}
			Context::set('agreement_list', $agreement_list);
			
			
			
			$this->setTemplateFile('agreement');
		}
		
		function dispMemberextendAuthentication()
		{
			$logged_info = Context::get('logged_info');
			if($logged_info) return new Object(-1, 'msg_already_logged');
			
			if(!Context::get('member_type')) return new Object (-1, 'msg_invalid_request');
			
			$type_srl = Context::get('member_type');
			$type_property = 'membertype_'.$type_srl;
			
			$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			
			$member_config = $oMemberModel->getMemberConfig('member');
			Context::set('member_config', $member_config);
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
			
			if($memberextend_config->authenticationUse == 'N')
			{
				$returnUrl = getNotEncodedFullUrl('act','dispMemberSignUpForm');
				$_SESSION['authentication_pass'] = 'Y';
				$this->setRedirectUrl($returnUrl);
			}
			
			$this->setTemplateFile('authentication');
		}
		
	}
?>