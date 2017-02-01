<?php
    require_once "../lib/function.php";
    require_once "../lib/sql_message.php";

    // 로그인 check
    $member_srl = getMemberInfo('no');
    if (!$member_srl) moveUrl("/");

    $target_srl = isset($_GET['target_srl']) ? $_GET['target_srl'] : '';
    $nick_name = isset($_GET['nick_name']) ? $_GET['nick_name'] : '';
    $messageType = isset($_GET['messageType']) ? $_GET['messageType'] : '';
    $pos = isset($_GET['pos']) ? $_GET['pos'] : 0;
    $message_count = isset($_GET['message_count']) ? $_GET['message_count'] : 10;
    // profile Image
    $profile_target_image = getProfileImage($target_srl);
    if(!$profile_target_image) {
        $profile_target_image = "images/message_read_19.png";
    }
    $profile_member_image = getProfileImage($member_srl);

    $formatYou = "
    <div class=\"sm_message_you\">
            <img src=\"$profile_target_image\" class=\"profile_img r3\">%s
        <ul>
            <li style=\"height:30px;\">
                <span class=\"sm_message_time r3_btn\">
                <span class=\"r3_day\">%s</span>
                 %s
                </span>
                <span class=\"nickname\">
                    $nick_name
                </span>
             </li>
            <li class=\"center_message r3\" >
               <img src=\"images/message_read_22.png\" class=\"profile_you_tail\">
                 %s
            </li>
        </ul>
    </div> ";
    $formatMe = "
    <div class=\"sm_message_me\">
        <img id=\"profileme\" src=\"%s\" class=\"profile_img_me r3\">%s
        <ul>
            <li style=\"height:30px;\">
                <span class=\"sm_message_time r3_btn\">
                <span class=\"r3_day\">%s</span>
                 %s
                </span>%s
            </li>
            <li class=\"center_message r3\" >
                <img src=\"images/message_read_34.png\" class=\"profile_me_tail\">
                 %s
            </li>
        </ul>
    </div> ";   

    $result = getMessageDetail($member_srl, $target_srl, $messageType, $pos, $message_count);

    $message = '';
    while ($data = mysql_fetch_assoc($result)) {
        // update readed 
        postMessageReaded($data[message_srl], $member_srl);
        $last_srl = $data[message_srl];
            
        $date = new DateTime($data[regdate]);
        //$msgDate = $date->format('Y-m-d H:i:s');
        $msgDate = convertPastAgo($date);
        $msgWeek = $date->format('D');
        if (str_cut_string($data[title], 1, '') == '★') {
        	$star = 'blackstar';
        } else {
        	$star = 'whitestar';
        }
        if ($data[message_type] == "S") {
	        //star Message
        	$starMessage = "<span id=\"star$data[message_srl]\"><a href='javascript:starMessageTalk($data[message_srl],\"$star\", \"me\");'><img src=\"images/$star.icon_me.png\" class=\"profile_star_me\"></a></span>";
            if ($profile_member_image && $data[readed] == "readed") {
                $message = sprintf($formatMe, $profile_member_image, $starMessage, $msgWeek, $msgDate, '', auto_link(nl2br($data[content]))).$message;
            } else {
            	if ($data[readed] == "readed") {
            		$message = sprintf($formatMe, "images/message_$data[readed]_31.png", $starMessage, $msgWeek, $msgDate, '', auto_link(nl2br($data[content]))).$message;
            	} else {
	                $message = sprintf($formatMe, "images/message_$data[readed]_31.png", $starMessage, $msgWeek, $msgDate, "<span id=\"$data[message_srl]\" class=\"sm_message_del r3_btn\"><a href=\"javascript:cancelMessageTalk($data[message_srl]);\">x</a></span>", auto_link(nl2br($data[content]))).$message;
            	}
            }
        } else {
        	$starMessage = "<span id=\"star$data[message_srl]\"><a href='javascript:starMessageTalk($data[message_srl],\"$star\", \"you\");'><img src=\"images/$star.icon_you.png\" class=\"profile_star_you\"></a></span>";
            $message = sprintf($formatYou, $starMessage, $msgWeek, $msgDate, auto_link(nl2br($data[content]))).$message;
        }
        $i++;
    }
	$message = str_replace("<p>", "", $message);
	$message = str_replace("</p>", "", $message);
    echo $message;
?>