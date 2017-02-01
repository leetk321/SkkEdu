<?php
    require_once "../lib/function.php";
    require_once "../lib/sql_message.php";
	require_once "../lib/Pusher.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');

	$target_srl = isset($_POST['target_srl']) ? $_POST['target_srl'] : '';
	if (!$target_srl) {
		$target_srl = isset($_GET['target_srl']) ? $_GET['target_srl'] : '';
	}
	
	$message_srl = isset($_POST['message_srl']) ? $_POST['message_srl'] : '';
	if (!$message_srl) {
		$message_srl = isset($_GET['message_srl']) ? $_GET['message_srl'] : '';
	}
	if (($target_srl) && ($message_srl)) {
		echo "exec";
		$channel = pusherchannel($member_srl, $target_srl);
		$pusher = new Pusher($addon_info->pusher_key, $addon_info->pusher_secret, $addon_info->pusher_app_id);
		$pusher->trigger(''.$target_srl.'', 'read', array('message_srl' => '' .$message_srl. '') );
	}
?>