<?php
 
    if(!defined("__ZBXE__") && !defined("__XE__")) exit();
 
    /**
     * @file ggstopspam.addon.php
     * @author 포피플
     * @brief 스팸 패턴을 분석해 취약점을 공략하여 스팸이 등록되지 않도록 합니다.
     **/
 
	$act = Context::get('act');
	if($called_position == 'before_module_proc') {
		if($act == 'trackback') {
			Context::set('document_srl', '');
		}
		if($act == 'procBoardInsertDocument') {
			if(Context::get('password') && Context::get('user_id')) exit();
		}
		if($act == 'procMemberInsert') {
			if(Context::get('_filter') == 'signup') exit();
		}
	}


?>
