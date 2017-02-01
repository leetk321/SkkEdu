<?php
	require_once "../../messageTalk/lib/function.php";
	require_once "../../messageTalk/lib/sql_message.php";

	// 로그인 여부
	$member_srl = getMemberInfo('no');
	if (!$member_srl) return;

	$messageCount = getMessageCount($member_srl);
	if ($messageCount == 0) return;
	 
	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');
	
	$alert_new_messageTalk = getLangInfoAddons('alert_new_messageTalk');
	$message = preg_replace('@\r?\n@', '\\n', addslashes($alert_new_messageTalk));
	$message = "$.jGrowl('<a href=\"JavaScript:messageTalk(\'$addon_info->xe_path\');\">".$message."</a>',{life: $addon_info->alert_message_showtime, header : $messageCount });";
 	$text = '<script type="text/javascript">jQuery(function($){'.$message.'});</script>';
	if ($addon_info->alert_message_voice <> "NO") {
		$xe_path = $addon_info->xe_path;
		if ($xe_path == '/') $xe_path = '';
		
		$audioname = $xe_path.'/messageTalk/lib/memo_on.mp3';
		$text.=sprintf("<audio id='audio' src='%s' style='display:none;'></audio>", $audioname);
		$text.='<script type="text/javascript">jQuery(\'audio\').get(0).play();</script>';
	}
	$text = str_replace('%d', $messageCount, $text);
 
	 echo $text;
?>