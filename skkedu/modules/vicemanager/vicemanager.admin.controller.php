<?php
    /**
     * @class  vicemanagerAdminController
     * @author showjean
     * @brief  vicemanager module의 admin controller class
     **/

    class vicemanagerAdminController extends vicemanager {

        /**
         * @brief 
         **/
        function init() {
        }
        /**
         * @brief vicemanager 
         **/
        function procVicemanagerAdminInsertConfig() {
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');
			$oAdminModel = &getAdminModel('vicemanager');
			$config = $oAdminModel->getConfig();
			
			$grant_type = Context::get('grant_types');
            if(!is_array($grant_type)) $grant_types = explode('|@|', $grant_type);
			else $grant_types = $grant_type;
			$config->grant_types = $grant_types;

			$use_menu = Context::get('use_menu');
			$config->use_menu = $use_menu == "N";
			$vicemanager_srls = Context::get('vicemanager_srls');
			$config->vicemanager_srls = $vicemanager_srls;
			$permit_module_admin = Context::get('permit_module_admin');
			$config->permit_module_admin = $permit_module_admin == "Y";

			$trigger = array('moduleHandler.init', 'vicemanager', 'controller', 'triggerAfterModuleHandlerInit', 'after');

			// 
			if($config->use_menu){
				if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
				{
					$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
				}
			}else{
				if($oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
				{
					$oModuleController->deleteTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
				}
			}

            // module Controller 
			$output = $oModuleController->insertModuleConfig('vicemanager',$config);

			if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispVicemanagerAdminConfig');
				header('location:'.$returnUrl);
				return;
			}
        }

    }
