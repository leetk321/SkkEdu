<?php
	if(!defined("__ZBXE__")) exit();

	/**
	* @file manage_member.addon.php
	* @author CMD
	* @brief 회원 관리 애드온
	**/

	// 동작 위치 제어
	if($called_position != 'before_module_init') return;
	
	$logged_info = Context::get('logged_info');
	$manager_list = explode(',', $addon_info->manager_list);	
	
	// 허용할 act를 배열로 만듦
	$allow_act = array(
	'dispMemberAdminList',
	'dispMemberAdminInsert',
	'dispMemberAdminDeleteMembers',
	'dispMemberAdminInfo',
	'dispMemberAdminDeleteForm',
	'dispMemberAdminManageGroup',
	'procMemberAdminInsert',
	'procMemberAdminDelete',
	'procMemberAdminUpdateMembersGroup',
	'procMemberAdminDeleteMembers',
	'procAdminLogout'
	);
	
	if(!in_array($logged_info->user_id, $manager_list)) return;
	if(!in_array($this->act, $allow_act)) return;
	
	// 최고 관리자를 수정하려 할 경우 차단하며 폼을 통해 전송되는 is_admin의 값은 무조건 N으로 함 (쿠데타 방지)
	if($this->act == 'procMemberAdminInsert' || $this->act == 'dispMemberAdminInsert') {
		$oMemberModel = &getModel('member');
		$member_info = $oMemberModel->getMemberInfoByMemberSrl(Context::get('member_srl'));
		
		if($member_info->is_admin == 'Y') return;
		
		Context::set('is_admin', 'N');
	}
	
	$logged_info->is_admin = 'Y';