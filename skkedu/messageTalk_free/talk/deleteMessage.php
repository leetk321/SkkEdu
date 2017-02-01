<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");
	
	$memberlist = $_POST["memberlist"];
	
	if (empty($memberlist)) {
		echo getLangInfo('msg_select_delete_messageTalk');
	} else {
		for ($i=0; $i<count($memberlist);$i++) {
			delMessage($member_srl, $memberlist[$i]);
		} // end for
		echo getLangInfo('msg_deleted_messageTalk');
	}
?>