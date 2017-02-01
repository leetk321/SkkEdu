<?php
	if(!defined('__ZBXE__') && !defined('__XE__')) exit();
	/**
	 * @file ex_notice.addon.php
	 * @author 민채
	 * @brief 공지 게시글 관리 애드온
	 **/


	if(!($logged_info->is_admin == 'Y')) return;

	//글 삭제시
	if(Context::get('document_srl') && Context::get('is_ex_notice')){
		
		$args->document_srl = Context::get('document_srl');
		
		executeQuery('addons.ex_notice.updateDocumentNotice', $args);
				
		$returnUrl = getFullUrl('','mid',$this->mid);
		$this->setRedirectUrl($returnUrl);
	}	
	if($called_position == 'after_module_proc')
	{
		
		$link = getFullUrl('').'index.php?mid='.$this->mid;					
		Context::addHtmlHeader(sprintf("<script type=\"text/javascript\"> var addon_exnotice_var='%s';</script>", trim($link)));	
		Context::addJsFile('./addons/ex_notice/ex_notice.js');
		
	}
?>