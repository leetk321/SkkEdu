<?php
	require_once "dbconn.php";

    function getMessageNextSequence() {
		global $connect, $prefix;

        $sql = sprintf("insert into %ssequence (seq) values ('0')", 
        			mysql_real_escape_string($prefix));
        mysql_query($sql, $connect);
        
		$sequence = mysql_insert_id($connect);
        if($sequence % 10000 == 0) {
            $sql = sprintf("delete from %ssequence where seq < %s", 
            			mysql_real_escape_string($prefix), 
            			mysql_real_escape_string($sequence));
            mysql_query($sql, $connect);
        } 
        return $sequence;
    }

	function getMessageList($member_srl) {
		global $connect, $prefix;
		
		$sql = sprintf("
SELECT m.member_srl, m.nick_name, max(msg.regdate) AS msgDate, max(msg.message_srl) as msgSrl
  FROM %smember m, 
		( SELECT CASE message_type WHEN 'S'THEN receiver_srl WHEN 'R' THEN sender_srl END AS mid, regdate, message_srl
			FROM %smember_message
		   WHERE (sender_srl = %s and message_type = 'S')
			  OR (receiver_srl = %s and message_type = 'R') ) msg
 WHERE m.member_srl = msg.mid
 GROUP BY m.member_srl, m.nick_name
 ORDER BY 3 DESC ",
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($member_srl));

		return mysql_query($sql, $connect);
	} 

	function getNewMessageList($member_srl) {
		global $connect, $prefix;
		
		$sql = sprintf("
SELECT m.member_srl, m.nick_name, max(msg.regdate) AS msgDate, max(msg.message_srl) as msgSrl
  FROM %smember m, 
		( SELECT CASE message_type WHEN 'S'THEN receiver_srl WHEN 'R' THEN sender_srl END AS mid, regdate, message_srl
			FROM %smember_message
		   WHERE receiver_srl = %s AND message_type = 'R'
		     AND readed = 'N' ) msg
 WHERE m.member_srl = msg.mid
 GROUP BY m.member_srl, m.nick_name
 ORDER BY 3 DESC ",
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($member_srl));

		return mysql_query($sql, $connect);
	} 

	function getMessageDetail($member_srl, $target_srl) {
		global $connect, $prefix;

		$sql = sprintf("
SELECT message_srl, related_srl, message_type, content, CASE WHEN readed = 'Y' then 'readed' else 'unread' end AS readed, regdate, readed_date
  FROM %smember_message
 WHERE (sender_srl = %s and message_type = 'S' AND receiver_srl = %s)
    OR (sender_srl = %s and message_type = 'R' AND receiver_srl = %s) 
 ORDER BY message_srl ASC ",
 				mysql_real_escape_string($prefix), 
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($member_srl));
		
		return mysql_query($sql, $connect);
	}
	
	function delMessage($member_srl, $target_srl) {
		global $connect, $prefix;

		$sql = sprintf("
DELETE FROM %smember_message
 WHERE (sender_srl = %s and message_type = 'S' AND receiver_srl = %s)
    OR (sender_srl = %s and message_type = 'R' AND receiver_srl = %s) ",
    			mysql_real_escape_string($prefix), 
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($member_srl));
		
		return mysql_query($sql, $connect);
	}
	
	function postMessage($member_srl, $target_srl, $content) {
		global $connect, $prefix;
		
		$formatSql = 
                " INSERT INTO %smember_message (message_srl, related_srl, sender_srl, receiver_srl, message_type, title, content, readed, readed_date, list_order, regdate)" .
				" VALUES (%s, %s, %s, %s, '%s', '%s', '%s', '%s', %s, %s, '%s') ";

		$related_srl = getMessageNextSequence();
		$message_srl = getMessageNextSequence();
		$list_order = getMessageNextSequence()*-1;
			
		// 제목은 내용 일부를 발췌
		$content_length = 28;
		$title = strip_tags($content);
		$title = str_cut_string($title, $content_length);

		$date = new DateTime();
		$regdate = $date->format('YmdHis');
		
		$sql = sprintf($formatSql,
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($message_srl),
				mysql_real_escape_string($related_srl),
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string('S'),
				mysql_real_escape_string($title),
				mysql_real_escape_string($content),
				mysql_real_escape_string('N'),
				mysql_real_escape_string('NULL'),
				mysql_real_escape_string($list_order),
				mysql_real_escape_string($regdate));

		mysql_query($sql, $connect);
		$sql = sprintf($formatSql,
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($related_srl),
				mysql_real_escape_string('0'),
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string('R'),
				mysql_real_escape_string($title),
				mysql_real_escape_string($content),
				mysql_real_escape_string('N'),
				mysql_real_escape_string('NULL'),
				mysql_real_escape_string($related_srl*-1),
				mysql_real_escape_string($regdate));
		mysql_query($sql, $connect);
	}
	 
	function postMessageReaded($message_srl, $member_srl) {
		global $connect, $prefix;

		$date = new DateTime();
		$readed_date = $date->format('YmdHis');

		$sql = sprintf("
UPDATE %smember_message
   SET readed = 'Y', readed_date = '%s'
 WHERE (message_srl = %s OR related_srl = %s) 
   AND readed = 'N' AND receiver_srl = %s 
 ", 
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($readed_date),
				mysql_real_escape_string($message_srl),
				mysql_real_escape_string($message_srl),
				mysql_real_escape_string($member_srl));

		return mysql_query($sql, $connect);
	}

	function getMessage($message_srl) {
		global $connect, $prefix;

		$sql = sprintf("
SELECT content
  FROM %smember_message
 WHERE message_srl = %s ",
 				mysql_real_escape_string($prefix), 
				mysql_real_escape_string($message_srl));
		
		$result = mysql_query($sql, $connect);
		return mysql_result($result, 0);
	}
	
	function getMessageStatus($member_srl, $target_srl) {
		global $connect, $prefix;

		$sql = sprintf("
SELECT CASE WHEN message_type = 'S' THEN 'Y' ELSE readed END AS readed, count(1) as count
  FROM %smember_message
 WHERE (sender_srl = %s and message_type = 'S' AND receiver_srl = %s)
    OR (sender_srl = %s and message_type = 'R' AND receiver_srl = %s) 
 GROUP BY CASE WHEN message_type = 'S' THEN 'Y' ELSE readed END
 ", 
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($member_srl));
		
		$result = mysql_query($sql, $connect);
		$readed = 0; $unread = 0;
		while ($data = mysql_fetch_assoc($result)) {
			if ($data[readed] == "Y") {
				$readed = $data[count];	
			} else {
				$unread = $data[count];
			}
		}
		$format = "%d,%d";
		return sprintf($format, $readed, $unread);
	}
	
	function getAddonInfo($site_srl, $addon) {
		global $connect, $prefix;
		
		$sql = sprintf("
SELECT extra_vars
  FROM %saddons_site
 WHERE site_srl = %s
   AND addon = '%s' ", 
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($site_srl),
				mysql_real_escape_string($addon));
		$result = mysql_query($sql, $connect);
		$addon_info = null;
		
		while ($data = mysql_fetch_assoc($result)) {
			$addon_info->member_menu = unserialize($data[extra_vars])->member_menu;
			if (!$addon_info->member_menu) $addon_info->member_menu = 'NO';
			
			$addon_info->send_mode = unserialize($data[extra_vars])->send_mode;
			if (!$addon_info->send_mode) $addon_info->send_mode = 'NO';
			
			$addon_info->xe_path = unserialize($data[extra_vars])->xe_path;
			if (!$addon_info->xe_path) $addon_info->xe_path = '/';
		}
		
		return $addon_info;
	}
	
	function getFindFriend($option, $keyword) {
		global $connect, $prefix;
		
		if ($option == 'id') $where = ' user_id = \'%s\' ';
		else if ($option == 'nickname') $where = ' nick_name = \'%s\' ';
		else if ($option == 'email') $where = ' email_address = \'%s\' ';
		
		$sql = sprintf("
SELECT member_srl, nick_name
  FROM %smember
 WHERE $where ", 
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($keyword));
				
		
		return mysql_query($sql, $connect);
	}

	function getMessageCount($member_srl) {
		global $connect, $prefix;
		
		$sql = sprintf("
SELECT count(1) AS cnt
  FROM %smember member, %smember_message member_message
 WHERE member_message.receiver_srl = %s
   AND member_message.readed = 'N' 
   AND member_message.sender_srl = member.member_srl 
   AND member_message.related_srl = 0 ", 
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($prefix),
				mysql_real_escape_string($member_srl));
				
		$result = mysql_query($sql, $connect);
		$message_count = 0;
		while ($data = mysql_fetch_assoc($result)) {
			$message_count = $data[cnt]; 
		}
		
		return $message_count;
	}	 
?>
