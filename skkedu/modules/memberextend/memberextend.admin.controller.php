<?php
	
	/**
	 * @class memberextendAdminController
	 * @author skullacy
	 * @brief memberextend Admin Controller Class
	 */
	class memberextendAdminController extends memberextend
	{
		function init()
		{
			$xe_version = (float)substr(__XE_VERSION__, 0, 3);
		}
		
		/**
		 * @brief 사용환경 제공 동의
		 */
		function procMemberextendAdminGatheringAgreement()
		{
			$vars = Context::getRequestVars();
			$oModuleModel = &getModel('module');
			$memberextend_module_info = $oModuleModel->getModuleInfoXml('memberextend');
			$agreement_file = FileHandler::getRealPath(sprintf('%s%s.txt', './files/memberextend/', $memberextend_module_info->version));
			FileHandler::writeFile($agreement_file, $vars->is_agree);
	
			if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON')))
			{
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMemberextendAdminDashboard');
				header('location: ' . $returnUrl);
				return;
			}	
		}
		
		/**
		 * @brief 회원타입 관리.
		 */
		function procMemberextendAdminTypeConfig()
		{
			$vars = Context::getRequestVars();
			
			$type_srls = $vars->type_srls;
			
			foreach($type_srls as $list_order=>$type_srl)
			{
				$update_args = new stdClass();
				$update_args->title = $vars->type_titles[$list_order];
				$update_args->description = $vars->descriptions[$list_order];
				$update_args->list_order = $list_order + 1;
				$update_args->is_active = $vars->{$type_srl.'_isUse'};
				
				if(!$update_args->title) continue;
				if(is_numeric($type_srl))
				{
					$update_args->type_srl = $type_srl;
					$output = $this->updateMemberType($update_args);
				}
				else 
				{
					$update_args->type_srl = getNextSequence();
					$output = $this->insertMemberType($update_args);
				}
				
			}
			
			$this->setMessage(Context::getLang('success_updated').' ('.Context::getLang('msg_insert_type_name_detail').')');
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('','module','admin','act','dispMemberextendAdminTypeList');
			$this->setRedirectUrl($returnUrl);
		}

		/**
		 * @brief 회원타입별 설정 처리
		 * @memo  약관설정 미구현
		 */
		function procMemberextendAdminSignupConfig()
		{
			$all_args = Context::getRequestVars();
			$list_order = Context::get('list_order');
			$usable_list = Context::get('usable_list');
			$type_srl = Context::get('type_srl');
			$type_property = 'membertype_'.$type_srl;
			$redirect_url = Context::get('redirect_url');
			
			//그룹설정
			$target_group = Context::get('target_group');
			$target_group_list = Array();
			if($target_group)
			{
				foreach($target_group as $val)
				{
					$target_group_list[$val] = $val;
				}
			}
			
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
			
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
			
			//리다이렉트
			//2013.07.23 수정내용 : 1.5버전에서는 url을 적을 수 있게 수정.
			if($redirect_url && $xe_version >= 1.6)
			{
				$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
				$redirectModuleInfo = $oModuleModel->getModuleInfoByModuleSrl($redirect_url);
				
				if(!$redirectModuleInfo)
				{
					return new Object('-1', 'msg_exist_selected_module');
				}
				
				 
				if(substr(Context::getDefaultUrl(), -1) != '/')
				{
					$makeRightUrl = Context::getDefaultUrl().'/';
				}
				else
				{
					$makeRightUrl = Context::getDefaultUrl();
				}
				$redirect_url = $makeRightUrl.$redirectModuleInfo->mid;
			}
			
			
			//가입폼 등록
			$extendItems = $oMemberextendModel->getJoinFormList($type_srl);
			
			if($list_order)
			{
				foreach($list_order as $key)
				{
					$signupItem =  new stdClass();
					$signupItem->name = $key;
					$signupItem->title = $key;
					$signupItem->required = ($all_args->{$key} == 'required');
					$signupItem->isUse = in_array($key, $usable_list) || $signupItem->required;
					$signupItem->isPublic = ($all_args->{'is_'.$key.'_public'} == 'Y' && $signupItem->isUse) ? 'Y' : 'N';
					
					$extendItem = $extendItems[$all_args->{$key.'_member_join_form_srl'}];
					$signupItem->type = $extendItem->column_type;
					$signupItem->member_join_form_srl = $extendItem->member_join_form_srl;
					$signupItem->title = $extendItem->column_title;
					$signupItem->description = $extendItem->description;
					
					if($signupItem->isUse != ($extendItem->is_active == 'Y') || $signupItem->required != ($extendItem->required == 'Y'))
					{
						unset($update_args);
						$update_args->member_join_form_srl = $extendItem->member_join_form_srl;
						$update_args->is_active = $signupItem->isUse?'Y':'N';
						$update_args->required = $signupItem->required?'Y':'N';
						
						$update_output = executeQuery('memberextend.updateJoinForm', $update_args);
					}
					
					unset($extendItem);
					$signupForm[] = $signupItem;
				}
			}
			
			
			// 모듈콘픽 업데이트
			// 가입폼
			$args->signupForm = $memberextend_config->signupForm;
			$args->signupForm->$type_property = $signupForm ? $signupForm : NULL;
			
			//약관설정
			$args->agreementConfig = $memberextend_config->agreementConfig;
			$args->agreementConfig->$type_property->isUse = $all_args->enable_agreement;
			if($all_args->enable_agreement_default) $args->agreementConfig->$type_property->defaultUse = $all_args->enable_agreement_default;
			$args->agreement = NULL;
			
			//리다이렉트
			$args->redirect_url = $memberextend_config->redirect_url;
			$args->redirect_url->$type_property = $redirect_url ? $redirect_url : NULL;
			
			//그룹설정
			$args->target_group = $memberextend_config->target_group;
			$args->target_group->$type_property = $target_group_list ? $target_group_list : NULL;
			
			
			$output = $oModuleController->updateModuleConfig('memberextend', $args);
			
			//룰셋파일 생성
			$this->_createRuleset($signupForm, $type_srl);
			
			
			
			$this->setMessage('success_updated');
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMemberextendAdminSignUpConfig', 'type_srl', $type_srl);
			$this->setRedirectUrl($returnUrl); 
			
		}
		
		function procMemberextendAdminAgreementConfig()
		{
			$all_vars = Context::getRequestVars();
			$agreement_no = $all_vars->agreement_no;
			$agreement_content = $all_vars->agreement;
			$agreement_title = $all_vars->agreement_title;
			
			$type_srl = $all_vars->type_srl;
			$type_property = 'membertype_'.$type_srl;
			
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
			
			$agreement_count = count($memberextend_config->agreement->{$type_property}->list);
			if($agreement_no) $agreement_index = $agreement_no;
			else 
			{
				if($agreement_count) $agreement_index = $agreement_count+1;
				else $agreement_index = 1;
			}
			
			
			$agreement_file = _XE_PATH_.'files/member_extra_info/agreement_'.$type_srl.'_'.Context::get('lang_type').'_'.$agreement_index.'.txt';
			$agreement_title_file = _XE_PATH_.'files/member_extra_info/agreement_title_'.$type_srl.'_'.Context::get('lang_type').'_'.$agreement_index.'.txt';
			FileHandler::writeFile($agreement_file, $agreement_content);
			FileHandler::writeFile($agreement_title_file, $agreement_title);
			
			$config_args->agreementConfig = $memberextend_config->agreementConfig;
			if(!$agreement_no) $config_args->agreementConfig->{$type_property}->count = $agreement_count+1;
			
			$config_args->agreement = NULL;
			
			$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
			$oModuleController->updateModuleConfig('memberextend', $config_args);
			
			
			$this->setMessage('success_updated');
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMemberAdminSignUpConfig');
			$this->setRedirectUrl($returnUrl); 
			
		}
		
		function procMemberextendAdminGeneralConfig()
		{
			$all_vars = Context::getRequestVars();
			
			$config_args->progressUse = $all_vars->enable_progressheader;
			$config_args->viewMemberTypeUse = $all_vars->enable_viewmembertype;
			$config_args->skin = $all_vars->skin;
			$config_args->mobile_skin = $all_vars->mobile_skin;
			$config_args->authenticationUse = $all_vars->enable_authentication;
			$config_args->ipinAuthUse = 'N';
			$config_args->idnumberAuthUse = 'N';
			$config_args->phoneAuthUse = $all_vars->enable_phone_auth;
			
			//메일인증 사용유무 체크.
			$enable_mailauth = $all_vars->enable_mailauth;
			
			
			if($config_args->authenticationUse == 'N')
			{
				$config_args->ipinAuthUse = 'N';
				$config_args->idnumberAuthUse = 'N';
				$config_args->phoneAuthUse = 'N';
			}
			
			if($config_args->phoneAuthUse == 'Y')
			{
				$oAuthenticationModel = &getModel('authentication');
				$config_a = $oAuthenticationModel->getModuleConfig();
				if(!$config_a->list) $config_a->list = 'dispMemberSignUpForm';
				
				$oModuleController = getController('module');
				$output_a = $oModuleController->updateModuleConfig('authentication', $config_a);
			}
			elseif($config_args->phoneAuthUse == 'N' || !$config_args->phoneAuthUse)
			{
				$oAuthenticationModel = &getModel('authentication');
				if($oAuthenticationModel)
				{
					$config_a = $oAuthenticationModel->getModuleConfig();
					$config_a->list = NULL;
					$oModuleController = getController('module');
					$output_a = $oModuleController->updateModuleConfig('authentication', $config_a);
				}
				
				//현재는 핸드폰인증만 가능하기떄문에 핸드폰인증이 N일경우 인증페이지 자체를 표시안한다.
				$config_args->authenticationUse = 'N';
			}
			
			$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
			$output = $oModuleController->updateModuleConfig('memberextend', $config_args);
			
			//멤버설정 업데이트 (메일인증 동기화를 위함)
			//$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
			//$member_config = $oModuleModel->getModuleConfig('member');
			$member_config->enable_confirm = $enable_mailauth;
			debugPrint($member_config);
			
			
			$output_m = $oModuleController->updateModuleConfig('member', $member_config);
			
			
			$this->setMessage('success_updated');
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMemberAdminGeneralConfig');
			$this->setRedirectUrl($returnUrl); 
		}
		
		function procMemberextendAdminAgreeOrder()
		{
			$type_srl = Context::get('type_srl');
			if(!$type_srl) return new Object(-1, 'msg_invalid_request');
			
			$list_order = Context::get('list_order');
			if(!$list_order) return new Object(-1, 'msg_invalid_request');
			
			$type_property = 'membertype_'.$type_srl;
			
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
			
			$agreementList = $memberextend_config->agreement->{$type_property}->list;
			
			foreach($list_order as $index=>$key)
			{
				$order = $index+1;
				
				$agreement_file = _XE_PATH_.'files/member_extra_info/agreement_'.$type_srl.'_'.Context::get('lang_type').'_'.$order.'.txt';
				$agreement_title_file = _XE_PATH_.'files/member_extra_info/agreement_title_'.$type_srl.'_'.Context::get('lang_type').'_'.$order.'.txt';
				FileHandler::writeFile($agreement_file, $agreementList[$key]->content);
				FileHandler::writeFile($agreement_title_file, $agreementList[$key]->title);
			}
			
			$this->setMessage('success_updated');
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMemberAdminSignUpConfig');
			$this->setRedirectUrl($returnUrl); 
		}
		
		function procMemberextendAdminDeleteAgree()
		{
			$all_vars = Context::getRequestVars();
			$type_srl = $all_vars->type_srl;
			$type_property = 'membertype_'.$type_srl;
			$agreement_index = $all_vars->target_index;
			
			$oMemberextendModel = &getmodel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
			
			$agreementList = $memberextend_config->agreement->{$type_property}->list;
			$agreementCount = $memberextend_config->agreementConfig->{$type_property}->count;
			for ($i=1; $i <= $agreementCount; $i++) { 
				$target_agree_file = _XE_PATH_.'files/member_extra_info/agreement_'.$type_srl.'_'.Context::get('lang_type').'_'.$i.'.txt';
				$target_agree_title_file = _XE_PATH_.'files/member_extra_info/agreement_title_'.$type_srl.'_'.Context::get('lang_type').'_'.$i.'.txt';
				
				FileHandler::removeFile($target_agree_file);
				FileHandler::removeFile($target_agree_title_file);
			}
			unset($agreementList[$agreement_index]);
			$order = 0;
			foreach($agreementList as $val)
			{
				$order++;
				$target_agree_file = _XE_PATH_.'files/member_extra_info/agreement_'.$type_srl.'_'.Context::get('lang_type').'_'.$order.'.txt';
				$target_agree_title_file = _XE_PATH_.'files/member_extra_info/agreement_title_'.$type_srl.'_'.Context::get('lang_type').'_'.$order.'.txt';
				
				FileHandler::writeFile($target_agree_file, $val->content);
				FileHandler::writeFile($target_agree_title_file, $val->title);
			}
			
			
			$memberextend_config->agreement = NULL;
			
			$memberextend_config->agreementConfig->{$type_property}->count = count($agreementList);
			
			$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
			$oModuleController->updateModuleConfig('memberextend', $memberextend_config);
			
			
			$this->setMessage('success_deleted');
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('','module','admin','act','dispMemberextendAdminAgreementConfig', 'type_srl', $type_srl);
			$this->setRedirectUrl($returnUrl);
		}
		
		/**
		 * @brief 회원타입 삭제처리.
		 */
		function procMemberextendAdminDeleteType()
		{
			$vars = Context::getRequestVars();
			$args->type_srl = $vars->type_srl;
			$output = $this->deleteMemberType($args);
			if(!$output->toBool()) return $output;

			$this->add('type_srl', '');
			
			// 해당 회원타입 설정 삭제
			$output = $this->deleteJoinFormbyTypeSrl($args->type_srl);
			if(!$output->toBool()) return $output;
			
			$oMemberextendModel = &getModel('memberextend'); /*@var $oMemberextendModel memberextendModel */
			$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
			
			$type_property = 'membertype_'.$vars->type_srl;
			
			$config_e = $oMemberextendModel->getMemberextendConfig();
			unset($config_e->signupForm->$type_property);
			$config_e->agreement = NULL;
			
			$config_args = $config_e;
			$config_output = $oModuleController->updateModuleConfig('memberextend', $config_args);
			
			
			$this->setMessage('success_deleted');
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('','module','admin','act','dispMemberextendAdminTypeList');
			$this->setRedirectUrl($returnUrl);
			
		}
		
		/**
		 * @brief 회원타입별 가입폼 추가, 수정
		 */
		function procMemberextendAdminInsertJoinForm()
		{
			$args = new stdClass();
			$args->member_join_form_srl = Context::get('member_join_form_srl');
			$args->type_srl = Context::get('type_srl');
			$type_srl = 'membertype_'.$args->type_srl;
			$args->column_type = Context::get('column_type');
			$args->column_name = strtolower(Context::get('column_id'));
			$args->column_title = Context::get('column_title');
			$args->default_value = explode("\n", str_replace("\r", '', Context::get('default_value')));
			$args->required = Context::get('required');
			$args->is_active = (isset($args->required));
			
			// required 설정
			if(strtoupper($args->required) == 'Y') $args->required = 'Y';
			else $args->required = 'N';
			
			// description
			$args->description = Context::get('description') ? Context::get('description') : '';
			
			// column, default_type 설정
			if(in_array($args->column_type, array('checkbox','select','radio')) && count($args->default_value))
			{
				$args->default_value = serialize($args->default_value);
			}
			else
			{
				$args->default_value = '';
			}
			
			// column ID 체크.
			$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$config = $oMemberModel->getMemberConfig();
			$config_e = $oMemberextendModel->getMemberextendConfig();
			
			foreach($config->signupForm as $item) 
			{
				if($item->name == $args->column_name)
				{
					if($args->member_join_form_srl && $args->member_join_form_srl == $item->member_join_form_srl) continue;
					return new Object(-1,'msg_exists_in_default_signupForm');
				}
			}
			if($config_e->signupForm->$type_srl)
			{
				foreach($config_e->signupForm->$type_srl as $item)
				{
					if($item->name == $args->column_name)
					{
						if($args->member_join_form_srl && $args->member_join_form_srl == $item->member_join_form_srl) continue;
						return new Object(-1, 'msg_exists_in_join_form');
					}
				}
			}
			
			$isInsert;
			if(!$args->member_join_form_srl)
			{
				$isInsert = true;
				$args->list_order = $args->member_join_form_srl = getNextSequence();
				$output = executeQuery('memberextend.insertJoinForm', $args);
			}
			else
			{
				$output = executeQuery('memberextend.updateJoinForm', $args);
			}
			
			if(!$output->toBool()) return $output;
			
			// 콘픽설정 
			$signupItem = new stdClass();
			$signupItem->name = $args->column_name;
			$signupItem->title = $args->column_title;
			$signupItem->type = $args->column_type;
			$signupItem->member_join_form_srl = $args->member_join_form_srl;
			$signupItem->type_srl = $args->type_srl;
			$signupItem->required = ($args->required == 'Y');
			$signupItem->isUse = ($args->is_active == 'Y');
			$signupItem->description = $args->description;
			$signupItem->isPublic = 'Y';
			
			$config_e = $oMemberextendModel->getMemberextendConfig();
			
			if($isInsert)
			{
				$config_e->signupForm->{$type_srl}[] = $signupItem;
			}
			else
			{
				foreach($config_e->signupForm->$type_srl as $key=>$val)
				{
					if($val->member_join_form_srl == $signupItem->member_join_form_srl)
					{
						$config_e->signupForm->{$type_srl}[$key] = $signupItem;
					}
				}
			}
			
			$config_e->agreement = NULL;
			
			$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
			$output = $oModuleController->updateModuleConfig('memberextend', $config_e);
			
			
			//룰셋 업데이트
			if($args->type_srl != 1) $this->_createRuleset($config_e->signupForm->{$type_srl}, $args->type_srl);
			
			
			
			$this->setMessage('success_registed');

			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispMemberAdminJoinFormList');
			$this->setRedirectUrl($returnUrl);
		}

		/**
		 * @brief 회원타입별 가입폼 삭제.
		 */
		function procMemberextendAdminDeleteJoinForm()
		{
			$member_join_form_srl = Context::get('member_join_form_srl');
			$type_srl = Context::get('type_srl');
			$type_property = 'membertype_'.$type_srl;
			
			$this->deleteJoinForm($member_join_form_srl);
	
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$config = $oMemberextendModel->getMemberextendConfig();
	
			foreach($config->signupForm->{$type_property} as $key=>$val)
			{
				if($val->member_join_form_srl == $member_join_form_srl)
				{
					unset($config->signupForm->{$type_property}[$key]);
					break;
				}
			}
			$oModuleController = &getController('module');
			
			$config->agreement = NULL;
			
			$output = $oModuleController->updateModuleConfig('memberextend', $config);
			
			
			//룰셋 업데이트
			if($type_srl != 1) $this->_createRuleset($config->signupForm->{$type_property}, $type_srl);
		}
		
		
		
		/**
		 * @brief 회원타입 생성, 수정, 삭제 처리. 
		 * @func  insertMemberType, updateMemberType, deleteMemberType
		 */
		function insertMemberType($args)
		{
			if($args->is_default!='Y')
			{
				$args->is_default = 'N';
			}
	
			if(!$args->type_srl) $args->type_srl = getNextSequence();
			return executeQuery('memberextend.insertMemberType', $args);
		}
		
		function updateMemberType($args)
		{
			if($args->type_srl == 1) $args->is_default = 'Y';
			else $args->is_default = 'N';
			return executeQuery('memberextend.updateMemberType', $args);
		}
		
		function deleteMemberType($args)
		{
			return executeQuery('memberextend.deleteMemberType', $args);
		}
		
		/**
		 * @brief 선택된 가입폼 삭제. 
		 */
		function deleteJoinForm($member_join_form_srl)
		{
			$args = new stdClass();
			$args->member_join_form_srl = $member_join_form_srl;
			$output = executeQuery('memberextend.deleteJoinForm', $args);
			return $output;
		}
		
		function deleteJoinFormbyTypeSrl($type_srl)
		{
			$args = new stdClass();
			$args->type_srl = $type_srl;
			$output = executeQuery('memberextend.deleteJoinFormbyTypeSrl', $args);
			return $output;
		}
		
		/**
		 * @brief 룰셋파일 생성.
		 */
		function _createRuleset($signupForm, $type_srl, $agreement = null)
		{
			$xml_file = './files/ruleset/insertMember_type_'.$type_srl.'.xml';
			$buff = '<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL.
				'<ruleset version="1.5.0">' . PHP_EOL.
				'<customrules>' . PHP_EOL.
				'</customrules>' . PHP_EOL.
				'<fields>' . PHP_EOL . '%s' . PHP_EOL . '</fields>' . PHP_EOL.
				'</ruleset>';
	
			$fields = array();
	
			if ($agreement)
			{
				$fields[] = '<field name="accept_agreement"><if test="$act == \'procMemberInsert\'" attr="required" value="true" /></field>';
			}
			if($signupForm)
			{
				foreach($signupForm as $formInfo)
				{
					if($formInfo->required)
					{
						if($formInfo->type == 'tel' || $formInfo->type == 'kr_zip')
						{
							$fields[] = sprintf('<field name="%s[]" required="true" />', $formInfo->name);
						}
						elseif($formInfo->type == 'email_address')
						{
							$fields[] = sprintf('<field name="%s" required="true" rule="email" />', $formInfo->name);
						}
						elseif($formInfo->type == 'homepage')
						{
							$fields[] = sprintf('<field name="%s" required="true" rule="url" />', $formInfo->name);
						}
						else
						{
							$fields[] = sprintf('<field name="%s" required="true" />', $formInfo->name);
						}
					}
				}
			}
			
	
			$xml_buff = sprintf($buff, implode(PHP_EOL, $fields));
			FileHandler::writeFile($xml_file, $xml_buff);
			unset($xml_buff);
			
			$validator   = new Validator($xml_file);
			$validator->setCacheDir('files/cache');
			$test = $validator->getJsPath();
		}
		
	}
?>