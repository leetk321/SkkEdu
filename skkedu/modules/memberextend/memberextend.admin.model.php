<?php
	
	/**
	 * @class memberextendAdminModel
	 * @author skullacy
	 * @brief memberextend Admin Model Class
	 */
	class memberextendAdminModel extends memberextend
	{
		function init()
		{
			
		}
		
		/**
		 * @brief 가입폼 추가.
		 */
		function getMemberextendAdminInsertJoinForm()
		{
			$member_join_form_srl = Context::Get('member_join_form_srl');
			
			$args->member_join_form_srl = $member_join_form_srl;
			$output = executeQuery('memberextend.getJoinForm', $args);
			
			$formInfo = $output->data;
			$default_value = $formInfo->default_value;
			if($output->toBool() && $output->data)
			{
				if($default_value)
				{
					$default_value = unserialize($default_value);
					Context::set('default_value', $default_value);
				}
				Context::set('formInfo', $output->data);
			}
			 
			$type_srl = Context::get('type_srl');
			Context::set('type_srl', $type_srl);
			
			$oTemplate = &TemplateHandler::getInstance();
			$tpl = $oTemplate->compile($this->module_path.'tpl', 'insert_join_form');
			
			$this->add('tpl', str_replace('\n', ' ', $tpl));
		}
	}
?>