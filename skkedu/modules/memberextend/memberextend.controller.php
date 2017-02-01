<?php
	
	/**
	 * @class memberextendController
	 * @author skullacy
	 * @brief memberextend Controller Class
	 */
	class memberextendController extends memberextend
	{
		
		var $activateModuleHandler = array('dispMemberSignUpForm', 'dispMemberModifyInfo', 'dispMemberAdminInsert', 'dispMemberInfo');
		var $ModuleHandler_write = array('dispMemberSignUpForm', 'dispMemberModifyInfo', 'dispMemberAdminInsert');
		var $ModuleHandler_view = array('dispMemberInfo');
		
		function init()
		{
		}
		
		function procMemberextendAgreement()
		{
			$logged_info = Context::get('logged_info');
			if($logged_info) return new Object(-1, 'msg_already_logged');
			
			$all_vars = Context::getRequestVars();
			$mid = $all_vars->mid;
			$type_srl = $all_vars->type_srl;
			$agreement_count = $all_vars->agreement_count;
			$accept_agreement = $all_vars->accept_agreement;
			
			if(!$type_srl) return new Object(-1, 'msg_invalid_request');
			if($agreement_count != count($accept_agreement)) return new Object(-1, 'msg_invalid_request');
			
			setcookie('XE_AGREEMENT_'.$type_srl.'CHECK', TRUE);
			
			$redirect_url = getNotEncodedFullUrl('', 'act','dispMemberSignUpForm','mid',$mid,'member_type',$type_srl);
			$this->setRedirectUrl($redirect_url);
		}
		
		function procMemberextendAuthentication()
		{
			$logged_info = Context::get('logged_info');
			if($logged_info) return new Object(-1, 'msg_already_logged');
			
			$all_vars = Context::getRequestVars();
			$type_srl = $all_vars->type_srl;
			$auth_type = $all_vars->auth_type;
			
			if($auth_type == 'phone')
			{
				setcookie('XE_MEMBEREXTEND_AUTH_PHONE', TRUE);
				$redirect_url = getNotEncodedFullUrl('', 'act','dispMemberSignUpForm','member_type',$type_srl);
				$this->setRedirectUrl($redirect_url);
			}
		}
		
		function triggerModuleHandlerProc(&$oModule)
		{
			
			/**
			 * @brief activateModuleHandler변수 안의 액션값에서만 작동한다.
			 */
			if(in_array(Context::get('act'), $this->activateModuleHandler))
			{
				$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
				$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
				$oMemberextendAdminView = &getAdminView('memberextend'); /* @var $oMemberextendAdminView memberextendAdminView */
				
				if(Context::get('act') == 'dispMemberSignUpForm' && !Context::get('member_type')) return;
				
				//target member_srl 구하기
				if(in_array(Context::get('act'), $this->ModuleHandler_write))
				{
					if(Context::get('member_srl')) $member_srl = Context::get('member_srl');
					else
					{
						$logged_info = Context::get('logged_info');
						$member_srl = $logged_info->member_srl;
					}
				}
				else if(in_array(Context::get('act'), $this->ModuleHandler_view))
				{
					$memberInfo = Context::get('memberInfo');
					$member_srl = $memberInfo['member_srl'];
				}
				
				
				//type_srl 구하기
				if(Context::get('member_type')) $type_srl = Context::get('member_type');
				elseif(Context::get('type_srl')) $type_srl = Context::get('type_srl');
				else 
				{
					$memberextend_info = $oMemberextendModel->getMemberextendInfo($member_srl);
					$type_srl = $memberextend_info->type_srl;
				}
				$type_property = 'membertype_'.$type_srl;
				
				//콘픽 로드
				$member_config = $oMemberModel->getMemberConfig();
				$memberextend_config = $oMemberextendModel->getMemberextendConfig();
				
				
				/**
				 * @brief 회원정보 입력, 수정시 
				 */
				if(in_array(Context::get('act'), $this->ModuleHandler_write))
				{
					// 폼 추가
					$formTags = Context::get('formTags');
					if(count($memberextend_config->signupForm->$type_property))
					{
						$memberextend_formTags = $oMemberextendAdminView->_getMemberextendInputTag($type_srl, $memberextend_info);
						foreach($memberextend_formTags as $val)
						{
							$formTags[] = $val;
						}
					}
					
					//핸드폰인증 사용하는경우 확장변수에 값을 집어넣는다.
					if($memberextend_config->phoneAuthUse == 'Y')
					{
						$auth_srl = $_SESSION['authentication_srl'];
						$auth_args->authentication_srl = $auth_srl;
						$output_a = executeQuery('authentication.getAuthentication', $auth_args);
						$clue_phone = $output_a->data->clue;
						
						//전화번호 자르기
						$phone[] = substr($clue_phone, 0, 3);
						if(strlen($clue_phone) == 10)
						{
							$phone[] = substr($clue_phone, 3, 3);
							$phone[] = substr($clue_phone, 6, 4);
						}
						elseif(strlen($clue_phone) == 11){
							$phone[] = substr($clue_phone, 3, 4);
							$phone[] = substr($clue_phone, 7, 4);
						}
						
						Context::addJsFile($this->module_path.'tpl/js/fillphone.js');
						Context::addHtmlHeader('<script type="text/javascript">jQuery(document).ready(function(){addAuthPhoneNumber("'.$clue_phone.'", "mobilephone");});</script>');
						
						debugPrint($phone);
						
					}
					
					
					//어드민메뉴일 경우
					if(strpos(Context::get('act'), 'Admin'))
					{
						global $lang;
						$type_list = $oMemberextendModel->getMemberTypes();
						
						$selectMemberType = new stdClass();
						$selectMemberType->title = $lang->type_title;
						$selectMemberType->name = 'type_srl';
						$selectMemberType->type = 'radio';
						$selectMemberType->inputTag = '<div style="padding-top:5px">';
						foreach($type_list as $key=>$val)
						{
							$radio_check = ($type_srl == $key) ? 'checked=checked' : '';
							$selectMemberType->inputTag .= '<label><input type="radio" name="type_srl" value="'.$key.'" '.$radio_check.'/>'.$val->title.'</label>';
						}
						$selectMemberType->inputTag .= '</div>';
						Context::addJsFile($this->module_path.'/tpl/js/warning_changeMemberType.js');
						Context::addHtmlHeader('<script type="text/javascript">var xe_memberextend_lang_msg_warining_change_membertype = "'.Context::getLang('msg_warning_change_membertype').'"; </script>');
						$formTags[] = $selectMemberType;
					}
					
					
					Context::set('formTags', $formTags);
					
				}
				/**
				 * @brief 회원정보 조회시
				 */
				else if(in_array(Context::get('act'), $this->ModuleHandler_view))
				{
					$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
					$config = $oModuleModel->getModuleConfig('memberextend');
					
					$displayDatas = Context::get('displayDatas');
					if($type_srl != 1)
					{
						$memberextendFormInfo = $oMemberextendModel->getCombineJoinForm($type_srl, $memberextend_info);
						
						//2013.07.23 개별가입폼이 1개일때 회원정보에 표시안되던 문제 수정
						if(count($memberextendFormInfo) >= 1)
						{
							$memberextend_displayDatas = $this->_getDisplayedMemberInfo($memberextend_info, $memberextendFormInfo, $memberextend_config, $type_srl);
							foreach($memberextend_displayDatas as $key=>$val)
							{
								$displayDatas[] = $val;
							}
						}
						
					}
					
					/**
					 * 멤버타입 표시코드
					 * @memo 표시유무를 모듈설정에서 컨트롤가능하게 코드 구현해야함.
					 */
					
					if($config->viewMemberTypeUse == 'Y')
					{
						$memberType_displayData = new stdClass();
						$memberType_displayData->type = 'text';
						$memberType_displayData->name = 'MEMBEREXTEND_TYPE';
						$memberType_displayData->title = Context::getLang('type_title');
						$memberType_displayData->isUse = true;
						$memberType_displayData->isPublic = true;
						$memberType_displayData->value = $oMemberextendModel->getMemberType($type_srl)->title;
						
						$displayDatas[] = $memberType_displayData;
					}
					
					Context::set('displayDatas', $displayDatas);
				}
				
			}
		}
		
		function triggerDispMemberSignupFormBefore(&$member_config)
		{
			
			//가입약관 강제 비활성화
			$member_config->agreement = null;
		}
		
		/**
		 * @brief 회원가입후 리다이렉트 바꿔치기 트리거
		 */
		function triggerProcMemberInsert(&$config)
		{
			$type_srl = Context::get('type_srl');
			$type_property = 'membertype_'.$type_srl;
			
			$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
			$memberextend_config = $oModuleModel->getModuleConfig('memberextend');
			
			if($type_srl != 1)
			{
				if($memberextend_config->redirect_url->$type_property)
				{
					$config->redirect_url = $memberextend_config->redirect_url->$type_property;
				}
				else
				{
					//2013.07.23 기본회원정보에 리다이렉트 정보가 없는경우에만 쿠키와 success_return_url 사용.
					if(!$config->redirect_url)
					{
						if(Context::get('success_return_url'))
						{
							$config->redirect_url = Context::get('success_return_url');
						}
						else
						{
							$config->redirect_url = $_COOKIE['XE_MEMBEREXTEND_REDIRECT_URL'] ? $_COOKIE['XE_MEMBEREXTEND_REDIRECT_URL'] : $_COOKIE['XE_REDIRECT_URL'];
						}
					}
					
				}
			}
			
			
		}
		
		/**
		 * @brief addon called_position = before_display_content와 상응하는 트리거.
		 */
		function triggerDisplay(&$output)
		{
			//회원가입메뉴 이동시, 회원타입 선택메뉴로 이동.
			if(Context::get('act') == 'dispMemberSignUpForm')
			{
				if(Context::get('member_type'))
				{
					//약관 동의 페이지로부터 쿠키를 얻어온 경우
					if($_COOKIE['XE_AGREEMENT_'.Context::get('member_type').'CHECK'] || ($_COOKIE['XE_MEMBEREXTEND_AUTH_PHONE'] && !$_COOKIE['XE_AGREEMENT_'.Context::get('member_type').'CHECK']))
					{
						//type_srl, 약관동의 코드 삽입
						$output = str_replace('<input type="hidden" name="ruleset" value="@insertMember" />', '<input type="hidden" name="ruleset" value="@insertMember" /><input type="hidden" name="accept_agreement" value="Y" id="accept_agree" /><input type="hidden" name="type_srl" value="'.Context::get('member_type').'" />', $output);
						
						//공통 헤더 삽입
						$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
						$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
						$config = $oMemberextendModel->getMemberextendConfig();
						$member_config = $oMemberModel->getMemberConfig();
						
						$oTemplate_h = &TemplateHandler::getInstance();
						Context::set('memberextend_config', $config);
						Context::set('member_config', $member_config);
						Context::set('type_count', count($oMemberextendModel->getMemberTypes('active')));
						
						
						$code_h = $oTemplate_h->compile($this->module_path.'skins/'.$config->skin,'header');
						$code_h = preg_replace('/\r\n|\r|\n/','',$code_h);
						$code_h = str_replace("'", "\'", $code_h);
						$code_h = str_replace('"', '\"', $code_h);
						
						$output_h = array();
						$output_h[] = '<script>//<![CDATA[';
						$output_h[] = 'var memberextend_header_output = "'.$code_h.'";';
						$output_h[] = '//]]></script>';
						$output_h = implode("\n", $output_h);
						
						Context::addHtmlHeader($output_h);
						Context::addJsFile($this->module_path.'tpl/js/insert_me_header.js');
						
						//핸드폰인증 요청 쿠키 지움, 
						setcookie('XE_MEMBEREXTEND_AUTH_PHONE','',1);
					}
					//약관 동의 페이지를 넘어가지 않음.
					else
					{
						$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
						$config = $oMemberextendModel->getMemberextendConfig();
						//인증메뉴를 사용함.
						if($config->authenticationUse == 'Y')
						{
							if($_SESSION['authentication_pass'] == 'Y')
							{
								$redirect_url = getNotEncodedFullUrl('act','dispMemberextendAgreement');
							}
							else 
							{
								$redirect_url = getNotEncodedFullUrl('act','dispMemberextendAuthentication');
							}
							header("Location: ".$redirect_url);
							exit();
							
						}
						//사용하지 않을경우, 바로 약관 페이지로 넘어감.
						else 
						{
							$redirect_url = getNotEncodedFullUrl('act','dispMemberextendAgreement');
							header("Location: ".$redirect_url);
							exit();
						}
					}
					
				}
				else 
				{
					$redirect_url = getNotEncodedFullUrl('act','dispMemberextendIndex');
					header("Location: ".$redirect_url);
					exit();
				}
			}
			
			if(Context::get('act') == 'dispMemberModifyInfo')
			{
				$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
				
				$logged_info = Context::get('logged_info');
				$member_srl = $logged_info->member_srl;
				$memberextend_info = $oMemberextendModel->getMemberextendInfo($member_srl);
				$type_srl = $memberextend_info->type_srl;
				$output = str_replace('<input type="hidden" name="ruleset" value="@insertMember" />', '<input type="hidden" name="ruleset" value="@insertMember" /><input type="hidden" name="type_srl" value="'.$type_srl.'" />', $output);
			}
			
			if(strpos(Context::get('act'), 'Memberextend'))
			{
				if(Context::get('act') != 'dispMemberextendAdminDashboard')
				{
					$cache_file = sprintf("%sfiles/cache/memberextend/memberextend.cache.php", _XE_PATH_);
					if(file_exists($cache_file))
					{
						$oXml = new XmlParser();
						$buff = $oXml->parse(FileHandler::readFile($cache_file));
					
						$permission = $buff->response->noticeinfo->permission->body;
						if($permission == 'N')
							$output = 'Memberextend 모듈의 사용이 금지된 사이트입니다.';
					}
					else
					{
						$output = 'Memberextend(회원모듈 확장) 모듈의 설치가 완료되지 않았습니다. 해당 모듈의 설정페이지로 이동하여 설치를 완료하여 주시기 바랍니다.';
					}
				}
			}
			
		}
		
		
		/**
		 * @brief 회원등록시 멤버타입별 데이터입력 유효성 체크(룰셋) 하는 트리거
		 * @memo debugPrint에 출력안되므로 Redirect 주석처리 후 작업. memberadminController 126ln, memberController 380ln.
		 */
		function triggerInsertMemberBefore(&$args)
		{
			$all_vars = unserialize($args->extra_vars);
			$type_srl = $all_vars->type_srl;
			
			
			// 멤버타입별 룰셋 적용후, 유효성검사.
			if($type_srl != 1) 
			{
				$this->validateExtendSignUpForm($type_srl);
			}
			
		}
		
		
		/**
		 * @brief 회원등록 시 데이터 구분 후 처리하는 트리거 
		 * @memo debugPrint에 출력안되므로 Redirect 주석처리 후 작업. memberadminController 126ln, memberController 380ln, 418ln.
		 */
		function triggerInsertMember(&$args)
		{
			// extra_var 에서 해당 회원타입정보 빼낸 후 다시 serialize
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$config = $oMemberextendModel->getMemberextendConfig();
			
			$memberextend_args = new stdClass();
			$extra_args = unserialize($args->extra_vars);
			$memberextend_args->member_srl = $args->member_srl;
			$memberextend_args->type_srl = $extra_args->type_srl;
			$type_property = 'membertype_'.$extra_args->type_srl;
			
			$dummy = new stdClass();
			if(count($config->signupForm->$type_property))
			{
				foreach($config->signupForm->$type_property as $item)
				{
					if($item->isUse)
					{
						$dummy->{$item->name} = $extra_args->{$item->name};
						unset($extra_args->{$item->name});
					}
				}
				$memberextend_args->type_extra_vars = serialize($dummy);
			}
			else $memberextend_args->type_extra_vars = '';
			
			//DB 트랜잭션.
			$oDB = &DB::getInstance();
			$oDB->begin();
			
			
			if(is_array($config->target_group->$type_property))
			{
				//타겟그룹이 설정된 경우, 그룹 바꿔치기
				$is_default_group = true;
					//현재 가상도메인에 관련된 회원그룹은 지원안하므로 site_srl = 0설정.
					$group_args->site_srl = 0;
				$group_args->member_srl = $args->member_srl;
				//member group 초기화.			
				$output = executeQuery('member.deleteMemberGroupMember', $group_args);
				if(!$output->toBool())
				{
					$oDB->rollback();
					return $output;
				}
				
				$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
				$oMemberController = &getController('member'); /* @var $oMemberController memberController */
				$group_list = $oMemberModel->getGroups();
				foreach($config->target_group->$type_property as $val)
				{
					if($group_list[$val])
					{
						$output = $oMemberController->addMemberToGroup($args->member_srl,$val);
						if(!$output->toBool())
						{
							$oDB->rollback();
							return $output;
						}
						$is_default_group = false;
					}
				}
				//그룹이 존재하지 않거나 오류가 생겨서 제대로 그룹설정이 안된경우 디폴트로 변경.
				if($is_default_group)
				{
					$columnList = array('site_srl', 'group_srl');
					$default_group = $oMemberModel->getDefaultGroup(0, $columnList);
					if($default_group)
					{
						// Add to the default group
						$output = $this->addMemberToGroup($args->member_srl,$default_group->group_srl);
						if(!$output->toBool()) 
						{
							$oDB->rollback();
							return $output;
						}
					}
				}
			}
			
			$insert_output = $this->insertMember($memberextend_args);
			$args->extra_vars = serialize($extra_args);
			$member_output = executeQuery('member.updateMember', $args);
			
			
			setcookie('XE_MEMBEREXTEND_INSERTMEMBER', true);
			
			//약관동의 쿠키삭제
			$type_list = $oMemberextendModel->getMemberTypes();
			foreach($type_list as $key=>$val)
			{
				setcookie('XE_AGREEMENT_'.$key.'CHECK', '', 1);
			}
			
		}
		
		/**
		 * @brief 회원정보 수정시 멤버타입별 데이터입력 유효성 체크(룰셋) 하는 트리거
		 */
		function triggerUpdateMemberBefore(&$args)
		{
			$all_vars = unserialize($args->extra_vars);
			$type_srl = $all_vars->type_srl;
			
			// 멤버타입별 룰셋 적용후, 유효성검사.
			if($type_srl != 1) 
			{
				if($_SESSION['rechecked_password_step'] = 'INPUT_DATA') $_SESSION['rechecked_password_step'] = 'VALIDATE_PASSWORD';
				$this->validateExtendSignUpForm($type_srl);
			}
		}
		
		/**
		 * @brief 회원등록 시 데이터 구분 후 처리하는 트리거 
		 * @memo debugPrint에 출력안되므로 Redirect 주석처리 후 작업. memberController 418ln.
		 */
		function triggerUpdateMember(&$args)
		{
			// extra_var 에서 해당 회원타입정보 빼낸 후 다시 serialize
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$config = $oMemberextendModel->getMemberextendConfig();
			
			$memberextend_args = new stdClass();
			$extra_args = unserialize($args->extra_vars);
			$memberextend_args->member_srl = $args->member_srl;
			$memberextend_args->type_srl = $extra_args->type_srl;
			$type_property = 'membertype_'.$extra_args->type_srl;
			
			$dummy = new stdClass();
			if(count($config->signupForm->$type_property))
			{
				foreach($config->signupForm->$type_property as $item)
				{
					if($item->isUse)
					{
						$dummy->{$item->name} = $extra_args->{$item->name};
						unset($extra_args->{$item->name});
					}
				}
				$memberextend_args->type_extra_vars = serialize($dummy);
			}
			else $memberextend_args->type_extra_vars = '';
			
			
			$insert_output = $this->updateMember($memberextend_args);
			$args->extra_vars = serialize($extra_args);
			$member_output = executeQuery('member.updateMember', $args);
		}
		
		function triggerDeleteMember(&$args)
		{
			$this->deleteMember($args);
		}
		
		
		function insertMember($args)
		{
			$oDB = &DB::getInstance();
			$oDB->begin();
			
			$output = executeQuery('memberextend.insertMember', $args);
			if(!$output->toBool())
			{
				$oDB->rollback();
				return new Object(-1, 'msg_error_occured');
			}
			
			$oDB->commit();
			
			return $output;
			
		}
		
		function updateMember($args)
		{
			$oDB = &DB::getInstance();
			$oDB->begin();
			
			$output = executeQuery('memberextend.updateMember', $args);
			if(!$output->toBool())
			{
				$oDB->rollback();
				return new Object(-1, 'msg_error_occured');
			}
			
			$oDB->commit();
			
			return $output;
			
		}
		
		function deleteMember($args)
		{
			$oDB = &DB::getInstance();
			$oDB->begin();
			
			$output = executeQuery('memberextend.deleteMember', $args);
			if(!$output->toBool())
			{
				$oDB->rollback();
				return new Object(-1, 'msg_error_occured');
			}
			
			$oDB->commit();
			return $output;
		}
		
		/**
		 * @brief 멤버타입별 룰셋 적용후 체크.
		 */
		function validateExtendSignUpForm($type_srl)
		{
			if(strpos(Context::get('act'), 'Admin')) return;
			
			$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
			
			//룰셋 파일 로딩 후 인스턴스 생성.
			$rulesetModule = 'member';
			$ruleset = '@insertMember_type_'.$type_srl;
			$rulesetFile = $oModuleModel->getValidatorFilePath($rulesetModule, $ruleset);
			$Validator = new Validator($rulesetFile);
			$result = $Validator->validate();
			if(!$result)
			{
				$oMemberController = &getController('member'); /* @var $oMemberController memberController */
				
				$lastError = $Validator->getLastError();
				$returnUrl = Context::get('error_return_url');
				
				$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
				
				//에러 메세지 가공코드
				$usedJoinForm = $oMemberextendModel->getUsedJoinFormList($type_srl);
				foreach($usedJoinForm as $formInfo)
				{
					if($lastError['field'] == $formInfo->column_name) $lastError['msg'] = str_replace($lastError['field'], $formInfo->column_title, $lastError['msg']);
				}
				$errorMsg = $lastError['msg'] ? $lastError['msg'] : 'validation error';
				if($errorMsg == 'validation error')
				{
					foreach($usedJoinForm as $formInfo)
					{
						if($lastError['field'] == $formInfo->column_name) $lastError['msg'] = sprintf(Context::getLang('msg_fieldrequired'), $formInfo->column_title);
					}
				}
				$errorMsg = $lastError['msg'];
				
				//에러 메세지 적용
				$oMemberController->setError(-1);
				$oMemberController->setMessage($errorMsg);
				$oMemberController->error = $errorMsg;
				$_SESSION['XE_VALIDATOR_ERROR'] = -1;
				$_SESSION['XE_VALIDATOR_MESSAGE'] = $errorMsg;
				$_SESSION['XE_VALIDATOR_MESSAGE_TYPE'] = 'error';
				$_SESSION['XE_VALIDATOR_RETURN_URL'] = $returnUrl;
				$_SESSION['XE_VALIDATOR_ID'] = Context::get('xe_validator_id');
				
				$this->_setInputValueToSession();
				
				header("Location: ".$returnUrl);
				exit();
			}
		}
		
		/**
		 * @brief 세션에 인풋값 저장
		 */
		function _setInputValueToSession()
		{
			$requestVars = Context::getRequestVars();
			unset($requestVars->act, $requestVars->mid, $requestVars->vid, $requestVars->success_return_url, $requestVars->error_return_url);
			foreach($requestVars AS $key => $value)
			{
				$_SESSION['INPUT_ERROR'][$key] = $value;
			}
		}
		
		
		function _getDisplayedMemberInfo($memberInfo, $extendFormInfo, $memberextendConfig, $type_srl)
		{
			$type_property = 'membertype_'.$type_srl;
			$logged_info = Context::get('logged_info');
			$displayDatas = array();
			foreach($memberextendConfig->signupForm->$type_property as $no=>$formInfo)
			{
				if(!$formInfo->isUse)
				{
					continue;
				}
	
				if($formInfo->name == 'password' || $formInfo->name == 'find_account_question')
				{
					continue;
				}
	
				if($memberInfo->member_srl != $logged_info->member_srl && $formInfo->isPublic != 'Y')
				{
					continue;
				}
	
				$item = $formInfo;
	
				if($formInfo->isDefaultForm)
				{
					$item->title = Context::getLang($formInfo->name);
					$item->value = $memberInfo->{$formInfo->name};
	
					if($formInfo->name == 'profile_image' && $memberInfo->profile_image)
					{
						$target = $memberInfo->profile_image;
						$item->value = '<img src="'.$target->src.'" />';
					}
					elseif($formInfo->name == 'image_name' && $memberInfo->image_name)
					{
						$target = $memberInfo->image_name;
						$item->value = '<img src="'.$target->src.'" />';
					}
					elseif($formInfo->name == 'image_mark' && $memberInfo->image_mark)
					{
						$target = $memberInfo->image_mark;
						$item->value = '<img src="'.$target->src.'" />';
					}
					elseif($formInfo->name == 'birthday' && $memberInfo->birthday)
					{
						$item->value = zdate($item->value, 'Y-m-d');
					}
				}
				else
				{
					$item->title = $extendFormInfo[$formInfo->member_join_form_srl]->column_title;
					$orgValue = $extendFormInfo[$formInfo->member_join_form_srl]->value;
					if($formInfo->type=='tel' && is_array($orgValue))
					{
						$item->value = implode('-', $orgValue);
					}
					elseif($formInfo->type=='kr_zip' && is_array($orgValue))
					{
						$item->value = implode(' ', $orgValue);
					}
					elseif($formInfo->type=='checkbox' && is_array($orgValue))
					{
						$item->value = implode(", ",$orgValue);
					}
					elseif($formInfo->type=='date')
					{
						$item->value = zdate($orgValue, "Y-m-d");
					}
					else
					{
						$item->value = nl2br($orgValue);
					}
				}
	
				$displayDatas[] = $item;
			}
	
			return $displayDatas;
		}
		
		
		
	}
?>