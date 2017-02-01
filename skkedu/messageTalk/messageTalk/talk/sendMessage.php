<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";
	require "../lib/Pusher.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");
	
	$memo = isset($_POST['memo']) ? $_POST['memo'] : '';
	$target_srl = isset($_POST['target_srl']) ? $_POST['target_srl'] : '';
	
	if (!$memo) {
		return;
	} else {
		$now = new DateTime;
		$message_srl = postMessage($member_srl, $target_srl, $memo);
	}

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');

	// profile Image
	$profile_target_image = getProfileImage($target_srl);
	if(!$profile_target_image) {
		$profile_target_image = "images/message_read_19.png";
	}
	$profile_member_image = getProfileImage($member_srl);
	$msgDate = $now->format('H:i:s');
	$msgWeek = $now->format('D');
	$formatMe = "
	<div class=\"sm_message_me\">
        <img id=\"profile$message_srl\" src=\"%s\" class=\"profile_img_me r3\">%s
		<img src=\"images/message_read_34.png\" style=\"position: absolute; bottom:10px;right:63px;z-index: 10;\">
		<ul>
			<li style=\"height:30px;\">
    			<span class=\"sm_message_time r3_btn\">
    			<span class=\"r3_day\">%s</span>
    			 %s
    			</span>
				<span id=\"$message_srl\" class=\"sm_message_del r3_btn\"><a href=\"javascript:cancelMessageTalk($message_srl);\">x</a></span>
			</li>
			<li class=\"center_message r3\" >%s</li>
		</ul>
	</div> ";
	$starMessage = "<span id=\"star$message_srl\"><a href='javascript:starMessageTalk($message_srl,\"☆\", \"me\");'><img src=\"images/whitestar.icon_me.png\" class=\"profile_star_me\"></a></span>";
	echo sprintf($formatMe, "images/message_unread_31.png", $starMessage, $msgWeek, $msgDate, auto_link(nl2br($memo)));	
	$pusher = new Pusher($addon_info->pusher_key, $addon_info->pusher_secret, $addon_info->pusher_app_id);
	$pusher->trigger(''.$target_srl.'', 'talk', array('message_srl' => '' .$message_srl. '', 'target_srl' => ''.$member_srl.'') );
?>