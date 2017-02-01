<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk-free');
	$linkbreaks = "";
	if ($addon_info->send_mode == 'YES') {
		$linkbreaks = "(*".getLangInfo('msg_line_Breaks')." shift + enter)"; 
	}

	$target_srl = isset($_GET['target_srl']) ? $_GET['target_srl'] : '';
	if (!$target_srl) {
		$target_srl = isset($_POST['target_srl']) ? $_POST['target_srl'] : '';
	}
	
	$nick_name = isset($_GET['nick_name']) ? $_GET['nick_name'] : '';
	if (!$nick_name) {
		$nick_name = isset($_POST['nick_name']) ? $_POST['nick_name'] : '';
	}
	
	$profile_target_image = "images/message_read_19.png";
	$formatYou = "
	<div class=\"sm_message_you\">
		<img src=\"$profile_target_image\" class=\"r3\" style=\"position: absolute; bottom:0;left:10px; width:42px; height:42px;\">
		<img src=\"images/message_read_22.png\" style=\"position: absolute; bottom:10px;left:63px;z-index: 10;\">
		<ul>
			<li style=\"height:30px;\"><span class=\"sm_message_time\">%s</span></li>
			<li class=\"center_message r3\" >%s</li>
		</ul>
	</div> ";
	$formatMe = "
	<div class=\"sm_message_me\">
		<img src=\"%s\" class=\"r3\" style=\"position: absolute; bottom:0;right:10px; width:42px; height:42px;\">
		<img src=\"images/message_read_34.png\" style=\"position: absolute; bottom:10px;right:63px;z-index: 10;\">
		<ul>
			<li style=\"height:30px;\"><span class=\"sm_message_time\">%s</span></li>
			<li class=\"center_message r3\" >%s</li>
		</ul>
	</div> ";
	
	// 쪽지 개수 
	$readed = explode(',', getMessageStatus($member_srl, $target_srl));
	
	$result = getMessageDetail($member_srl, $target_srl);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo getLangInfo('messageTalk'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="stylesheet" type="text/css" href="css/default.css" />
<script language="javascript">
	function resize() {
		window.resizeTo(400, 550);
		document.message.memo.focus();
	}
	
	function setLine( txa ){
		line = 2; //기본 줄 수

		new_line = txa.value.split( "\n" ).length + 1;
	    if( new_line < line ) new_line = line;
	
	  	txa.rows = new_line;
   }
</script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
    <script type="text/javascript">
		function sendMessage(f)
		{
			if (trim(f.memo.value)) {
			    var memo=jQuery('#memo').val();
			    var target_srl=jQuery('#target_srl').val();
			    
			    $.post('sendMessage.php', {memo: $('textarea[name="memo"]').val(), target_srl : $('input[name="target_srl"]').val() }, function(resp){
			    	if (resp == 1) {
		    			f.submit(); 
			    	} else {
			    		alert('<?php echo getLangInfo('msg_no_input_messageTalk');?>');
			    	}  
				});
			} else {
				alert('<?php echo getLangInfo('msg_no_input_messageTalk');?>');
			}
		}
    </script>
<script LANGUAGE="Javascript">
function trim(str) {
        return str.replace(/^\s+|\s+$/g,"");
}

function processKey(f) 
{
	var enterKey = '<?php echo $addon_info->send_mode; ?>';
	
	// Ctrl & Shift 키가 눌러지지 않은 상태에서 ENTER-KEY를 눌렀을 때  
	if( (enterKey == 'YES' && event.ctrlKey == false && event.shiftKey == false && (event.keyCode == 13)) ) 
    {
    	// 공백과 enter-key 값을 제외한 내용이 있으면 
    	// 메시지 입력창을 비활성화한 뒤, 메시지톡 Send
    	if (trim(f.memo.value)) {
    		f.memo.disabled = true;
	    	sendMessage(f);
    	}
    } 
}
</script>
<?php echo colorset($addon_info->colorset_background, $addon_info->colorset_bar, $addon_info->colorset_tabfont); ?>
</head>
<body onload="resize()">
<div class="sm_box_detail">
	<div class="sm_top_detail">
		<a href="index.php"><img src="images/message_list_03.png" alt="쪽지"></a><span><a href="index.php"><img src="images/message_read_03.png" alt="back"></a></span>
	</div>
	<!--//sm_message_box 구간을 연속적으로 만들어 주세요.-->
	<?php
	while ($data = mysql_fetch_assoc($result)) {

		// update readed 
		postMessageReaded($data[message_srl], $member_srl);
		
		$date = new DateTime($data[regdate]);
		$msgDate = $date->format('Y-m-d H:i');
		
		if ($data[message_type] == "S") {
			echo sprintf($formatMe, "images/message_$data[readed]_31.png", $msgDate, auto_link(nl2br($data[content])));
		} else {
			echo sprintf($formatYou, $msgDate, auto_link(nl2br($data[content])));
		}
	}
	?>
	<a name='last'></a>
</div>
<form name="message" id="message" action="<?php echo $_SERVER['PHP_SELF']; ?>#last" method="post" enctype="multipart/form-data" onkeydown="processKey(this)">
<input type="hidden" name="target_srl" value="<?php echo $target_srl; ?>">
<input type="hidden" name="nick_name" value="<?php echo $nick_name; ?>">
<input type="hidden" name="auction" value="send">
<div class="sm_sendbox_bg">
	<div class="sm_name">
		<img src="images/message_read_07.png" alt="message icon"> <?php echo $nick_name."님"; ?>
		<a href="index.php"><img src="images/message_back_02.png" alt="back"></a><?php echo $linkbreaks; ?><!--?php echo $readed[0]  + $readed[1]; ?-->
	</div>
	<div class="sm_sendbox">
		<textarea name="memo" onKeyDown="setLine( this )" class="r3_box"></textarea>
		<input class="btn_submit" type="button" onclick="sendMessage(this.form)">
	</div>
</div>
</form>
</body>
</html>