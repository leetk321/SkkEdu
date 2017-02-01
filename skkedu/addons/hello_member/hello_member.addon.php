<?php
    if(!defined("__XE__")) exit();

    /**
     * @hello_member.addon.php
     * @author 카르마(http://www.wildgreen.co.kr)
     * @brief 자동등업 에드온
     *
     **/

	$title ="정회원으로 등업되셨습니다.";
	$msg_content ="정회원이 되심을 환영합니다.";

	$args->documents = $addon_info->documents;
	if(!$args->documents) $args->documents=1;
	
	//정회원 그룹이 설정되지 않으면 패쑤~~
	$logged_info = Context::get('logged_info');
	$args->with_group = $addon_info->with_group;

	//로그인 상태가 아니면 패쑤~~
	if(!$logged_info) return;

	//관리자도 패쑤~~
	if($logged_info->is_admin == 'Y') return;
	$mid = Context::get('mid');
	$vid = Context::get('vid');
	if($vid && $mid !=$vid) {
		$oModuleModel = &getModel('module');
    	$site_info = $oModuleModel->getSiteInfoByDomain($vid);
    	$args->site_srl = $site_info->site_srl;
	} else $args->site_srl =0;

	$oMemberModel=&getModel('member');
	$args->default_group = $oMemberModel ->getDefaultGroup($site_srl);
	if(!$args->default_group) return;

	if(!$args->with_group) return;

	//정회원그룹과 default group이 같으면 패쑤~~~
	if($args->default_group == $args->with_group) return;

    $group_list = array_keys($logged_info->group_list);
	$obj->member_srl = $logged_info->member_srl;

//
if($called_position == 'after_module_proc' && ($this->act == 'procBoardInsertDocument' || $this->act=='procBodexInsertDocument')) {
        $site_info=Context::get('site_module_info');
}

	//글을 default group 회원이 글을 쓸때...
	if($called_position == 'after_module_proc' && ($this->act == 'procBoardInsertDocument' || $this->act=='procBodexInsertDocument') && !in_array($args->with_group,$group_list)) {

		$mid_list=$addon_info->mid_list;

		$args->module_srls = null;
        if(count($mid_list)) {
			$oModuleModel= &getModel('module');
            $module_srls = $oModuleModel->getModuleSrlByMid($mid_list);
            if(count($module_srls)) $args->module_srls = implode(',',$module_srls);
            else $args->module_srls = null;
        }

		$args->member_srl=$obj->member_srl;
		$hello = executeQueryArray('addons.hello_member.getMyHello', $args);
		$hello_count = $hello->data[0]->count;
		if($args->documents <= $hello_count) {

			//default group을 정회원그룹으로 변경
			$obj->target_group_srl = $args->with_group;
			$obj->present_group_srl = $args->default_group;
			if($addon_info->update_group !='N' && $args->site_srl ==0) $query_id='addons.hello_member.updateGroup';
			else $query_id='addons.hello_member.insertGroup';
			$temp = executeQuery($query_id, $obj);
			$oCacheHandler = CacheHandler::getInstance('object', null, true);
	        if($oCacheHandler->isSupport())
    	    {
        	    $oCacheHandler->invalidateGroupKey('member');
        	}


			if($addon_info->give_point) {
				$oPointController = &getController('point');
    	        $oPointModel = &getModel('point');
				$owner_point = $oPointModel->getPoint($obj->member_srl, true);
            	$oPointController->setPoint($obj->member_srl, $owner_point+$addon_info->give_point);
			}
			if($addon_info->send_message =='Y') {
            if($addon_info->title) $title = cut_str(strip_tags($addon_info->title), 40);
            if($addon_info->msg_content) $msg_content = $addon_info->msg_content;

            $oCommunicationController = &getController('communication');
            $oCommunicationController->sendMessage($obj->member_srl, $obj->member_srl, $title, $msg_content, false);
            }
		}
	}
	
	//정회원이 글을 삭제할때
	//자동등업 취소를 사용할때만 작동
	if($addon_info->del_group=='Y' && $called_position == 'after_module_proc' && ($this->act == 'procBoardDeleteDocument' || $this->act == 'procBodexDeleteDocument') && in_array($args->with_group,$group_list)) {


		$mid_list=$addon_info->mid_list;
		// mid를 module_srl로
		$args->module_srls = null;
		if(count($mid_list)) {
			$oModuleModel= &getModel('module');
	       	$module_srls = $oModuleModel->getModuleSrlByMid($mid_list);
            if(count($module_srls)) $args->module_srls = implode(',',$module_srls);
            else $args->module_srls = null;
			
        }

		//해당영역의 글 숫자를 체크
		$args->member_srl=$obj->member_srl;
		$hello = executeQueryArray('addons.hello_member.getMyHello', $args);
		$hello_count = $hello->data[0]->count;

		//해당 영역에 글이 없는 경우
		if($args->documents > $hello_count) {
			//정회원 그룹을 다시 default group으로...
    	    $obj->target_group_srl=$args->default_group;
        	$obj->present_group_srl=$args->with_group;
	        if($addon_info->update_group !='N' && $args->site_srl == 0) $query_id='addons.hello_member.updateGroup';
			else $query_id='addons.hello_member.deleteGroup';
			executeQuery($query_id, $obj);
			$oCacheHandler = CacheHandler::getInstance('object', null, true);
	        if($oCacheHandler->isSupport())
    	    {
        	    $oCacheHandler->invalidateGroupKey('member');
        	}
			if($addon_info->give_point) {
                $oPointController = &getController('point');
                $oPointModel = &getModel('point');
                $owner_point = $oPointModel->getPoint($obj->member_srl, true);
                $oPointController->setPoint($obj->member_srl, $owner_point - $addon_info->give_point);
            }
		} 
}
