<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";
	// 쪽지 미리보기 내용 길이 지정
	$content_length = 28;
	
	// 로그인 여부
	$member_srl = getMemberInfo('no');
	
	if (!$member_srl) moveUrl("/");

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk-free');
	
	// 메시지 구분
	$type = isset($_GET['type']) ? $_GET['type'] : '';

	if ($type == 'NEW') {
		$result = getNewMessageList($member_srl);
		$classAll = 'r3_btn';
		$classNew = 'message_btn_bold';
	} else {
		$result = getMessageList($member_srl);
		$classAll = 'message_btn_bold';
		$classNew = 'r3_btn';
	}
	if (!$license) $license = getLicense();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title><?php echo getLangInfo('messageTalk'); ?></title>
	<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript">
	function resize() {
		window.resizeTo(400, 550);
	}
</script>
<?php echo colorset($addon_info->colorset_background, $addon_info->colorset_bar, $addon_info->colorset_tabfont); ?>
</head>
<body onload="resize()">
<div class="sm_box">
	<div class="sm_top">
	    <div class="copyright"><a href="http://www.2sisstore.com/index.php?mid=xeskin&document_srl=26651" target="_blank" style="position: absolute; top:2px; right:10px; color:#fff;">copyright @2sisstore.com</a></div>
		<img src="images/message_list_03.png" alt="쪽지">
		<div class="message_btn">
			<a href="index.php"><span class="r3_btn <?php echo $classAll; ?>"><?php echo getLangInfo('cmd_view_messageTalk_All'); ?></span></a><a href="index.php?type=NEW"><span class="r3_btn <?php echo $classNew; ?>"><?php echo getLangInfo('cmd_view_messageTalk_New'); ?></span></a><a href="findFriends.php"><span class="r3_btn"><?php echo getLangInfo('cmd_view_messageTalk_FindFriends'); ?></span></a>
		</div>
	</div>
	<?php
	$i = 0;
	while ($data = mysql_fetch_assoc($result)) {
		$format = "<a href='detail.php?target_srl=%s&nick_name=%s#last'>%s</a>";
		//테그 제거, 한줄 미리보기
		$memo = strip_tags(getMessage($data[msgSrl]));
		$memo = str_cut_string($memo, $content_length);
		$nickname = sprintf($format, $data[member_srl], $data[nick_name], $data[nick_name]);
		$content = sprintf($format, $data[member_srl], $data[nick_name], $memo);

		// 날짜
		$date = new DateTime($data[msgDate]);
		$msgDate = $date->format('Y-m-d H:i:s (l)');

		// 쪽지 안읽은개수, 읽은개수 
		$readed = explode(',', getMessageStatus($member_srl, $data[member_srl]));
			
	
		$i++;
	?>
	<form>
	<!--//sm_message_box 구간을 연속적으로 만들어 주세요.-->
	<div class="sm_message_box">
		<ul>
			<li>
				<img src="images/message_list_08.png"><?php echo $nickname; ?>
				<span class="del_checkbox">
					<input type="checkbox" name="memberlist[]" value="<?php echo $data[member_srl]; ?>" />
				</span>
			</li>
			<li>
				<img src="images/message_list_11.png" alt="enter icon"><?php echo $content; ?> 
			</li>
			<li class="bottom_time">
				<img src="images/message_list_13.png" alt="watch icon"> <?php echo $msgDate; ?>
				<div class="message_counter">
					<!--//새로운 메세지가 있을때 출력-->
					<?php if ($readed[1] > 0) { ?> 
					<span class="new_message"><img src="images/message_list_15.png" alt="new message"> <?php echo $readed[1]; ?></span>
					<?php } ?>
					<!--//새로운 메세지가 있을때 출력-->
					<span><img src="images/message_list_17.png" alt="total message"> <?php echo $readed[0] + $readed[1]; ?></span>
				</div>
			</li>
		</ul>
	</div>
	<!--//sm_message_box 구간을 연속적으로 만들어 주세요 end-->
	<?php
	}
	?>
	<?php if ($i == 0) { ?>
	<!--//메세지가 없을떄-->
	<div class="sm_message_box">
		<ul>
			<li class="friend_li" style="text-align: center;">
				<?php echo getLangInfo(msg_no_messageTalk); ?>
			</li>
		</ul>
	</div>
	<?php } ?>
	<!--//메세지가 없을떄-->
	<div class="sm_bottom">
		<input type="checkbox" id="check_all" name="check_all" class="checkbox"/> <input id="btn_delete" class="btn_submit" type="button" onclick="deleteMessage(this.form)" >
	</div>
	</form>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
	$("#check_all").click(function(){
		var checkedValue = $(this).prop("checked");
		
		$("input[name=memberlist\\[\\]]").each(function()    
		{    
		    this.checked = checkedValue;    
		});
	});

	function deleteMessage(f)
	{
	    // checkBox 배열을 post하기 위해...
	    var data = { 'memberlist[]' : []};
	    $(":checked").each(function() {
	    	data['memberlist[]'].push($(this).val());
	    })
	    
	    $.post('deleteMessage.php', data, function(resp){
	    	alert(resp);
	    	location.href = 'index.php';
	});
	}
</script>
</body>
</html>