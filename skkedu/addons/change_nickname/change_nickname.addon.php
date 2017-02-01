<?php
	if(!defined('__XE__')) exit();

	if($this->act == 'procMemberModifyInfo' && $called_position == 'after_module_proc'){
		$oMemberController = &getController("member");
		if($oMemberController->message == Context::getLang('success_updated')) {
			$logged_info = Context::get('logged_info');
			$args->nick_name = Context::get('nick_name');
			$args->member_srl = $logged_info->member_srl;
			executeQuery('addons.change_nickname.updatedocuments', $args);
			executeQuery('addons.change_nickname.updatecomments', $args);
		}
	} else if($this->act == 'procMemberAdminInsert' && $called_position == 'after_module_proc') {
		$oMemberAdminController = &getAdminController("member");
		if($oMemberAdminController->message == Context::getLang('success_updated')) {
			$oMemberModel = &getModel("member");
			$member_info = $oMemberModel->getMemberInfoByMemberSrl(Context::get("member_srl"));
			$args->nick_name = $member_info->nick_name;
			$args->member_srl = Context::get("member_srl");
			executeQuery('addons.change_nickname.updatedocuments', $args);
			executeQuery('addons.change_nickname.updatecomments', $args);
		}
	}
?>