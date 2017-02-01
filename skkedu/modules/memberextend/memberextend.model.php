<?php
	
	/**
	 * @class memberextendModel
	 * @author skullacy
	 * @brief memberextend Model Class
	 */
	class memberextendModel extends memberextend
	{
		var $join_form_list = null;
		
		
		function init()
		{
			
		}
		
		/**
		 * @brief Memberextend 회원정보 가져오기
		 */
		function getMemberextendInfo($member_srl)
		{
			$args = new stdClass();
			$args->member_srl = $member_srl;
			
			$output = executeQuery('memberextend.getMemberextendInfo', $args);
			if(!$output->toBool() || !$output->data)
			{
				return;
			}
			
			$result = new stdClass();
			$result = unserialize($output->data->type_extra_vars);
			$result->member_srl = $output->data->member_srl;
			$result->type_srl = $output->data->type_srl;
			 
			return $result;
		}
		
		/**
		 * @brief 회원타입 리스트 가져오기
		 * @param $filter_type = active, all
		 */
		function getMemberTypes($filter_type = 'all')
		{
			$args->sort_index = 'list_order';
			$args->order_type = 'asc';
			$output = executeQueryArray('memberextend.getMemberTypes', $args);
			if(!$output->toBool() || !$output->data)
			{
				return array();
			}

			$type_list = $output->data;

			foreach($type_list as $val) 
			{
				if($filter_type == 'active' && $val->is_active == 'N') continue;
				$result[$val->type_srl] = $val;
			}
			
			return $result;
		}
		
		/**
		 * @brief 회원타입 가져오기
		 * @param $type_srl
		 */
		function getMemberType($type_srl)
		{
			$args->type_srl = $type_srl;
			$output = executeQuery('memberextend.getMemberType', $args);
			if(!$output->toBool() || !$output->data)
			{
				return array();
			}
			
			return $output->data;
		}
		
		/**
		 * @brief 모듈 정보 가져오기, 코드 미완성.
		 * @return unknown
		 */
		function getMemberextendConfig($type_srl = null)
		{
			$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
			$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */

			$config = $oModuleModel->getModuleConfig('memberextend');
			
			$type_list = $oMemberextendModel->getMemberTypes();
			
			//약관 텍스트파일 로딩
			foreach($type_list as $key=>$val)
			{
				if($key != 1)
				{
					$type_property = 'membertype_'.$key;
					$agreement_count = $config->agreementConfig->{$type_property}->count;
					if($agreement_count)
					{
						for($i = 1; $i <= $agreement_count; $i++)
						{
							$agreement_file = _XE_PATH_.'files/member_extra_info/agreement_'.$key.'_'.Context::get('lang_type').'_'.$i.'.txt';
							if(is_readable($agreement_file))
							{
								$config->agreement->$type_property->list[$i]->content = FileHandler::readFile($agreement_file); 
							}
							
							$agreement_title_file = _XE_PATH_.'files/member_extra_info/agreement_title_'.$key.'_'.Context::get('lang_type').'_'.$i.'.txt';
							if(is_readable($agreement_title_file))
							{
								$config->agreement->{$type_property}->list[$i]->title = FileHandler::readFile($agreement_title_file); 
							}
						}
					}
					$config->agreementConfig->{$type_property}->count = count($config->agreement->{$type_property}->list);
					$config->agreementConfig->{$type_property}->isUse = $config->agreementConfig->{$type_property}->isUse ? $config->agreementConfig->{$type_property}->isUse : 'N';
					$config->agreementConfig->{$type_property}->defaultUse = $config->agreementConfig->{$type_property}->defaultUse ? $config->agreementConfig->{$type_property}->defaultUse : 'N' ;
				} 
				else 
				{
					continue;
				}  
			}
			
			
			//설정이 존재하지 않을시 디폴트설정
			if(!$config->skin) $config->skin = 'default';
			if(!$config->colorset) $config->colorset = 'white';
			if(!$config->progressUse) $config->progressUse = 'Y';
			if(!$config->completePageUse) $config->completePageUse = 'Y';
			if(!$config->viewMemberTypeUse) $config->viewMemberTypeUse = 'Y';
			$config->ipinAuthUse = 'N';
			$config->idnumberAuthUse = 'N';
			if(!$config->phoneAuthUse) $config->phoneAuthUse = 'N';
			
			//현재는 인증방식이 한가지뿐이므로 핸드폰인증 사용안함 설정시, 인증자체를 사용안함으로 설정
			if($config->phoneAuthUse == 'N') $config_args->authenticationUse = 'N';
			
			
			//핸드폰인증 미사용으로 해당모듈 처리.
			$exist_authentication = $oModuleModel->getModuleInfoXml('authentication');
			if($exist_authentication)
			{
				if($config->phoneAuthUse == 'N')
				{
					$oAuthenticationModel = &getModel('authentication');
					$config_a = $oAuthenticationModel->getModuleConfig();
					$config_a->list = NULL;
					
					$oModuleController = getController('module');
					$output_a = $oModuleController->updateModuleConfig('authentication', $config_a);
				}
			}
			
			
			//예외처리
			if($config->agreementConfig->membertype_) unset($config->agreementConfig->membertype_);
			
			if(!$type_srl) return $config;
			else 
			{
				$type_property = 'membertype_'.$type_srl;
				foreach($config as $key=>$val)
				{
					if($val->$type_property) $config->$key = $val->$type_property;
					else if($val->$type_property == null) $config->$key = NULL;
				}
				return $config;
			}
			
		}
		
		function getUsedJoinFormList($type_srl)
		{
			$args = new stdClass();
			$args->sort_index = "list_order";
			$args->type_srl = $type_srl;
			$output = executeQueryArray('memberextend.getJoinFormList', $args);
	
			if(!$output->toBool())
			{
				return array();
			}
	
			$joinFormList = array();
			foreach($output->data as $val)
			{
				if($val->is_active != 'Y')
				{
					continue;
				}
	
				$joinFormList[] = $val;
			}
	
			return $joinFormList;
		}

		function getJoinFormList($type_srl, $filter_response = false)
		{
			global $lang;
			// Set to ignore if a super administrator.
			$logged_info = Context::get('logged_info');
			if(!$this->join_form_list)
			{
				// Argument setting to sort list_order column
				$args = new stdClass();
				$args->sort_index = "list_order";
				$args->type_srl = $type_srl;
				$output = executeQuery('memberextend.getJoinFormList', $args);
				// NULL if output data deosn't exist
				$join_form_list = $output->data;
					
				if(!$join_form_list) return NULL;
				// Need to unserialize because serialized array is inserted into DB in case of default_value
				if(!is_array($join_form_list)) $join_form_list = array($join_form_list);
				$join_form_count = count($join_form_list);
					
				for($i=0;$i<$join_form_count;$i++)
				{
					$join_form_list[$i]->column_name = strtolower($join_form_list[$i]->column_name);
			
					$member_join_form_srl = $join_form_list[$i]->member_join_form_srl;
					$column_type = $join_form_list[$i]->column_type;
					$column_name = $join_form_list[$i]->column_name;
					$column_title = $join_form_list[$i]->column_title;
					$default_value = $join_form_list[$i]->default_value;
					// Add language variable
					$lang->extend_vars[$column_name] = $column_title;
					// unserialize if the data type if checkbox, select and so on
					if(in_array($column_type, array('checkbox','select','radio')))
					{
						$join_form_list[$i]->default_value = unserialize($default_value);
						if(!$join_form_list[$i]->default_value[0]) $join_form_list[$i]->default_value = '';
					}
					else
					{
						$join_form_list[$i]->default_value = '';
					}
			
					$list[$member_join_form_srl] = $join_form_list[$i];
				}
				$this->join_form_list = $list;
			}
			// Get object style if the filter_response is true
			if($filter_response && count($this->join_form_list))
			{
				foreach($this->join_form_list as $key => $val)
				{
					if($val->is_active != 'Y') continue;
					unset($obj);
					$obj->type = $val->column_type;
					$obj->name = $val->column_name;
					$obj->lang = $val->column_title;
					if($logged_info->is_admin != 'Y') $obj->required = $val->required=='Y'?true:false;
					else $obj->required = false;
					$filter_output[] = $obj;
		
					unset($open_obj);
					$open_obj->name = 'open_'.$val->column_name;
					$open_obj->required = false;
					$filter_output[] = $open_obj;
		
				}
				return $filter_output;
			}
			// Return the result
			return $this->join_form_list;
		}
		
		function getCombineJoinForm($type_srl, $member_info)
		{
			$extend_form_list = $this->getJoinFormlist($type_srl);
			if(!$extend_form_list) return;
			// Member info is open only to an administrator and him/herself when is_private is true.
			$logged_info = Context::get('logged_info');
	
			foreach($extend_form_list as $srl => $item)
			{
				$column_name = $item->column_name;
				$value = $member_info->{$column_name};
	
				// Change values depening on the type of extend form
				switch($item->column_type)
				{
					case 'checkbox' :
						if($value && !is_array($value)) $value = array($value);
						break;
					case 'text' :
					case 'homepage' :
					case 'email_address' :
					case 'tel' :
					case 'textarea' :
					case 'select' :
					case 'kr_zip' :
						break;
				}
	
				$extend_form_list[$srl]->value = $value;
	
				if($member_info->{'open_'.$column_name}=='Y') $extend_form_list[$srl]->is_opened = true;
				else $extend_form_list[$srl]->is_opened = false;
			}
			return $extend_form_list;
		}
		
	}
?>