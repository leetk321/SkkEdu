<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";

	// 쪽지 미리보기 내용 길이 지정
	$content_length = 30;
	// 로그인 여부
	$member_srl = getMemberInfo('no');
	
	if (!$member_srl) moveUrl("/");

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');
	$domain = getSite_domain();
	if (!$license) $license = getLicense($domain);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=0.85, minimum-scale=0.85, maximum-scale=0.85, user-scalable=yes, target-densitydpi=medium-dpi" />
	<title><?php echo getLangInfo('messageTalk'); ?></title>
	<link rel="stylesheet" type="text/css" href="css/default.css" />
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script type="text/javascript">
		function trim(str) {
		        return str.replace(/^\s+|\s+$/g,"");
		}
		
		function findFriends(f)
		{	
			var findoption = f.findOptions[f.findOptions.selectedIndex].value;
			var keyword = f.keyword.value;
			if (!trim(keyword)) {
				alert('<?php echo getLangInfo('msg_no_FindFriends_Keyword');?>');
				return;
			}
			
		    $.post('findingFriends.php', {findoption: findoption, keyword : keyword }, function(resp){
		    	$("#findResult").show();
		    	$("#findResult").html(resp);
			});
		}
	</script>
	<script type="text/javascript">
		function processKey(f) 
		{
			if( event.keyCode == 13 ) 
		    {
		    	findFriends(f);
			    return false;
		    } 
		}
		
		function resize() {
			window.resizeTo(400, 550);
			
			$("#findResult").hide();
		}
	</script>
</head>
<?php echo colorset($addon_info->colorset_background, $addon_info->colorset_bar, $addon_info->colorset_tabfont); ?>
<body onload="resize()">
<div class="sm_box">
    <div class="sm_top r3_color">
        <!--//pro-->
        <!-- <img src="images/message_list_03.png" alt="쪽지" class="message_title"> -->
        <!--//free start-->
        <style>
             .sm_top{position:relative;height:80px; width: 355px; padding:0px; margin:0 auto;*margin-bottom:5px; *z-index:1;}
             .message_btn{position: absolute; right:3px; bottom:0; color: #777; z-index:1000;}
             .iframe_box{position:fixed; top:5px; z-index:1; margin:0;}
        </style>
        <div id="iframe_box" class="iframe_box">
        <iframe src="http://www.2sisstore.com/support/messageTalk/head/index.php?domain=<?php echo $domain; ?>" frameborder="0" width="355px" height="80" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true"></iframe>
        </div>
        <!--//free end-->
        <div class="message_btn">
            <a href="index.php"><span class="r3_tab <?php echo $classAll; ?>"><?php echo getLangInfo('cmd_view_messageTalk_All'); ?></span></a><a href="index.php?type=NEW"><span class="r3_tab <?php echo $classNew; ?>"><?php echo getLangInfo('cmd_view_messageTalk_New'); ?></span></a><a href="findFriends.php"><span class="r3_tab"><?php echo getLangInfo('cmd_view_messageTalk_FindFriends'); ?></span></a>
        </div>
    </div>
	<form onkeydown="return processKey(this)">
	<div class="sm_message_box r3">
		<ul>
			<li class="friend_li">
				<select id="findOptions" class="friend_select">
					<!--//xe 로그인 방식이 아이디나 이메일로 선택이 가능함 그래서 아이디 또는 이메일을 로그인방식에 맞게 해야할듯 -->
					<option value="id"><?php echo getLangInfo('msg_id_FindFriends'); ?></option>
					<option value="nickname"><?php echo getLangInfo('msg_nickname_FindFriends'); ?></option>
					<option value="email"><?php echo getLangInfo('msg_email_FindFriends'); ?></option>
				</select>
				<input id="keyword" type="text" value="" class="friend_text">
				<!--<a href="#" onclick="findFriends(this.form)"><span class="r3_btn search_btn"><?php echo getLangInfo('cmd_search_FindFriends'); ?></span></a>-->
				<input class="r3_btn search_btn" type="button" onclick="findFriends(this.form)" value="<?php echo getLangInfo('cmd_search_FindFriends'); ?>">
			</li>
		</ul>
	</div>
	<div id="findResult"></div>
	
	<div class="sm_bottom r3_color">
	</div>
	</form>
</div>
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
