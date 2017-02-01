<?php
if(!defined('__ZBXE__') && !defined('__XE__')) exit();

// Stop if non-logged-in user is
$logged_info = Context::get('logged_info');
if(!$logged_info) return;

if($called_position == 'before_module_init' && $this->module != 'member') {

	// Load a language file 
	Context::loadLang('./addons/messageTalk/lang');

	$xe_path = $addon_info->xe_path;
	if ($xe_path == '/') $xe_path = '';
	if ($addon_info->member_menu <> "NO") {
		// 회원 로그인 정보중에 메뉴를 추가
		$oMemberController = &getController('member');
		$oMemberController->addMemberMenu('dispMemberMessageTalk', 'cmd_messageTalk');
					
		// hooking
		if($this->act == 'dispMemberMessageTalk'){
			Context::addJsFile('./addons/messageTalk/messageTalk.js');
			$addPopupScript = '<script type="text/javascript">messageTalk('.'"'.$xe_path.'"'.');</script>';
			Context::addHtmlFooter($addPopupScript);
	
			$this->act = 'dispMemberLoginForm';
			Context::set('isMessageTalk', 'true');
		}
	
	}

	if ($addon_info->alert_message <> "NO") {

		// 실시간 알림 	
		if ($addon_info->alert_message_realtime <> "0") {
			$realTime = 
	'<script type="text/javascript">
		jQuery(document).ready(function() {
			setInterval(function() {
				jQuery(\'#checkMessageTalk\').load(\'./addons/messageTalk/messageTalk.lib.php\');
			}, '.$addon_info->alert_message_realtime.');
		});		 
	</script>'; 
			$messageTalkDiv = '<div id="checkMessageTalk"></div>';
			Context::addBodyHeader($messageTalkDiv);
			Context::addBodyHeader($realTime);
		}

		Context::addCSSFile("./addons/messageTalk/jquery.jgrowl.css", false);
		Context::addJsFile('./addons/messageTalk/messageTalk.js', false ,'', null, 'body');
		Context::addJsFile('./addons/messageTalk/jquery.jgrowl.min.js', false ,'', null, 'body');

		$aobj->receiver_srl = $logged_info->member_srl;
		$aobj->readed = 'N';
		$aobj->related_srl = 0;
		$output = executeQueryArray('addons.messageTalk.getMessageCount', $aobj);
	
		if(!count($output->data)) return;
		
		$showtime = $addon_info->alert_message_showtime;
		if (!$showtime) $showtime = 15000;

		$message = preg_replace('@\r?\n@', '\\n', addslashes(Context::getLang('alert_new_messageTalk')));

		$messageCount = count($output->data);
		$message = "$.jGrowl('<a href=\"JavaScript:messageTalk(\'$addon_info->xe_path\');\">".$message."</a>',{life: $showtime, header : $messageCount, subDomain : '$xe_path' });";	
	
		$text = '<script type="text/javascript">jQuery(function($){'.$message.'});</script>';
		if ($addon_info->alert_message_voice <> "NO") {
			$audioname = $xe_path.'/messageTalk/lib/memo_on.mp3';
			$text.=sprintf("<audio id='audio' src='%s' style='display:none;'></audio>", $audioname);
			$text.='<script type="text/javascript">jQuery(\'audio\').get(0).play();</script>';
		}
		$text = str_replace('%d', count($output->data), $text);
		Context::addHtmlFooter($text);
	}
} elseif ($called_position == 'before_module_proc' && $this->act == 'getMemberMenu') {
	// Load a language file 
	Context::loadLang('./addons/messageTalk/lang');
	
	$oMemberController = &getController('member');
	$member_srl = Context::get('target_srl');
	$mid = Context::get('cur_mid');
	
	$xe_path = $addon_info->xe_path;
	if ($xe_path == '/') $xe_path = '';
	
	// Add a feature to display own message box.
	if($logged_info->member_srl == $member_srl) {
		// Add your own viewing Note Template
		$oMemberController->addMemberPopupMenu($xe_path.'/messageTalk/talk/index.php', 'cmd_messageTalk', '', 'popup');
	// If not, Add menus to send message and to add friends
	} else {
		// Get member information
		$oMemberModel = &getModel('member');
		$target_member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
		if(!$target_member_info->member_srl) return;
		// Get logged-in user information
		$logged_info = Context::get('logged_info');
		// Add a menu for sending message
		if( $logged_info->is_admin == 'Y' || $target_member_info->allow_message =='Y' || ($target_member_info->allow_message == 'F' && $oCommunicationModel->isFriend($member_srl)))
			$oMemberController->addMemberPopupMenu($xe_path.'/messageTalk/talk/detail.php?target_srl='.$member_srl.'&nick_name='.$target_member_info->nick_name.'#last', 'cmd_messageTalk', '', 'popup');
	}
}else if($called_position == 'after_module_proc' ) {
	if($this->act == 'dispMemberLoginForm' && Context::get('isMessageTalk') == true) {
	        
        $oCommunicationModel = &getModel('communication');

        $communication_config = $oCommunicationModel->getConfig();
        $skin = $communication_config->skin;

		Context::loadLang('./addons/messageTalk/lang');
	
		Context::set('skin', $skin);
		Context::addCSSFile("../modules/communication/skins/$skin/css/communication.css", false);
		Context::set('xe_path', $addon_info->xe_path);
		
		$this->setTemplateFile('messageTalk');		
			
		$this->setTemplatePath('./addons/messageTalk/tpl');
	}
}
?>