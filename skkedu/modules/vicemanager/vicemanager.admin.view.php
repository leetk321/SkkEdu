<?php
    /**
     * @class  vicemanagerAdminView
     * @author showjean	
     * @brief  vicemanager module의 admin view class
     **/

    class vicemanagerAdminView extends vicemanager {

        /**
         * @brief 초기화
         **/
        function init() {
        }

		 /**
         * @brief 관리화면을 위한 변수 설정
         **/
        function dispVicemanagerAdminConfig() {
			
			$oAdminModel = &getAdminModel('vicemanager');
			$config = $oAdminModel->getConfig();

            Context::set('vicemanager_config', $config);
			
            // template 지정
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('config');
        }
       
    }
?>