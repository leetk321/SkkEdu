<?php
/**
     * @class  ViceManagerView
     * @author showjean	
     * @brief  ViceManager module view class
     **/

    class vicemanagerView extends vicemanager {

        /**
         * @brief 
         **/
        function init() {
        }
		
        /**
         * @brief modify
         **/
        function dispVicemanagerMemberModify() {
			$oModel = &getModel('vicemanager');
			
			$logged_info = Context::get('logged_info');
			if(!$oModel->isVicemanager($logged_info->member_srl) && $logged_info->is_admin != 'Y') return $this->stop('msg_not_permitted');

			global $lang;
			
			$member_srl = Context::get('member_srl');
			$oMemberModel = &getModel('member');	
			$oModuleModel = &getModel('module');
            $member_config = $oModuleModel->getModuleConfig('member');
			if(is_array($member_config->signupForm))
			{
				global $lang;
				foreach($member_config->signupForm AS $key=>$value)
				{
					if($lang->{$value->title})
						$member_config->signupForm[$key]->title = $lang->{$value->title};
				}
			}
            Context::set('member_config', $member_config);

            $member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
            unset($member_info->password);
            unset($member_info->email_id);
            unset($member_info->email_host);
			
			Context::set('member_info', get_object_vars($member_info));
			$extendForm = $oMemberModel->getCombineJoinForm($member_info);
            unset($extendForm->find_member_account);
            unset($extendForm->find_member_answer);
            Context::set('extend_form_list', $extendForm);

			$group_list = $oMemberModel->getGroups();

			$oPointModel = &getModel('point');
			$oModuleModel = &getModel('module');
			$point = $oPointModel->getPoint($member_srl);
			$pconfig = $oModuleModel->getModuleConfig('point');
			$level = $oPointModel->getLevel($point, $pconfig->level_step);

			$module_config = $oModuleModel->getModuleConfig('vicemanager');
			if(!$module_config->grant_types) $module_config->grant_types = array();
			foreach($lang->VM_grant_types as $key=>$val){				
				$temp_config->{'grant_'.$key} = $logged_info->is_admin == 'Y' ? 1 : in_array($key, $module_config->grant_types);
			}

            // Set values of member_model::getMemberList() objects for a template
			//Context::set('member_info', $member_info);
			Context::set('group_list', $group_list);
			Context::set('point', $point);
			Context::set('level', $level);
			Context::set('page', Context::get('page'));
            Context::set('search_target', Context::get('search_target'));
            Context::set('search_keyword', Context::get('search_keyword'));
			Context::set('module_config', $temp_config);

            // Specify a template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('member_modify');
		}
		
        /**
         * @brief Display a list
         **/
        function dispVicemanagerMemberList() {		
			$oModel = &getModel('vicemanager');
			
			$logged_info = Context::get('logged_info');
			if(!$oModel->isVicemanager($logged_info->member_srl) && $logged_info->is_admin != 'Y') return $this->stop('msg_not_permitted');

			global $lang;

            // get a list
			$oMemberAdminModel = &getAdminModel('member');
			$output = $oMemberAdminModel->getMemberList();

			
			$oMemberModel = &getModel('member');			
			$config = $oMemberModel->getMemberConfig();			
			$memberIdentifiers = array('user_id'=>'user_id', 'user_name'=>'user_name', 'nick_name'=>'nick_name');			
			$usedIdentifiers = array();			

			if (is_array($config->signupForm)){
				foreach($config->signupForm as $signupItem){				
					if (!count($memberIdentifiers)) break;				
					if(in_array($signupItem->name, $memberIdentifiers) && ($signupItem->required || $signupItem->isUse)){					
						unset($memberIdentifiers[$signupItem->name]);					
						$usedIdentifiers[$signupItem->name] = $lang->{$signupItem->name};				
					}			
				}
			}
            // Set values of member_model::getMemberList() objects for a template
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('member_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);
			Context::set('usedIdentifiers', $usedIdentifiers);
			Context::set('identifier', $config->identifier);
            Context::set('search_target', Context::get('search_target'));
            Context::set('search_keyword', Context::get('search_keyword'));			

            // Specify a template
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('member_list');
        }
    }
?>
