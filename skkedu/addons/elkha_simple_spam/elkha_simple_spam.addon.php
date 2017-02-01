<?php
if($called_position!='before_module_proc') return;

// only for controller
if(!preg_match('/^(?:trackback|proc(?:[A-Z][a-z]+)*Insert(?:Document|Comment))/', $this->act)) return;
if($this->grant->manager) return;

$_content = Context::get('content');
if(!strlen($_content)) return;

$logged_info = Context::get('logged_info');
if(isset($logged_info->nick_name))
{
	$nick_name = &$logged_info->nick_name;
}
else
{
	$nick_name = &Context::get('nick_name');
}

$_content .= Context::get('title');
$_content .= $nick_name;

if(preg_match('/[ㄱ-ㅣ가-힣]/u', $_content)) return;
if(!preg_match('#<a\s|https?://#is', $_content)) return;

$email_address = strlen($addon_info->email_address)? "(\n$addon_info->email_address)" : '';

$output = new object(-1, "스팸으로 의심되어 등록이 보류되었습니다.\n관리자 메일주소로 문의주시길 바랍니다.$email_address");
$oDisplayHandler = new DisplayHandler();
$oDisplayHandler->printContent($output);
exit;
?>