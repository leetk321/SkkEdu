<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");
	
	$memo = isset($_POST['memo']) ? $_POST['memo'] : '';
	$target_srl = isset($_POST['target_srl']) ? $_POST['target_srl'] : '';
	
	if (!$memo) {
		echo false;
	} else {
		postMessage($member_srl, $target_srl, $memo);
		echo true;
	}
?>