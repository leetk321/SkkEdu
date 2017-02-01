<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");
	
    $message_srl = isset($_POST['message_srl']) ? $_POST['message_srl'] : '';
    $star = isset($_POST['star']) ? $_POST['star'] : '☆';
    $youme = isset($_POST['youme']) ? $_POST['youme'] : 'me';
    
    if ($message_srl) {
    	starMessage($message_srl, $star);
		if ($star == '☆') {
			echo "<img src=\"images/blackstar.icon_$youme.png\" class=\"profile_star_$youme\">";
		} else {
			echo "<img src=\"images/whitestar.icon_$youme.png\" class=\"profile_star_$youme\">";
		}
	} else {
		echo "<img src=\"images/whitestar.icon_$youme.png\" class=\"profile_star_$youme\">";
	}
?>