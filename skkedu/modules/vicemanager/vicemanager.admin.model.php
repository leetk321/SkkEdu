<?php
    /**
     * @class  vicemanagerAdminModel
     * @author showjean	
     * @brief  vicemanager module의 admin mdoel class
     **/

    class vicemanagerAdminModel extends vicemanager {

        /**
         * @brief 초기화
         **/
        function init() {
        }

		 /**
         * @brief 관리화면을 위한 변수 설정
         **/
        function getConfig() {
			
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('vicemanager');
			if(!$config->grant_types) $config->grant_types = array();
			if(!$config->use_menu) $config->use_menu = false;
			if(!$config->vicemanager_srls) $config->vicemanager_srls = "";
			if(!$config->permit_module_admin) $config->permit_module_admin = false;

			return $config;
        }
       
    }
?>