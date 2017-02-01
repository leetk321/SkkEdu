<?php
    /**
     * @class  ximember
     * @author Xiso (xiso@xiso.co.kr)
     * @brief  Xiso Member module
     **/
	
	require_once(_XE_PATH_.'modules/member/member.controller.php');
    class ximemberController extends memberController {
		var $xiConfig = NULL;
		/**
         * @brief initialization
         **/
        function init() {
			//get Config
			$oModuleModel = &getModel('module');
			$this->xiConfig = $oModuleModel->getModuleConfig('ximember');
        }
		
		//Addon Trigger
		function triggerModuleHandlerInit(){
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$this->checkUseXiMember()) return;
			
			$logged_info = Context::get('logged_info');
			//add MemberMenu
			if($config->limit['use_update'] == "Y" && $logged_info){
				$oMemberController = &getController('member');
				$oMemberController->addMemberMenu('dispXimemberAuthPage', 'xi_kcb');
			}
			
			if(Context::get('act') == "procMemberInsert"){
				//check Variables
				foreach($config->extra_vars as $method => $extra_vars){
					if($extra_vars->use == "Y"){
						if($extra_vars->signup == 'modify_disabled' || $extra_vars->signup == 'show_disabled'){
							if(Context::get($method) != $_SESSION['auth_info'][$extra_vars->target]) return new Object(-1,"msg_warning_variables");
						}
					}
				}
			}else if(Context::get('act') == "procMemberModifyInfo"){
				//check Variables
				foreach($config->extra_vars as $method => $extra_vars){
					if($extra_vars->use == "Y"){
						if($extra_vars->modify == 'modify_disabled' || $extra_vars->modify == 'show_disabled'){
							if(Context::get($method) != $logged_info->{$method}) return new Object(-1,"msg_warning_variables");
						}
					}
				}
			}
		}
		
		function triggerModuleHandlerProc(&$oModule) {
			//useXimember 여부때문에 먼저 체크
			if(Context::get('act') == "dispMemberAdminInsert" && Context::get('member_srl')){
				$oMemberModel = &getModel('member');
				//$args->member_srl = Context::get('member_srl');
				$member_info = $oMemberModel->getMemberInfoByMemberSrl(Context::get('member_srl'));
				//$ximember_info = executeQuery("ximember.getXimember",$args);
				$age = substr(date('Ymd')-$member_info->birthday,0,2);
				$age = sprintf('<div class="x_control-group"><label class="x_control-label" for="man_age">만 나이</label><div class="x_controls">%s 세</div></div>',$age);
				$script = '<script type="text/javascript">jQuery(document).ready(function($){';
				$script .= sprintf('jQuery(\'%s\').insertBefore(".x_control-group:eq(6)");',$age);
				$script .= '});</script>';
				Context::addHtmlHeader($script);
			}
			
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$this->checkUseXiMember()) return;
			
			//save Voted Member
			if(Context::get('voted_id')) $_SESSION['voted_id'] = Context::get('voted_id');
			
			//change Pages
			Context::loadLang('./modules/ximember/lang');
			global $lang;
			if(array_key_exists(Context::get('act'),$lang->xi_change_pages_list) && is_array($config->xi_change_pages)){
				if(in_array(Context::get('act'),$config->xi_change_pages)){
					$redirect_url = getNotEncodedUrl('act', str_replace("dispMember", "dispXimember",Context::get('act')));
					return header('Location: ' . $redirect_url, $_SERVER['REQUEST_URI']);
				}
			}
			//change proc
			/* if(Context::get('act') == "procMemberInsert" && in_array("dispMemberSignUpForm",$config->xi_change_pages)){
				$redirect_url = getNotEncodedUrl('act', "dispXiMemberSignUpForm");
				return header('Location: ' . $redirect_url, $_SERVER['REQUEST_URI']);
			} */
			
			//check NoMember Limit
			$logged_info = Context::get('logged_info');
			if($logged_info->is_admin == "Y" || Context::get('module') == 'admin') return;
			
			$oMemberModel = &getModel('member');
			//cancel?
			if($_SESSION['auth_info']["resultCd"] == "B123"){
				unset($_SESSION['agreements']);
				unset($_SESSION['auth_info']);
			}
			if($config->limit["use_nom_limit"] == 'Y' && !$oMemberModel->isLogged() && !strpos(Context::get('act'),"Ximember") && !strpos(Context::get('act'),"Member") && !$_SESSION['auth_info']){
				return header('Location: ' . getNotEncodedUrl('act','dispMemberLoginForm'), $_SERVER['REQUEST_URI']);
			}else if($config->limit['use_member'] == "Y" && $oMemberModel->isLogged() && !strpos(Context::get('act'),"Ximember")){
				$chk_obj->member_srl = $logged_info->member_srl;
				$output = executeQuery('ximember.chkAuth',$chk_obj);
				if($output->toBool() && $output->data->count == '0')
					return header('Location: ' . getNotEncodedUrl('act','dispXimemberAuthPage','forced','Y'), $_SERVER['REQUEST_URI']);
			}
		}
		
		function triggerDisplay(){
		}
		
		function procXimemberSetAgree(){
			$args = Context::gets('agree_srl','value');
			if($args->agree_srl == "0") $args->agree_srl = 'default';
			$_SESSION["agreements"][$args->agree_srl] = $args->value;
			foreach($_SESSION["agreements"] as $key=>$val) if($key == '') unset($_SESSION["agreements"][$key]);
			//return new Object(-1, json_encode($_SESSION['agreements']));
			return $this->setMessage(json_encode($_SESSION['agreements']));
		}
		
		function triggerInsertMemberBefore(){
			return;
		}
		
		function triggerInsertMember(&$obj){
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$this->checkUseXiMember()) return;
			
			$member_srl = $obj->member_srl;
            $oMemberController = &getController('member');
            $oMemberModel = &getModel('member');
			
			//addVoted
			if(Context::get('voted_id')){
				//checkVotedID
				$voted_member = $oMemberModel->getMemberInfoByUserID(Context::get('voted_id'));
				if(!$voted_member) return new Object(-1,'msg_not_voted_member');
				
				//추천인에게 포인트 지급
				$oPointController = &getController('point');
				$oPointController->setPoint($voted_member->member_srl, $config->limit['voted_point'], "add");
				
				$args->voted_member_srl = $voted_member->member_srl;
			}
			
			//backup AuthData
			$args->member_srl = $member_srl;
			$args->ci = $_SESSION['auth_info']["CI"];
			$args->di = $_SESSION['auth_info']["DI"];
			$args->birth_ori = $_SESSION['auth_info']['birthday'];
			$args->sex = $_SESSION['auth_info']['ori_sex'];
			$args->is_forigen = $_SESSION['auth_info']['ori_for'];
			$args->auth_type = $_SESSION['auth_info']['ori_auth'];
			$args->mobile = $_SESSION['auth_info']['mobile'];
			$args->allow_sms = Context::get('allow_sms') == "Y" ? "Y" : "N";
			
			$output = executeQuery('ximember.insertXimember',$args);
			if(!$output->toBool()) {
                $oMemberController->deleteMember($member_srl);
                return $output;
            }
			
			//Add Group
			if(is_array($config->groups)){
				$site_module_info = Context::get('site_module_info');
				foreach($config->groups as $group){
					unset($signup_group,$sex);
					//나이제한이 있는경우
					if($group->fullage){
						$condition['age'] = false;
						if($group->limit_type == "limit_up" && $group->fullage <= $_SESSION['auth_info']["age"]) $condition['age'] = true;
						if($group->limit_type == "limit_down" && $group->fullage >= $_SESSION['auth_info']["age"]) $condition['age'] = true;
					}
					//성별제한이 있는경우
					if($group->sex_type){
						$condition['sex'] = false;
						//성별을 타겟으로하는 메소드를 찾음
						foreach($config->extra_vars as $method => $extra_vars){
							if($extra_vars->target == "sex") $target = $method;
						}
						//남자의 값
						if($_SESSION['auth_info']['sex'] == $config->extra_vars[$target]->typeA) $sex = "male";
						if($_SESSION['auth_info']['sex'] == $config->extra_vars[$target]->typeB) $sex = "female";
						if($group->sex_type == $sex) $condition['sex'] = true;
					}
					
					//검사한 조건으로 그룹에 추가
					if($group->fullage && $group->sex_type && $condition['age'] && $condition['sex']) $signup_group = true;
					else if($group->fullage && $condition['age'] && !$group->sex_type) $signup_group = true;
					else if($group->sex_type && $condition['sex'] && !$group->fullage) $signup_group = true;
					
					if($signup_group)	$output = $oMemberController->addMemberToGroup($member_srl, $group->group_srl, $site_module_info->site_srl);
				}
			}
			$this->_clearMemberCache($member_srl, $site_module_info->site_srl);
			unset($_SESSION['auth_info']);
		}
		
		function triggerDeleteMember(&$obj) {
            $member_srl = $obj->member_srl;
            $args->member_srl = $member_srl;
			
            $output = executeQuery('ximember.deleteXimember', $args);
            if (!$output->toBool()) return $output;
            return new Object();
        }
		
		function procXimemberAuthUpdater(){
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if(!$this->checkUseXiMember()) return;
			
			$logged_info = Context::get('logged_info');
			if(!$logged_info) return new Object(-1,"msg_not_logged");
			
			//DI 값이있으면 di가 일치하는지 체크
			$args->member_srl = $logged_info->member_srl;
			$oXiInfo = executeQuery('ximember.getXimember',$args)->data;
			if($oXiInfo && ($oXiInfo->di != $_SESSION['auth_info']["DI"])) return new Object(-1,"msg_different_user");
			
			//complete auth?
			if(!$_SESSION['auth_info']["DI"]) return new Object(-1,"msg_not_auth");
			
			//backup AuthData
			$args->member_srl = $logged_info->member_srl;
			$args->ci = $_SESSION['auth_info']["CI"];
			$args->di = $_SESSION['auth_info']["DI"];
			$args->birth_ori = $_SESSION['auth_info']['birthday'];
			$args->sex = $_SESSION['auth_info']['ori_sex'];
			$args->is_forigen = $_SESSION['auth_info']['ori_for'];
			$args->auth_type = $_SESSION['auth_info']['ori_auth'];
			$args->mobile = $_SESSION['auth_info']['mobile'];
			
			if($oXiInfo){
				$output = executeQuery('ximember.updateXimember',$args);
			}else{
				$args->allow_sms = $args->mobile ? "Y" : "N";
				$output = executeQuery('ximember.insertXimember',$args);
			}
			if(!$output->toBool()) return $output;
			
			//Add Group
			if(is_array($config->groups)){
				$oMemberController = &getController('member');
				$site_module_info = Context::get('site_module_info');
				foreach($config->groups as $group){
					//일단 이그룹에서 제거
					$delete_args->member_srl = $logged_info->member_srl;
					$delete_args->group_srl = $group->group_srl;
					$output = executeQuery('ximember.deleteMemberGroupMember',$delete_args);
					$condition = array();
					unset($signup_group,$sex);
					//나이제한이 있는경우
					if($group->fullage){
						$condition['age'] = false;
						if($group->limit_type == "limit_up" && $group->fullage <= $_SESSION['auth_info']["age"]) $condition['age'] = true;
						if($group->limit_type == "limit_down" && $group->fullage >= $_SESSION['auth_info']["age"]) $condition['age'] = true;
					}
					//성별제한이 있는경우
					if($group->sex_type){
						$condition['sex'] = false;
						//성별을 타겟으로하는 메소드를 찾음
						foreach($config->extra_vars as $method => $extra_vars){
							if($extra_vars->target == "sex") $target = $method;
						}
						//남자의 값
						if($_SESSION['auth_info']['sex'] == $config->extra_vars[$target]->typeA) $sex = "male";
						if($_SESSION['auth_info']['sex'] == $config->extra_vars[$target]->typeB) $sex = "female";
						if($group->sex_type == $sex) $condition['sex'] = true;
					}
					
					//검사한 조건으로 그룹에 추가
					if($group->fullage && $group->sex_type && $condition['age'] && $condition['sex']) $signup_group = true;
					else if($group->fullage && $condition['age'] && !$group->sex_type) $signup_group = true;
					else if($group->sex_type && $condition['sex'] && !$group->fullage) $signup_group = true;
					//echo sprintf("sex : %s == %s, age : %s, signup : %s",$sex, $group_sex_type, $condition['age'], $signup_group);
					//echo "<br />";
					if($signup_group)	$output = $oMemberController->addMemberToGroup($logged_info->member_srl, $group->group_srl, $site_module_info->site_srl);
				}
			}
			$this->_clearMemberCache($logged_info->member_srl, $site_module_info->site_srl);
			
			unset($_SESSION['auth_info']);
			
			$this->setMessage('success');
			return $this->setRedirectUrl(getNotEncodedUrl('','act','dispXimemberAuthPage','mid',Context::get('mid')));
		}
		
		function checkUseXiMember(){
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('ximember');
			if($config->use_ximember != "Y") return false;
			
			if(!is_array($config->mid_list)) return true;
			
			if($config->xe_run_method == "no_run_selected"){
				if(in_array(Context::get('mid'),$config->mid_list)) return false;
				else return true;
			}else{
				if(in_array(Context::get('mid'),$config->mid_list)) return true;
				else return false;
			}
		}
		
    }
?>
