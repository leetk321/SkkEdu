<?php
    require_once "../lib/function.php";
    require_once "../lib/sql_message.php";

    // 로그인 check
    $member_srl = getMemberInfo('no');
    if (!$member_srl) moveUrl("/");

    $target_srl = isset($_GET['target_srl']) ? $_GET['target_srl'] : '';
    $nick_name = isset($_GET['nick_name']) ? $_GET['nick_name'] : '';
    $pos = isset($_GET['pos']) ? $_GET['pos'] : 0;
    $message_count = isset($_GET['message_count']) ? $_GET['message_count'] : 0;

    $profile_target_image = "images/message_read_19.png";
    $formatYou = "
    <div class=\"sm_message_you\">
        <img src=\"$profile_target_image\" class=\"r3\" style=\"position: absolute; bottom:0;left:10px; width:42px; height:42px;\">
        <img src=\"images/message_read_22.png\" style=\"position: absolute; bottom:10px;left:60px;z-index: 10;\">
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
            <li class=\"center_message r3\" >%s</li>
        </ul>
    </div> ";
    $formatMe = "
    <div class=\"sm_message_me\">
        <img src=\"%s\" class=\"r3\" style=\"position: absolute; bottom:0;right:10px; width:42px; height:42px;\">
        <img src=\"images/message_read_34.png\" style=\"position: absolute; bottom:10px;right:63px;z-index: 10;\">
        <ul>
            <li style=\"height:30px;\">
                <span class=\"sm_message_time r3_btn\">
                <span class=\"r3_day\">%s</span>
                 %s
                </span>
            </li>
            <li class=\"center_message r3\" >%s</li>
        </ul>
    </div> ";   

    $result = getMessageDetail($member_srl, $target_srl, $pos, $message_count);

    $message = '';
    while ($data = mysql_fetch_assoc($result)) {
        // update readed 
        postMessageReaded($data[message_srl], $member_srl);
        $last_srl = $data[message_srl];
            
        $date = new DateTime($data[regdate]);
        //$msgDate = $date->format('Y-m-d H:i:s');
        $msgDate = convertPastAgo($date);
        $msgWeek = $date->format('D');
        
        if ($data[message_type] == "S") {
            $message = sprintf($formatMe, "images/message_$data[readed]_31.png", $msgWeek, $msgDate, auto_link(nl2br($data[content]))).$message;
        } else {
            $message = sprintf($formatYou, $msgWeek, $msgDate, auto_link(nl2br($data[content]))).$message;
        }
        $i++;
    }
    echo $message;
?>