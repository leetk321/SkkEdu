<?php
    /**
     * @class  ximember
     * @author Xiso (xiso@xiso.co.kr)
     * @brief  Xiso Member module
     **/
	
	require_once(_XE_PATH_.'modules/member/member.admin.controller.php');
    class ximemberAdminController extends memberAdminController {
		/**
         * @brief initialization
         **/
        function init() {
        }
		
		function procXimemberAdminConfig(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			$_config = Context::getRequestVars();
			unset($_config->error_return_url,$_config->ruleset,$_config->module,$_config->act,$_config->success_return_url);
			
			//change option
			foreach($_config as $key => $option) $config->{$key} = $option;
			
			//setDefault
			if($_config->use_mobile != "Y") $config->use_mobile = "N";
			if(!$_config->xi_change_pages) $config->xi_change_pages = array();
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminConfig');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminUseModule(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			$config->xe_run_method = Context::get('xe_run_method');
			$config->mid_list = Context::get('mid_list');
				
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminUseModule');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminInsertAgree(){
			if(!Context::get('new_agreement')) return new Object(-1, "msg_not_new_agreement");
			
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			if(!$config->agreements) $config->agreements = array();
			//fixed 2014.08.07
			if($config->agreements[0]) unset($config->agreements[0]);
			if(!$config->agreements['default']){
				global $lang;
				$config->agreements['default']->srl = 'default';
				$config->agreements['default']->name = $lang->agreement;
				ksort($config->agreements);
			}
			$agreements_key = getNextSequence();
			
			global $lang;
			$agreement_obj = new stdClass;
			$agreement_obj->srl = $agreements_key;
			$agreement_obj->name = Context::get('new_agreement');
			$agreement_obj->is_use = "N";
			$agreement_obj->required = "N";
			$agreement_obj->content = $lang->not_agreement_content;
			
			$config->agreements[$agreements_key] = $agreement_obj;
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminAgree');
			$this->setRedirectUrl($returnUrl);
			
		}
		
		function procXimemberAdminAgreeConfig(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			if(!$config->agreements) $config->agreements = array();
			//fixed 2014.08.07
			if($config->agreements[0]) unset($config->agreements[0]);
			if(!$config->agreements['default']){
				global $lang;
				$config->agreements['default']->srl = 'default';
				$config->agreements['default']->name = $lang->agreement;
				ksort($config->agreements);
			}
			
			foreach($config->agreements as $srl => $agreement){
				$config->agreements[$srl]->is_use = Context::get('is_use_'.$srl) ? Context::get('is_use_'.$srl) : "N";
				$config->agreements[$srl]->required = Context::get('required_'.$srl) ? Context::get('required_'.$srl) : "N";
			}
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminAgree');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminEditAgree(){
			$oModuleController = &getController('module');
			$args->agreement = Context::get('agreement');
			$srl = Context::get('agree_srl');
			if($srl == 'default'){
				if(!trim(strip_tags($args->agreement)))
				{
					$agreement_file = _XE_PATH_.'files/member_extra_info/agreement_' . Context::get('lang_type') . '.txt';
					FileHandler::removeFile($agreement_file);
					$args->agreement = NULL;
				}
				// check agreement value exist
				if($args->agreement)
				{
					$agreement_file = _XE_PATH_.'files/member_extra_info/agreement_' . Context::get('lang_type') . '.txt';
					$output = FileHandler::writeFile($agreement_file, $args->agreement);

					unset($args->agreement);
				}
				$output = $oModuleController->updateModuleConfig('member', $args);
				if(!$output->toBool()) return $output;
			}else{
				//get SiteInfo
				$site_module_info = Context::get('site_module_info');
				$site_srl = $site_module_info->site_srl;
				
				//get Config
				$oModuleModel = &getModel('module');
				$config = $oModuleModel->getModuleConfig('ximember');
				if(!$config) $config = new stdClass;
				
				if(!$config->agreements) $config->agreements = array();
				//fixed 2014.08.07
				if($config->agreements[0]) unset($config->agreements[0]);
				if(!$config->agreements['default']){
					global $lang;
					$config->agreements['default']->srl = 'default';
					$config->agreements['default']->name = $lang->agreement;
					ksort($config->agreements);
				}
				
				global $lang;
				if(!trim(strip_tags($args->agreement))) $config->agreements[$srl]->content = $lang->not_agreement_content;
				else $config->agreements[$srl]->content = $args->agreement;
				
				//set Config
				$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
				if(!$output->toBool()) return $output;
			}
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminAgree');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminDeleteAgree(){
			if(!Context::get('agree_srl')) return new Object(-1, "msg_not_agreement");
			
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			if(!$config->agreements) $config->agreements = array();
			
			//delete Agreement
			unset($config->agreements[Context::get('agree_srl')]);
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminAgree');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminLimitConfig(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			$_config = Context::getRequestVars();
			unset($_config->error_return_url,$_config->ruleset,$_config->module,$_config->act,$_config->success_return_url);
			
			//change Object To Array
			foreach($_config as $key => $option) $limit[$key] = $option;
			
			//setData
			$config->limit = $limit;
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminLimit');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminGroupAdd(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			$_config = Context::getRequestVars();
			unset($_config->error_return_url,$_config->ruleset,$_config->module,$_config->act,$_config->success_return_url);
			
			if(!$_config->group_srl) return new Object(-1,"msg_not_founded");
			
			//set Data
			$added_group->group_srl = $_config->group_srl;
			if($_config->limit_type){
				$added_group->fullage = $_config->fullage;
				$added_group->limit_type = $_config->limit_type;
				$is_conditon = true;
			}
			if($_config->sex_type){
				$added_group->sex_type = $_config->sex_type;
				$is_conditon = true;
			}
			if(!$is_conditon) return new Object(-1, "msg_not_condition");
			if(!$config->groups) $config->groups = array();
			$config->groups[] = $added_group;
			ksort($config->groups);
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminGroup');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminGroupDelete(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			if(!$config->groups[Context::get('group_key')]) return new Object(-1, "msg_not_founded");
			
			//delete Group
			unset($config->groups[Context::get('group_key')]);
			//reset condition key
			foreach($config->groups as $val) $_groups[] = $val;
			$config->groups = $_groups;
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminGroup');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminExtra(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			//setData
			$extra_vars = array();
			
			//save data only use_list
			$use_list = Context::get('use_extravars');
			if(is_array($use_list)){
				foreach($use_list as $key => $val){
					$extra_vars[$val]->use = "Y";
					$extra_vars[$val]->target = Context::get('target_'.$val);
					$extra_vars[$val]->typeA = Context::get('typeA_'.$val);
					$extra_vars[$val]->typeB = Context::get('typeB_'.$val);
					$extra_vars[$val]->signup = Context::get('signup_'.$val);
					$extra_vars[$val]->modify = Context::get('modify_'.$val);
				}
			}			
			$config->extra_vars = $extra_vars;
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminExtra');
			$this->setRedirectUrl($returnUrl);
		}
		
		function procXimemberAdminKcb(){
			//get SiteInfo
			$site_module_info = Context::get('site_module_info');
			$site_srl = $site_module_info->site_srl;
			
			//get Config
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$config) $config = new stdClass;
			
			//setData
			$args = Context::gets('kcb_domain','kcb_shopid','module_file','del_module','use_auth');
			$kcb = new stdClass;
			$kcb->kcb_domain = $args->kcb_domain;
			$kcb->kcb_shopid = $args->kcb_shopid;
			if(is_array($args->use_auth)){
				foreach($args->use_auth as $val) $kcb->use_auth[$val] = true;
			}
			
			$module_file = $args->module_file;
			if($args->del_module == "Y"){
				FileHandler::removeDir("./files/ximember/module/");
				unset($kcb->module_file,$kcb->module_path);
			}else if($module_file['tmp_name'] && is_uploaded_file($module_file['tmp_name'])){
				FileHandler::removeDir("./files/ximember/module/");
				unset($kcb->module_file,$kcb->module_path);
				
				$path = sprintf("./files/ximember/module/%s",getNumberingPath(getNextSequence()));
				if(!FileHandler::makeDir($path)) return new Object(-1, 'msg_error');
				
				$filename = $path.time()."_".$module_file['name'];
				if(!move_uploaded_file($module_file['tmp_name'], $filename)) return new Object(-1, 'msg_rror');
				chmod($path, 0777);
				chmod($filename, 0755);
				$kcb->module_path = $path;
				$kcb->module_file = $filename;
			}else{
				$kcb->module_path = $config->kcb->module_path;
				$kcb->module_file = $config->kcb->module_file;
			}
			$config->kcb = $kcb;
			
			//set Config
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig("ximember",$config,site_srl);
			
			if(!$output->toBool()) return $output;
			
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispXimemberAdminExtra');
			$this->setRedirectUrl($returnUrl);
		}
    }
?>
