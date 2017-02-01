<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");

	$findoption = isset($_POST['findoption']) ? $_POST['findoption'] : '';
	$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
	
	$result = getFindFriend($findoption, $keyword);

	$res = "";	
	if (mysql_num_rows($result) == 0) {
		$res = "
	<div id=\"noResult\" class=\"sm_message_box r3\">
		<ul>
			<li class=\"friend_li\" style=\"text-align: center;\">
				".getLangInfo('msg_no_FindFriends')."
			</li>
		</ul>
	</div> ";
		
	} else {
		while ($data = mysql_fetch_assoc($result)) {
			$res .= "
	<div class=\"sm_message_box r3\">
		<ul>
			<li class=\"friend_li\">
				<img src=\"images/message_list_08.png\" alt=\"id icon\"> <a href=\"detail.php?target_srl=$data[member_srl]&nick_name=$data[nick_name]#last\">$data[nick_name]</a>
				<div style=\"float: right;\">
				</div>
			</li>
		</ul>
	</div> ";

		}
	}
	
	echo $res;
?>