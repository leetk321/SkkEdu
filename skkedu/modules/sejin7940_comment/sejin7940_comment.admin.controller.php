<?php
	/**
	 * @class  sejin7940_commentAdminController
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_comment 모듈의 AdminController class
	 **/

	class sejin7940_commentAdminController extends sejin7940_comment {

		/**
		 * @brief Initialization
		 **/
		function init() {
			
		}

		function procSejin7940_commentAdminConfig() {
            $args = Context::getRequestVars();

			$args->comment_cut_size = Context::get('comment_cut_size');
			$args->title_cut_size = Context::get('title_cut_size');
			$args->annoymous_use = Context::get('annoymous_use');

			$args->apply_module = Context::get('apply_module');		

			// 가져온 옵션값들을 module config에 저장한다.
			$oModuleController = &getController('module');
			$oModuleController->insertModuleConfig('sejin7940_comment',$args);
			

			if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispSejin7940_memberAdminConfig');
				header('location:'.$returnUrl);
				return;
			}
			else return $output;
		}


	}
?>