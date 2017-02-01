<?php
    /**
     * @class  ViceManagerController
     * @author showjean
     * @brief  ViceManager module controller class 
     **/

    class vicemanagerController extends vicemanager {

        /**
         * @brief **/
        function init() {
        }

		/**
         * @brief 
         **/
		function triggerAfterModuleHandlerInit(&$obj) {
			$oModel = &getModel('vicemanager');
			
			$logged_info = Context::get('logged_info');
				
			global $lang;
			if($oModel->isVicemanager($logged_info->member_srl) || $logged_info->is_admin == 'Y'){
			
				$oMemberController = &getController('member');
				$oMemberController->addMemberMenu( 'dispVicemanagerMemberList', $lang->VM_menu_title);	// 
			}
		}
       
		/**
         * @brief Edit member profile
         **/
        function procVicemanagerMemberModify() {
			$oModel = &getModel('vicemanager');
			
			$logged_info = Context::get('logged_info');
			if(!$oModel->isVicemanager($logged_info->member_srl) && $logged_info->is_admin != 'Y') return $this->stop('msg_not_permitted');

			$args = Context::gets('member_srl','denied','group_srl_list','limit_date','point');

			$oMemberModel = &getModel('member');	
			$member_info = $oMemberModel->getMemberInfoByMemberSrl($args->member_srl);
			if($logged_info->is_admin != 'Y' && ($oModel->isVicemanager($member_info->member_srl) || $member_info->is_admin == 'Y')) return $this->stop('msg_not_permitted');

			$date = date("YmdHis");
			if(strlen(preg_replace("/[^0-9]/","", $args->limit_date)) != 8 && $args->limit_date){
				return $this->stop($lang->msg_invalid_format);
			}

			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('vicemanager');

            $oDB = &DB::getInstance();
            $oDB->begin();
            // DB in the update

			$obj;

			if(in_array('denied', $config->grant_types) || $logged_info->is_admin == 'Y') $obj->denied = $args->denied == 'on' ? 'Y':'N';
			if(in_array('limit_date', $config->grant_types) || $logged_info->is_admin == 'Y') $obj->limit_date = $args->limit_date ? $args->limit_date:'';
			if(isset($obj->denied) || isset($obj->limit_date)){
				$obj->member_srl = $args->member_srl;
				$output = executeQuery('vicemanager.updateMemberInfo', $obj);
				if(!$output->toBool()) {
					return $this->stop($output->getMessage());
				}
			}

			if(in_array('group', $config->grant_types) || $logged_info->is_admin == 'Y'){
				$oMemberController = &getController('member');
				if ($args->group_srl_list){
					if(is_array($args->group_srl_list)) $group_srl_list = $args->group_srl_list;
					else $group_srl_list = explode('|@|', $args->group_srl_list);
					// If the group information, group information changes
					if(count($group_srl_list) > 0) {
						$args->site_srl = 0;
						// One of its members to delete all the group
						$output = executeQuery('member.deleteMemberGroupMember', $args);
						if(!$output->toBool()) {
							$oDB->rollback();
							return $output;
						}
						// Enter one of the loop a
						for($i=0;$i<count($group_srl_list);$i++) {
							$output = $oMemberController->addMemberToGroup($args->member_srl,$group_srl_list[$i]);
							if(!$output->toBool()) {
								$oDB->rollback();
								return $output;
							}
						}
					}
				}	
			}
			
			if(in_array('point', $config->grant_types) || $logged_info->is_admin == 'Y'){
				$oPointController = &getController('point');
				$args->point = $args->point ? $args->point : 0;
				$oPointController->setPoint($args->member_srl, $args->point, 'update');
			}

			$this->setMessage('success_updated');

			if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'mid', Context::get('mid'), 'act', 'dispVicemanagerMemberList', 'page', Context::get('page'),  'search_keyword', Context::get('search_keyword'), 'search_target', Context::get('search_target'));
				header('location:'.$returnUrl);
				return;
			}
        }

    }
