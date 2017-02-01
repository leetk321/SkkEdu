<?php
    /**
     * @class  ximember
     * @author Xiso (xiso@xiso.co.kr)
     * @brief  Xiso Member module
     **/
	
	require_once(_XE_PATH_.'modules/member/member.admin.view.php');
    class ximemberAdminView extends memberAdminView {
		/**
         * @brief initialization
         **/
        function init() {
        }
		
		function dispXimemberAdminConfig(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
			//get Skins
            $oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);
			
			// list of skins for member module
			$mskin_list = $oModuleModel->getSkins($this->module_path, 'm.skins');
			Context::set('mskin_list', $mskin_list);
			
			// get the layouts path
			$oLayoutModel = getModel('layout');
			$layout_list = $oLayoutModel->getLayoutList();
			Context::set('layout_list', $layout_list);

			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('mlayout_list', $mobile_layout_list);
		
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('index');
		}
		
		function dispXimemberAdminUseModule(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
			$site_module_info = Context::get('site_module_info');
			
			// Get a mid list
			$oModuleModel = getModel('module');
			
			$args = new stdClass();
			if($site_module_info->site_srl)
			{
				$args->site_srl = $site_module_info->site_srl;
			}
			
			$columnList = array('module_srl', 'module_category_srl', 'mid', 'browser_title');
			$mid_list = $oModuleModel->getMidList($args, $columnList);
			// module_category and module combination
			if(!$site_module_info->site_srl)
			{
				// Get a list of module categories
				$module_categories = $oModuleModel->getModuleCategories();

				if(is_array($mid_list))
				{
					foreach($mid_list as $module_srl => $module)
					{
						$module_categories[$module->module_category_srl]->list[$module_srl] = $module;
					}
				}
			}
			else
			{
				$module_categories = array();
				$module_categories[0] = new stdClass();
				$module_categories[0]->list = $mid_list;
			}
			
			Context::set('mid_list', $module_categories);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('use_module');
		}
		
		function dispXimemberAdminAgree(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('agreement');
		}
		
		function dispXimemberAdminEditAgree(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
			//getDefault Agreement Content
			if(Context::get('agree_srl') == 'default'){
				$oMemberModel = getModel('member');
				$memberConfig = $oMemberModel->getMemberConfig();
				$config->agreements["default"]->content = $memberConfig->agreement;
				Context::set('agreement',$config->agreements["default"]);
			}else if(!$config->agreements[Context::get('agree_srl')]){
				return new Object(-1,"msg_not_agreement");
			}else{
				Context::set('agreement',$config->agreements[Context::get('agree_srl')]);
			}
			// retrieve skins of editor
			$oEditorModel = getModel('editor');
			Context::set('editor_skin_list', $oEditorModel->getEditorSkinList());

			// get an editor
			$option = new stdClass();
			$option->primary_key_name = 'temp_srl';
			$option->content_key_name = 'agreement';
			$option->allow_fileupload = false;
			$option->enable_autosave = false;
			$option->enable_default_component = true;
			$option->enable_component = true;
			$option->resizable = true;
			$option->height = 300;
			$editor = $oEditorModel->getEditor(0, $option);
			Context::set('editor', $editor);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('agreement_edit');
		}
		
		function dispXimemberAdminLimit(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('limit');
		}
		
		function dispXimemberAdminGroup(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
			//set Group List
			$oMemberModel = &getModel('member');
			$group_list = $oMemberModel->getGroups();
			Context::set('group_list', $group_list);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('group');
		}
		
		function dispXimemberAdminExtra(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
			//get MemberConfig
			$memberConfig = $oModuleModel->getModuleConfig('member');
			
			//Variables Fix
			foreach($memberConfig->signupForm as $key => $item){
				if($item->name == 'user_name' || $item->name == 'birthday' || $item->name == 'nick_name'){
					unset($memberConfig->signupForm[$key]->isDefaultForm);
					unset($memberConfig->signupForm[$key]->isIdentifier);
				}
				if($item->name == 'nick_name'){
					$memberConfig->signupForm[$key]->requiredSignup = true;
				}
			}
			Context::set('memberConfig',$memberConfig);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('extra_vars');
		}		
		
		function dispXimemberAdminKcb(){
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			Context::set('config',$config);
			
            //set Template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('kcb');
		}
    }
?>
