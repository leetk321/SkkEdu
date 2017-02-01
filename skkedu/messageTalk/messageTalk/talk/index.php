<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";
	
	// 쪽지 미리보기 내용 길이 지정
	$content_length = 28;
	
	// 로그인 여부
	$member_srl = getMemberInfo('no');
	
	if (!$member_srl) moveUrl("/");

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');
	
	// 메시지 구분
	$type = isset($_GET['type']) ? $_GET['type'] : '';

	$cmd_view_messageTalk_All = getLangInfo('cmd_view_messageTalk_All');
	$cmd_view_messageTalk_New = getLangInfo('cmd_view_messageTalk_New');
	$cmd_view_messageTalk_FindFriends = getLangInfo('cmd_view_messageTalk_FindFriends');
	if ($type == 'NEW') {
		$result = getNewMessageList($member_srl);
		$classAll = 'r3_btn';
		$classNew = 'message_btn_bold';
	} else {
		$result = getMessageList($member_srl);
		$classAll = 'message_btn_bold';
		$classNew = 'r3_btn';
	}
	$domain = getSite_domain();
	$msg = getLangInfo('msg_confirm_deleted_messageTalk');
	if (!$license) $license = getLicense($domain);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo getLangInfo('messageTalk'); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=0.85, minimum-scale=0.85, maximum-scale=0.85, user-scalable=yes, target-densitydpi=medium-dpi" />
	<link rel="stylesheet" type="text/css" href="css/default.css" />
<script type="text/javascript">
	function resize() {
		window.resizeTo(400, 600);
	}
</script>
<?php echo colorset($addon_info->colorset_background, $addon_info->colorset_bar, $addon_info->colorset_tabfont); ?>
</head>
<body onload="resize()">
<div class="sm_box">
	<div class="sm_top r3_color">
        <style>
             .sm_top{position:relative;height:80px; width: 355px; padding:0px; margin:0 auto;*margin-bottom:5px; *z-index:1;}
             .message_btn{position: absolute; right:3px; bottom:0; color: #777;}
             .iframe_box{position:fixed; top:5px; z-index:1000; margin:0;}
        </style>
        <div id="iframe_box" class="iframe_box">
        <iframe src="http://www.2sisstore.com/support/messageTalk/head/index.php?domain=<?php echo $domain; ?>" frameborder="0" width="355px" height="80" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true"></iframe>
        </div>
		<div id="talk_tab" class="message_btn">
			<a href="index.php"><span class="r3_tab <?php echo $classAll; ?>"><?php echo $cmd_view_messageTalk_All; ?></span></a><a href="index.php?type=NEW"><span class="r3_tab <?php echo $classNew; ?>"><?php echo $cmd_view_messageTalk_New; ?></span></a><a href="findFriends.php"><span class="r3_tab"><?php echo $cmd_view_messageTalk_FindFriends; ?></span></a>
		</div>
	</div>
	<?php
	$i = 0;
	while ($data = mysql_fetch_assoc($result)) {
		$format = "<a href='detail.php?target_srl=%s&nick_name=%s#last'>%s</a>";
		//테그 제거, 한줄 미리보기
		$memo = strip_tags(getMessage($data[msgSrl]));
		$memo = str_cut_string($memo, $content_length, '...');
		$nickname = sprintf($format, $data[member_srl], $data[nick_name], $data[nick_name]);
		$content = sprintf($format, $data[member_srl], $data[nick_name], $memo);

		// 날짜
		$date = new DateTime($data[msgDate]);
		$msgDate = $date->format('Y-m-d H:i:s (l)');

		// 쪽지 안읽은개수, 읽은개수 
		$readed = explode(',', getMessageStatus($member_srl, $data[member_srl]));
		$starMessageCount = getStarMessageCount($member_srl, $data[member_srl]);
			
		// profile Image
		$profile_image = getProfileImage($data[member_srl]);
		if(!$profile_image) {
			//프로필사진이 없을때
			$profile_html = "<li><img src=\"images/message_readed_31.png\" class=\"r3 sm_profile\"></li>";
		} else {
			//프로필사진이 있을때
            $profile_html = "<li class=\"sm_profile_box\"><img src=\"$profile_image\" class=\"r3 sm_profile\"></li>";
		}
		
		$i++;
	?>
	<form>
	<!--//sm_message_box 구간을 연속적으로 만들어 주세요.-->
    <div class="sm_message_box r3">
        <ul>
            <?php echo $profile_html; ?>
            <li class="nickname">
                <?php echo $nickname; ?>
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
                    <!--//별 메세지가 있을때 출력-->
                    <?php 
                    	if ($starMessageCount > 0) { echo "<span><img src=\"images/star_icon.png\" alt=\"favorite\"> $starMessageCount </span>"; } 
                    ?>
                    <!--//새로운 메세지가 있을때 출력-->
                    <?php 
                    	if ($readed[1] > 0) { echo "<span class=\"new_message\"><img src=\"images/message_list_15.png\" alt=\"new message\"> $readed[1] </span>"; } 
                    ?>
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
	<div class="sm_message_box r3">
		<ul>
			<li class="friend_li" style="text-align: center;">
				<?php echo getLangInfo(msg_no_messageTalk); ?>
			</li>
		</ul>
	</div>
	<?php } ?>
	<!--//메세지가 없을떄-->
	<div class="sm_bottom r3_color">
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
		var msg = '<?php echo $msg; ?>';
		var answer = confirm(msg);
		
		if (answer) {
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
	}
</script>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery(window).scroll(function() {
			if (jQuery(window).scrollTop() > 0) {
				<?php
					if ($license == "Y") { 
				?>
					jQuery('#iframe_box').attr("class", "");
				<?php
					} else {
				?>
					jQuery('#talk_tab').html("");
				<?php
					}
				?>
			} else if (jQuery(window).scrollTop() == 0) {
				jQuery('#talk_tab').html("<a href=\"index.php\"><span class=\"r3_tab <?php echo $classAll; ?>\"><?php echo $cmd_view_messageTalk_All; ?></span></a><a href=\"index.php?type=NEW\"><span class=\"r3_tab <?php echo $classNew; ?>\"><?php echo $cmd_view_messageTalk_New; ?></span></a><a href=\"findFriends.php\"><span class=\"r3_tab\"><?php echo $cmd_view_messageTalk_FindFriends; ?></span></a>");
			}
		});
	});
</script>
</body>
</html>