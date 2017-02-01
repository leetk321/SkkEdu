<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 쪽지 미리보기 내용 길이 지정
	$content_length = 30;
	// 로그인 여부
	$member_srl = getMemberInfo('no');
	
	if (!$member_srl) moveUrl("/");

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
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<title>MESSAGE</title>
	<link rel="stylesheet" type="text/css" href="css/default.css" />
	<script type="text/javascript">
		function resize() {
			window.resizeTo(400, 550);
		}
	</script>
	<style>
		.sm_message_box .friend_li{padding: 10px 0;}
		.friend_select{padding:4px 5px; border:1px solid #eee; color:#333;}
		.friend_text{border:1px solid #eee; padding:5px 5px; color:#333; width: 180px;}
		.search_btn{padding:7px 13px; color: #fff;/*칼라셋*/background-color: #ed1b2e; margin-left: 3px;}
	</style>
</head>
<body onload="resize()">
<div class="sm_box">
	<div class="sm_top r3_color">
		<img src="images/message_list_03.png" alt="쪽지">
		<div class="message_btn">
			<a href="index.php"><span class="r3_btn <?php echo $classAll; ?>">전체</span></a><a href="index.php?type=NEW"><span class="r3_btn <?php echo $classNew; ?>">안읽은 메세지</span><a href="friend_search.php"><span class="r3_btn <?php echo $classAll; ?>">친구찾기</span></a>
		</div>
	</div>
	<form>
	<div class="sm_message_box r3">
		<ul>
			<li class="friend_li">
				<select class="friend_select">
					<!--//xe 로그인 방식이 아이디나 이메일로 선택이 가능함 그래서 아이디 또는 이메일을 로그인방식에 맞게 해야할듯 -->
					<option>아이디</option>
					<option>닉네임</option>
					<option>이메일</option>
				</select>
				<input type="text" value="" class="friend_text">
				<span class="r3_btn search_btn">검색</span>
			</li>
		</ul>
	</div>
	<?php
	$i = 0;
	while ($data = mysql_fetch_assoc($result)) {
		$format = "<a href='detail.php?target_srl=%s&nick_name=%s#last'>%s</a>";
		// 쪽지 안읽은개수, 읽은개수 
		$readed = explode(',', getMessageStatus($member_srl, $data[member_srl]));
			
		$i++;
	?>
	<!--//sm_message_box 구간을 연속적으로 만들어 주세요.-->
	<div class="sm_message_box r3">
		<ul>
			<li class="friend_li">
				<img src="images/message_list_08.png" alt="id icon"> <a href=""><?php echo $data[nick_name]; ?></a>
				<div style="float: right;">
				<!--//부열-해당 친구찾기를 했을때 나와 쪽지를 보낸 내역이 있다면 출력-->	
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
	<!--//해당 아이디/닉네임이 없을떄-->
	<div class="sm_message_box r3">
		<ul>
			<li class="friend_li" style="text-align: center;">
				해당 아이디를 찾을수 없습니다.
			</li>
		</ul>
	</div>
	<?php } ?>
	<!--//메세지가 없을떄-->
	<div class="sm_bottom r3_color">
	</div>
	</form>
</div>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
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
