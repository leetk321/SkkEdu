<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");
	
    $message_srl = isset($_POST['message_srl']) ? $_POST['message_srl'] : '';
    if ($message_srl) {
		if (cancelMessage($message_srl)) {
			echo "SUCCESS";
		} else {
			echo "ERROR";
		}
	} else {
		echo "ERROR";
	}
?>