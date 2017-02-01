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

	function getMessageDetail($member_srl, $target_srl, $messageType, $pos, $message_count) {
		global $connect, $prefix;

		if (!$messageType) {
		$sql = sprintf("
SELECT message_srl, related_srl, message_type, title, content, CASE WHEN readed = 'Y' then 'readed' else 'unread' end AS readed, regdate, readed_date
  FROM %smember_message
 WHERE (sender_srl = %s and message_type = 'S' AND receiver_srl = %s)
    OR (sender_srl = %s and message_type = 'R' AND receiver_srl = %s) 
 ORDER BY message_srl DESC LIMIT %s, %s ",
 				mysql_real_escape_string($prefix), 
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($pos),
				mysql_real_escape_string($message_count));
		} else {
		$sql = sprintf("
SELECT message_srl, related_srl, message_type, title, content, CASE WHEN readed = 'Y' then 'readed' else 'unread' end AS readed, regdate, readed_date
  FROM %smember_message
 WHERE ((sender_srl = %s and message_type = 'S' AND receiver_srl = %s)
    OR (sender_srl = %s and message_type = 'R' AND receiver_srl = %s)) 
   AND SUBSTRING(title FROM 1 FOR 1) = '★'
 ORDER BY message_srl DESC ",
 				mysql_real_escape_string($prefix), 
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($member_srl));
		}
		//echo $sql;
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
		$title = str_cut_string($title, $content_length, '...');
		
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
		
		return $message_srl;
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
	
	function getStarMessageCount($member_srl, $target_srl) {
		global $connect, $prefix;

		$sql = sprintf("
SELECT count(1) as count
  FROM %smember_message
 WHERE ((sender_srl = %s and message_type = 'S' AND receiver_srl = %s)
    OR (sender_srl = %s and message_type = 'R' AND receiver_srl = %s)) 
   AND SUBSTRING(title FROM 1 FOR 1) = '★' ", 
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($member_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($target_srl),
				mysql_real_escape_string($member_srl));
		
		$result = mysql_query($sql, $connect);
		return mysql_result($result, 0);
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
			
			$addon_info->alert_message = unserialize($data[extra_vars])->alert_message;
			if (!$addon_info->alert_message) $addon_info->alert_message = 'YES';

			$addon_info->alert_message_voice = unserialize($data[extra_vars])->alert_message_voice;
			if (!$addon_info->alert_message_voice) $addon_info->alert_message_voice = 'YES';
			
			// 메시지 개수
			$addon_info->show_message_default = unserialize($data[extra_vars])->show_message_default;
			if (!$addon_info->show_message_default) $addon_info->show_message_default = '10';
			if ($addon_info->show_message_default <= 0) $addon_info->show_message_default = '10';
			if ($addon_info->show_message_default > 50) $addon_info->show_message_default = '50';
			
			$addon_info->show_message_more = unserialize($data[extra_vars])->show_message_more;
			if (!$addon_info->show_message_more) $addon_info->show_message_more = '10';
			if ($addon_info->show_message_more <= 0) $addon_info->show_message_more = '10';
			if ($addon_info->show_message_more > 50) $addon_info->show_message_more = '50';
			
			// pusher 지원
			$addon_info->pusher_use = 'NO'; 
			$addon_info->pusher_key = unserialize($data[extra_vars])->pusher_key;
			$addon_info->pusher_app_id = unserialize($data[extra_vars])->pusher_app_id;
			$addon_info->pusher_secret = unserialize($data[extra_vars])->pusher_secret;
			if ( ($addon_info->pusher_key) && ($addon_info->pusher_app_id) && ($addon_info->pusher_secret) ) {
				$addon_info->pusher_use = 'YES';	
			}
			
			// 실시간 메시지톡 확인 (5초 미만이면 5초로)
			$addon_info->alert_message_realtime = unserialize($data[extra_vars])->alert_message_realtime;
			if (!$addon_info->alert_message_realtime) $addon_info->alert_message_realtime = '0';
			if ($addon_info->alert_message_realtime >= 1 && $addon_info->alert_message_realtime < 5) $addon_info->alert_message_realtime = '5';
			
			$addon_info->alert_message_showtime = unserialize($data[extra_vars])->alert_message_showtime;
			if (!$addon_info->alert_message_showtime) $addon_info->alert_message_showtime = '15';
			
			$addon_info->xe_path = unserialize($data[extra_vars])->xe_path;
			if (!$addon_info->xe_path) $addon_info->xe_path = '/';
			
			$addon_info->colorset_background = unserialize($data[extra_vars])->colorset_background;
			//if (!$addon_info->colorset_background) $addon_info->colorset_background = '#eee;';
			 
			$addon_info->colorset_bar = unserialize($data[extra_vars])->colorset_bar;
			//if (!$addon_info->colorset_bar) $addon_info->colorset_bar = '#ed1b2e;';
			
			$addon_info->colorset_tabfont = unserialize($data[extra_vars])->colorset_tabfont;
			//if (!$addon_info->colorset_tabfont) $addon_info->colorset_tabfont = '#ed1b2e;';
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
	
	function cancelMessage($message_srl) {
		global $connect, $prefix;

		$sql = sprintf("
SELECT related_srl
  FROM %smember_message
 WHERE message_srl = %s AND readed = 'N' ", 
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($message_srl));

		$result = mysql_query($sql, $connect);
		$related_srl = mysql_result($result, 0);
		
		if ($related_srl) {
		$sql = sprintf("
DELETE FROM %smember_message
 WHERE message_srl = %s ", 
 				mysql_real_escape_string($prefix),
				mysql_real_escape_string($related_srl));

		mysql_query($sql, $connect);
			
		$date = new DateTime();
		$cancel_date = $date->format('Y-m-d H:i:s');

		$sql = sprintf("
UPDATE %smember_message
   SET content = CONCAT(content, '%s'), readed = 'Y', readed_date = now()
 WHERE message_srl = %s ", 
 				mysql_real_escape_string($prefix),
 				mysql_real_escape_string('<br /> - Canceled('.$cancel_date.')'),
				mysql_real_escape_string($message_srl));

		mysql_query($sql, $connect);
			return true;
		} else {
			return false;
		}
	}
		 
	function starMessage($message_srl, $star) {
		global $connect, $prefix;

		if ($star == '☆') {
			$sql = sprintf("
UPDATE %smember_message			
   SET title = CONCAT('★', title) 
 WHERE message_srl = %s ",
 				mysql_real_escape_string($prefix),
 				mysql_real_escape_string($message_srl));
		} else {
			$sql = sprintf("
UPDATE %smember_message			
   SET title = SUBSTRING(title FROM 2 FOR LENGTH(title)) 
 WHERE message_srl = %s AND SUBSTRING(title FROM 1 FOR 1) = '★' ",
 				mysql_real_escape_string($prefix),
 				mysql_real_escape_string($message_srl));
		}
		
		mysql_query($sql, $connect);
	}
?>