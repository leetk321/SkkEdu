<?php
	/**
	 * @class  sejin7940_commentView
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_comment 모듈의 View class
	 **/

	class sejin7940_commentView extends sejin7940_comment {

		/**
		 * @brief Initialization
		 **/
		function init() {

            // Get teh configuration information
            $oModuleModel = &getModel('module');
            $this->config = $config = $oModuleModel->getModuleConfig('sejin7940_comment');
            // Set the configuration variable
            Context::set('module_config', $config);		

			$template_path = sprintf("%sskins/%s/",$this->module_path, $config->skin);
            if(!is_dir($template_path)||!$config->skin) {
                $config->skin = 'default';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $config->skin);
            }
            $this->setTemplatePath($template_path);			

			$oLayoutModel = &getModel('layout');
			$layout_info = $oLayoutModel->getLayout($config->layout_srl);
			if($layout_info)
			{
				$this->module_info->layout_srl = $config->layout_srl;
				$this->setLayoutPath($layout_info->path);
			}
		}

        /**
         * @brief Display documents written by the member
         **/
        function dispSejin7940_commentOwnComment() {

            $logged_info = Context::get('logged_info');
			if(!$logged_info) return $this->stop('msg_not_logged');

//			if(Context::get('annoymous')=='Y') $member_srl = (-1)*$logged_info->member_srl;
//          else $member_srl = $logged_info->member_srl;

			$member_srl = $logged_info->member_srl;

//            $module_srl = Context::get('module_srl');
			
			if($this->config->apply_module) {
				if(Context::get('selected_module_srl')) {
					$apply_module_array = explode(',',$this->config->apply_module);
					for($a=0;$a<count($apply_module_array);$a++) {
						if($apply_module_array[$a]!=Context::get('selected_module_srl')) {
							$new_apply_module[] = $apply_module_array[$a];
						}
					}
					$apply_module = implode(',',$new_apply_module);
				}
				else {
					Context::set('module_srl',$this->config->apply_module);
				}
			}
			elseif(Context::get('selected_module_srl')) {
				Context::set('module_srl',Context::get('selected_module_srl'));
			}

            Context::set('search_target','member_srl');
            Context::set('search_keyword',$member_srl);


			// 출력 수 등을 조절하기 위해 CommentAdmin 에서 끌어오던걸 그냥 바로 정의함
            $args->page = Context::get('page'); // /< Page

			$oSejin7940_commentModel = &getModel('sejin7940_comment');
			$output = $oSejin7940_commentModel->getCommnetList($member_srl,$args->page);

			$oCommentModel = &getModel("comment");
			$modules = $oCommentModel->getDistinctModules();

			$modules_list = $modules;

            // set values in the return object of comment_model:: getTotalCommentList() in order to use a template.
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('comment_list', $output->data);
            Context::set('modules_list', $modules_list);
            Context::set('page_navigation', $output->page_navigation);
            Context::set('secret_name_list', $secretNameList);

            Context::set('module_srl', $module_srl);
            $this->setTemplateFile('comment_list');
        }



	}
?>