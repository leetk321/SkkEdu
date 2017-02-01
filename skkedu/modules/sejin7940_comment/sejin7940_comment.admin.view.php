<?php
	/**
	 * @class  sejin7940_commentAdminView
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_comment 모듈의 AdminView class
	 **/

	class sejin7940_commentAdminView extends sejin7940_comment {

		/**
		 * @brief Initialization
		 **/
		function init() {
            // Get teh configuration information
            $oModuleModel = &getModel('module');
            $config = $oModuleModel->getModuleConfig('sejin7940_comment');
            // Set the configuration variable
            Context::set('config', $config);				

			Context::set('apply_module',$config->apply_module);
			// 템플릿 폴더 지정
			$this->setTemplatePath($this->module_path.'tpl');	
		}


		// sejin7940_comment 모듈 통합 관리자 페이지
		function dispSejin7940_commentAdminConfig() {
            $oModuleModel = &getModel('module');
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

			$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
			Context::set('mskin_list', $mskin_list);

            // get the layouts path
            $oLayoutModel = &getModel('layout');
            $layout_list = $oLayoutModel->getLayoutList();
            Context::set('layout_list', $layout_list);

			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('mlayout_list', $mobile_layout_list);


			// 템플릿 세팅
			$this->setTemplatePath($this->module_path.'tpl');
			$this->setTemplateFile('config.html');
		}

	}
?>