<?php
	
	/**
	 * @class memberextendAdminView
	 * @author skullacy
	 * @brief memberextend Admin View Class
	 */
	class memberextendAdminView extends memberextend
	{
		
		var $memberextendConfig = null;
			
		function init()
		{
			$this->setTemplatePath($this->module_path.'tpl');
			
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$this->memberextendConfig = $oMemberextendModel->getMemberextendConfig();
			Context::set('config', $this->memberextendConfig);
			
			$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
			$group_list = $oMemberModel->getGroups();
			Context::set('group_list', $group_list);
			
			$type_list = $oMemberextendModel->getMemberTypes();
			Context::set('type_name', $type_list[Context::get('type_srl')]->title);
			
			
			$xe_version = (float)substr(__XE_VERSION__, 0, 3);
			if($xe_version < 1.6)
			{
				Context::addCSSFile($this->template_path.'css/xe1.5.css');
			}
			
		}
		
		function dispMemberextendAdminDashboard()
		{
			$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
			$memberextend_module_info = $oModuleModel->getModuleInfoXml('memberextend');
			$gathering_agreement_file = FileHandler::getRealPath(sprintf('%s%s.txt', './files/memberextend/', $memberextend_module_info->version));
			if(file_exists($gathering_agreement_file))
			{
				$agreement = FileHandler::readFile($gathering_agreement_file);
				Context::set('_memberextend_env_agreement', $agreement);
				if($agreement == 'Y')
				{
					$newest_news_url = sprintf("%s/index.php?module=skullacymanagement&act=procSkullacymanagementGathering&module_type=memberextend",$memberextend_module_info->author[0]->homepage);
					$_host_info = urlencode($_SERVER['HTTP_HOST']) . '--VER' . $memberextend_module_info->version . '--PHP' . phpversion() . '--XE' . __XE_VERSION__;
					$newest_news_url .= '&_host='.$_host_info;
					$cache_file = sprintf("%sfiles/cache/memberextend/memberextend.cache.php", _XE_PATH_);
					if(!file_exists($cache_file) || filemtime($cache_file)+ 60*60 < time())
					{
						FileHandler::writeFile($cache_file,'');
						FileHandler::getRemoteFile($newest_news_url, $cache_file, null, 1, 'GET', 'text/html', array('REQUESTURL'=>getFullUrl('')));
					}
					if(file_exists($cache_file)) 
					{
						$oXml = new XmlParser();
						$buff = $oXml->parse(FileHandler::readFile($cache_file));
						$item = $buff->response->noticeinfo->noticelist->item;
						if($item) {
							if(!is_array($item)) $item = array($item);
		
							foreach($item as $key => $val) {
								$obj = null;
								$obj->title = $val->title->body;
								$obj->date = $val->updatedate->body;
								$obj->url = $val->article_url->body;
								$news[] = $obj;
							}
							Context::set('news', $news);
						}
						Context::set('released_version', $buff->response->noticeinfo->released_version->body);
						Context::set('current_version', $memberextend_module_info->version);
						Context::set('more_link', $buff->response->noticeinfo->more_url->body);
					}
				}
			}
			
			
			
			$this->setTemplateFile('dashboard');
		}
		
		
		/**
		 * @brief 회원타입 리스트 출력.
		 */
		function dispMemberextendAdminTypeList()
		{
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$type_list = $oMemberextendModel->getMemberTypes();
			Context::set('type_list', $type_list);
			
			//1.5에서 1.7로 넘어가면서 회원설정 페이지 링크값이 바뀜. 
			//1.6이하는 Default type 수정 링크 바꾸기
			$xe_version = (float)substr(__XE_VERSION__, 0, 3);
			if($xe_version < 1.6)
			{
				Context::set('default_modify_link', getUrl('','module','admin','act','dispMemberAdminConfig'));
			}
			else 
			{
				Context::set('default_modify_link', getUrl('','module','admin','act','dispMemberAdminSignUpConfig'));
			}
			
			$this->setTemplateFile('type_list');
		}
		
		/**
		 * @brief 회원타입별 설정.
		 */
		function dispMemberextendAdminTypeConfig()
		{
			$config = $this->memberextendConfig;
			debugPrint($config);
			$oModuleModel = &getModel('module'); /*@var $oModuleModel moduleModel */
			
			$type_srl = Context::get('type_srl');
			$type_property = 'membertype_'.$type_srl;
			
			
			$config->target_group = $config->target_group->$type_property;
			
			$config->agreementConfig = $config->agreementConfig->$type_property;
			
			//2013.07.23 XE 1.5버전에서 리다이렉트 항목을 url로 표시
			if($config->redirect_url && $xe_version >= 1.6)
			{
				if(substr(Context::getDefaultUrl(), -1) != '/')
				{
					$makeRightUrl = Context::getDefaultUrl().'/';
				}
				else
				{
					$makeRightUrl = Context::getDefaultUrl();
				}
				
				$mid = str_ireplace($makeRightUrl, '', $config->redirect_url->$type_property);
				$siteModuleInfo = Context::get('site_module_info');
				$moduleInfo = $oModuleModel->getModuleInfoByMid($mid, (int)$siteModuleInfo->site_srl);
				
				$config->redirect_url = $moduleInfo->module_srl;
				Context::set('config', $config);
			}
			else if($xe_version < 1.6)
			{
				$config->redirect_url = $config->redirect_url->$type_property;
			}
			
			$signupForms = $this->memberextendConfig->signupForm->$type_property;
			
			Context::set('signupForms', $signupForms);
			
			$this->setTemplateFile('signup_config');
		}
		
		function dispMemberextendAdminAgreementConfig()
		{
			$type_srl = Context::get('type_srl');
			if(!$type_srl) return new Object(-1, 'msg_invalid_request');
			$type_property = 'membertype_'.$type_srl;
			
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			$memberextend_config = $oMemberextendModel->getMemberextendConfig();
			
			$agreementList = $memberextend_config->agreement->{$type_property}->list;
			Context::set('agreementList', $agreementList);
			
			$target_agreement->no = Context::get('agree_no');
			if($target_agreement->no)
			{
				$target_agreement->content = $agreementList[$target_agreement->no]->content;
				$target_agreement->title = $agreementList[$target_agreement->no]->title;
				Context::set('target_agreement', $target_agreement);
			}
			
			
			// 에디터 불러오기
			$oEditorModel = getModel('editor'); /* @var $oEditorModel editorModel */
			$option = new stdClass();
			
			$option->allow_fileupload = false;
			$option->content_style = 'default';
			$option->content_font = null;
			$option->content_font_size = null;
			$option->enable_autosave = false;
			$option->enable_default_component = true;
			$option->enable_component = true;
			$option->disable_html = false;
			$option->height = 200;
			$option->skin = 'xpresseditor';
			$option->colorset ='white';
			$option->primary_key_name = 'agreement_no'; 
			$option->content_key_name = 'agreement';
			$editor = $oEditorModel->getEditor(0, $option);
			
			Context::set('editor', $editor);
			
			$this->setTemplateFile('agreement_config');
		}
		
		function dispMemberextendAdminGeneralConfig()
		{
			$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
			
			$skin_list = $oModuleModel->getSkins($this->module_path);
			Context::set('skin_list', $skin_list);
			
			//2013.07.23 모바일스킨 코드 입력.
			$mskin_list = $oModuleModel->getSkins($this->module_path, 'm.skins');
			Context::set('mskin_list', $mskin_list);
			
			
			
			$exist_authentication = $oModuleModel->getModuleInfoXml('authentication');
			if(!$exist_authentication)
			{
				Context::set('check_phone_auth', false);
				Context::set('phone_auth_message', Context::getLang('msg_phone_auth_denied'));
			}
			else
			{
				Context::set('check_phone_auth', true);
				Context::set('phone_auth_message', Context::getLang('msg_phone_auth_approved'));
			}
			
			$oMemberModel = &getModel('member');
			$member_config = $oMemberModel->getMemberConfig();
			Context::set('member_config', $member_config);
			
			
			
			
			$this->setTemplateFile('general_config');
		}
		
		
		
		function _getMemberextendInputTag($type_srl, $memberInfo, $isAdmin = false)
		{
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
			
			$extend_form_list = $oMemberextendModel->getCombineJoinForm($type_srl, $memberInfo);
			$type_property = 'membertype_'.$type_srl;
			
			if ($memberInfo)
			{
				$memberInfo = get_object_vars($memberInfo);
			}
	
			$memberextend_config = $this->memberextendConfig;
			if(!$this->memberextendConfig)
			{
				$memberextend_config = $this->memberextendConfig = $oMemberextendModel->getMemberextendConfig();
			}
	
			$formTags = array();
			global $lang;
	
			foreach($memberextend_config->signupForm->$type_property as $no=>$formInfo)
			{
				if(!$formInfo->isUse)continue;
				$formTag = new stdClass();
				$inputTag = '';
				if($formInfo->required == 'Y') $formTag->title = '<em style="color:red">*</em> '.$formInfo->title;
				else $formTag->title = $formInfo->title;
				$formTag->name = $formInfo->name;
				$extendForm = $extend_form_list[$formInfo->member_join_form_srl];
				$replace = array('column_name' => $extendForm->column_name, 'value' => $extendForm->value);
				$extentionReplace = array();

				$formTag->type = $extendForm->column_type;
				if($extendForm->column_type == 'text')
				{
					$template = '<input type="text" name="%column_name%" id="%column_name%" value="%value%" />';
				}
				else if($extendForm->column_type == 'homepage')
				{
					$template = '<input type="url" name="%column_name%" id="%column_name%" value="%value%" />';
				}
				else if($extendForm->column_type == 'email_address')
				{
					$template = '<input type="email" name="%column_name%" id="%column_name%" value="%value%" />';
				}
				else if($extendForm->column_type == 'tel')
				{
					$extentionReplace = array('tel_0' => $extendForm->value[0],
						'tel_1' => $extendForm->value[1],
						'tel_2' => $extendForm->value[2]);
					$template = '<input type="tel" name="%column_name%[]" id="%column_name%" value="%tel_0%" size="4" maxlength="4" style="width:30px" title="First Number" /> - <input type="tel" name="%column_name%[]" value="%tel_1%" size="4" maxlength="4" style="width:30px" title="Second Number" /> - <input type="tel" name="%column_name%[]" value="%tel_2%" size="4" maxlength="4" style="width:30px" title="Third Number" />';
				}
				else if($extendForm->column_type == 'textarea')
				{
					$template = '<textarea name="%column_name%" id="%column_name%" rows="4" cols="42">%value%</textarea>';
				}
				else if($extendForm->column_type == 'checkbox')
				{
					$template = '';
					if($extendForm->default_value)
					{
						$template = '<div style="padding-top:5px">%s</div>';
						$__i = 0;
						$optionTag = array();
						foreach($extendForm->default_value as $v)
						{
							$checked = '';
							if(is_array($extendForm->value) && in_array($v, $extendForm->value))$checked = 'checked="checked"';
							$optionTag[] = '<label for="%column_name%'.$__i.'"><input type="checkbox" id="%column_name%'.$__i.'" name="%column_name%[]" value="'.htmlspecialchars($v).'" '.$checked.' /> '.$v.'</label>';
							$__i++;
						}
						$template = sprintf($template, implode('', $optionTag));
					}
				}
				else if($extendForm->column_type == 'radio')
				{
					$template = '';
					if($extendForm->default_value)
					{
						$template = '<div style="padding-top:5px">%s</div>';
						$optionTag = array();
						foreach($extendForm->default_value as $v)
						{
							if($extendForm->value == $v)$checked = 'checked="checked"';
							else $checked = '';
							$optionTag[] = '<label><input type="radio" name="%column_name%" value="'.$v.'" '.$checked.' /> '.$v.'</label>';
						}
						$template = sprintf($template, implode('', $optionTag));
					}
				}
				else if($extendForm->column_type == 'select')
				{
					$template = '<select name="'.$formInfo->name.'" id="'.$formInfo->name.'">%s</select>';
					$optionTag = array();
					if($extendForm->default_value)
					{
						foreach($extendForm->default_value as $v)
						{
							if($v == $extendForm->value) $selected = 'selected="selected"';
							else $selected = '';
							$optionTag[] = sprintf('<option value="%s" %s >%s</option>', $v, $selected, $v);
						}
					}
					$template = sprintf($template, implode('', $optionTag));
				}
				else if($extendForm->column_type == 'kr_zip')
				{
					Context::loadFile(array('./modules/member/tpl/js/krzip_search.js', 'body'), true);
					$extentionReplace = array(
						'msg_kr_address' => $lang->msg_kr_address,
						'msg_kr_address_etc' => $lang->msg_kr_address_etc,
						'cmd_search' => $lang->cmd_search,
						'cmd_search_again' => $lang->cmd_search_again,
						'addr_0' => $extendForm->value[0],
						'addr_1' => $extendForm->value[1],);
					$replace = array_merge($extentionReplace, $replace);
					$template = <<<EOD
							<div class="krZip" style="padding-top:5px">
								<div id="zone_address_search_%column_name%" style="margin-bottom:10px">
									<label for="krzip_address1_%column_name%">%msg_kr_address%</label>
									<span class="input-append">
										<input type="text" id="krzip_address1_%column_name%" value="%addr_0%" />
										<button type="button" class="btn">%cmd_search%</button>
									</span>
								</div>
								<div id="zone_address_list_%column_name%" hidden style="margin-bottom:10px">
									<select name="%column_name%[]" id="address_list_%column_name%"><option value="%addr_0%">%addr_0%</select>
									<button type="button">%cmd_search_again%</button>
								</div>
								<div class="address2" style="margin-bottom:10px">
									<label for="krzip_address2_%column_name%">%msg_kr_address_etc%</label>
									<input type="text" name="%column_name%[]" id="krzip_address2_%column_name%" value="%addr_1%" />
								</div>
							</div>
							<script>jQuery(function($){ $.krzip('%column_name%') });</script>
EOD;
				}
				else if($extendForm->column_type == 'jp_zip')
				{
					$template = '<input type="text" name="%column_name%" id="%column_name%" value="%value%" />';
				}
				else if($extendForm->column_type == 'date')
				{
					$extentionReplace = array('date' => zdate($extendForm->value, 'Y-m-d'), 'cmd_delete' => $lang->cmd_delete);
					$template = '<input type="hidden" name="%column_name%" id="date_%column_name%" value="%value%" /><input type="text" placeholder="YYYY-MM-DD" class="inputDate" value="%date%" readonly="readonly" /> <input type="button" value="%cmd_delete%" class="btn dateRemover" />';
				}
	
				$replace = array_merge($extentionReplace, $replace);
				$inputTag = preg_replace('@%(\w+)%@e', '$replace[$1]', $template);

				if($extendForm->description)
					$inputTag .= '<p class="help-block">'.htmlspecialchars($extendForm->description).'</p>';
					
				$formTag->inputTag = $inputTag;
				$formTags[] = $formTag;
			}
			return $formTags;
		}
		
		
	}
?>