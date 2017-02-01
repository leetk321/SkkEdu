<?php
    if (!defined('__XE__') && !defined('__ZBXE__')) exit();

/**
 * @file confirm_declare.addon.php
 * @brief 추천/비추천/신고시 확인 후 적용 애드온
 * @nick_name 키스투엑스이
 * 추천/비추천/신고시 확인창을 띄웁니다.
 **/

	if($called_position != 'before_module_proc' || Context::getResponseMethod()!="HTML") return;

	$msg = "'Really?";
	if($addon_info->msg != "") $msg = $addon_info->msg;

	$scrpit = "<script type='text/javascript'>function doCallModuleAction(a,b,c){confirm('".$msg."')&&exec_xml(a,b,{target_srl:c,cur_mid:current_mid,mid:current_mid},completeCallModuleAction)};</script>";
	Context::addHtmlFooter($scrpit);
