<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";
	require "../lib/Pusher.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");

	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');
	// 메시지 구분
	$type = isset($_GET['type']) ? $_GET['type'] : '';

	$cmd_view_messageTalk_All = getLangInfo('cmd_view_messageTalk_All');
	$cmd_view_messageTalk_New = getLangInfo('cmd_view_messageTalk_New');
	$cmd_view_messageTalk_FindFriends = getLangInfo('cmd_view_messageTalk_FindFriends');
	$msg_confirm_cancel_error_messageTalk = getLangInfo('msg_confirm_cancel_error_messageTalk');
	if ($type == 'NEW') {
		$classAll = 'r3_btn';
		$classNew = 'message_btn_bold';
	} else {
		$classAll = 'message_btn_bold';
		$classNew = 'r3_btn';
	}
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
	$star = isset($_GET['star']) ? $_GET['star'] : 'N';
	
    // profile Image
    $profile_member_image = getProfileImage($member_srl);
    if (!$profile_member_image) {
    	$profile_member_image = "images/message_readed_31.png";
    }
	// 쪽지 개수 
	$readed = explode(',', getMessageStatus($member_srl, $target_srl));
	$messageCount = $readed[0]  + $readed[1];
	
	$msg = getLangInfo('msg_confirm_cancel_messageTalk');
	$domain = getSite_domain();
	if (!$license) $license = getLicense($domain);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo getLangInfo('messageTalk').'::'.$nick_name; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=0.85, minimum-scale=0.85, maximum-scale=0.85, user-scalable=yes, target-densitydpi=medium-dpi" />
	<link rel="stylesheet" type="text/css" href="css/default.css" />
	<style>
		.chromefixfixed #drawer {
		    display: none;
		}
		.fixfixed #drawer {
		    bottom: 0;
		    margin-left: auto;
		    margin-right: auto;
		    position: absolute;
		    left: 0;
		    right: 0; }
	</style>
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="js/pusher.min.js"></script>
	<script language="javascript">
		function iOSversion() {
		    if (/iP(hone|od|ad)/.test(navigator.platform)) {
		        // supports iOS 2.0 and later: <http://bit.ly/TJjs1V>
		        var v = (navigator.appVersion).match(/OS (\d+)_(\d+)_?(\d+)?/);
		        return [parseInt(v[1], 10), parseInt(v[2], 10), parseInt(v[3] || 0, 10)];
		    }
		}
		var iosVersion = iOSversion();
		
		function resize() {
			window.resizeTo(400, 600);
			document.message.memo.focus();
		}
		
		function setLine( txa ){
			line = 2; //기본 줄 수
			if (iosVersion[0] == '7') {
				line = line + 1;
			}
			new_line = txa.value.split( "\n" ).length + 1;
			
			if(line > new_line) { 
			    new_line = line; 
			    txa.rows = new_line; 
			}
			if (parseInt(txa.value.length / 20) > 0) {
				txa.rows = new_line + parseInt(txa.value.length / 20);
			}
	   }
	   
	   function sendMessage(f)
		{
			if (trim(f.memo.value)) {
			    var memo=jQuery('#memo').val();
			    var target_srl=jQuery('#target_srl').val();
			    
			    $.post('sendMessage.php', {memo: $('textarea[name="memo"]').val(), target_srl : $('input[name="target_srl"]').val() }, function(resp){
			    	f.memo.value = '';
		    		f.memo.disabled = false;
		    		
			    	if ($.trim(resp)) {
						var htmlStr = jQuery('#messageTalk').html();
						jQuery('#messageTalk').html(htmlStr+resp);
						var $target = $('html, body');
						$target.animate({scrollTop: $target.height() * 10}, 1000);
						if (iosVersion[0] != '7') {
							document.message.memo.focus();
						} else {
							document.message.btn_submit.focus();
							document.message.memo.rows = 2;
						}
			    	} else {
			    		alert('<?php echo getLangInfo('msg_no_input_messageTalk');?>');
			    	}  
				});
			} else {
				alert('<?php echo getLangInfo('msg_no_input_messageTalk');?>');
			}
		}
		
		function getMessage(messageType)
		{
			var pos = $('input[name="limitpos"]').val();
			var message_count;
			if (parseInt(pos) == 0) {
				message_count = <?php echo $addon_info->show_message_default; ?>;
			} else {
				message_count = <?php echo $addon_info->show_message_more; ?>;
			}
			$.get('getMessage.php', {pos : $('input[name="limitpos"]').val(), message_count : message_count, messageType : messageType, target_srl : "<?php echo $target_srl; ?>", nick_name : "<?php echo $nick_name; ?>"}, function(resp) {
				if (messageType == "star") {
					jQuery('#showMoreMessage').html('');
					jQuery('#messageTalk').html(resp);
					var $target = $('html, body');
					$target.animate({scrollTop: $target.height() * 10}, 1000);
					
				} else {
					var htmlStr = jQuery('#messageTalk').html();
					jQuery('#messageTalk').html(resp + htmlStr);
	
					var nPos = parseInt(pos) + parseInt(message_count);
					$('input[name="limitpos"]').val(nPos);
					if (pos == 0) {
						var $target = $('html, body');
						$target.animate({scrollTop: $target.height() * 10}, 1000);
						//$(document).scrollTop( $("#last").offset().top);
					}
					if (parseInt($('input[name="limitpos"]').val()) >= parseInt($('input[name="messagecount"]').val()) ) {
						jQuery('#showMoreMessage').html('');
					} 
				}
			});
		}
	</script>
	<script type="text/javascript">
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
	
	function cancelMessageTalk(message_srl) {
		
		var msg = '<?php echo $msg; ?>';
		var answer = confirm(msg);
		
		if (answer) {
		    $.post('cancelMessage.php', {message_srl: message_srl}, function(resp){
		    	if ($.trim(resp)=="SUCCESS") {
					jQuery('#' + message_srl).html(' Canceled ');
		    	} else {
		    		alert('<?php echo $msg_confirm_cancel_error_messageTalk; ?>');
		    	}  
			});
		}
	} 
	
	function starMessageTalk(message_srl, star, youme) {
	    $.post('starMessage.php', {message_srl: message_srl, star: star, youme : youme}, function(resp){
	    	var cstar;
	    	if (star == '☆') {
	    		cstar = '★'; 
	    	} else {
	    		cstar = '☆';
	    	}
	    	jQuery('#star' + message_srl).html('<a href="javascript:starMessageTalk(' + message_srl + ', \'' + cstar + '\' , \'' + youme + '\');">' + $.trim(resp) + '</a>');
		});
	} 
	
	function getMessageRealtime() {
		$.get('getMessage.php', {pos : "0", message_count : "1", target_srl : "<?php echo $target_srl; ?>", nick_name : "<?php echo $nick_name; ?>"}, function(resp) {
			var htmlStr = jQuery('#messageTalk').html();
			jQuery('#messageTalk').html(htmlStr + resp);
			var $target = $('html, body');
			$target.animate({scrollTop: $target.height() * 10}, 500);
		});
	}
	</script>

	<script type="text/javascript" src="./js/jquery.form.js"></script>
 	<script>
        $(document).ready(function() {
        //elements
        var progressbox     = $('#progressbox');
        var progressbar     = $('#progressbar');
        var statustxt       = $('#statustxt');
        var submitbutton    = $("#SubmitButton");
        var myform          = $("#UploadForm");
        var completed       = '0%';

            $(myform).ajaxForm({
                beforeSend: function() { //brfore sending form
                    submitbutton.attr('disabled', ''); // disable upload button
                    statustxt.empty();
                    progressbox.slideDown(); //show progressbar
                    progressbar.width(completed); //initial value 0% of progressbar
                    statustxt.html(completed); //set status text
                    statustxt.css('color','#000'); //initial color of status text
                },
                uploadProgress: function(event, position, total, percentComplete) { //on progress
                    progressbar.width(percentComplete + '%') //update progressbar percent complete
                    statustxt.html(percentComplete + '%'); //update status text
                    if(percentComplete>50)
                        {
                            statustxt.css('color','#fff'); //change status text to white after 50%
                        }
                    },
                complete: function(response) { // on complete
					var htmlStr = jQuery('#messageTalk').html();
					jQuery('#messageTalk').html(htmlStr+response.responseText);
					var $target = $('html, body');
					$target.animate({scrollTop: $target.height() * 10}, 1000);
					//$(document).scrollTop( $("#last").offset().top);

                    myform.resetForm();  // reset form
                    submitbutton.removeAttr('disabled'); //enable submit button
                    progressbox.slideUp(); // hide progressbar
                }
	        });
        });
    </script>
	<link href="./css/upload.css" rel="stylesheet" type="text/css" />
	
	<script> 
		var imgHeight = 500;
		var imgWidth = 500;
		
		function imgOnLoad() {
			imgWidth = this.width;
			imgHeight = this.height; 
			return true;
		}
		
		function imageView(imgsrc){
			var imgTmp = new Image();
	        imgTmp.onload = imgOnLoad;
	        imgTmp.src = imgsrc;

			setTimeout(function(){
		        imgView = window.open("","imageView","width="+imgWidth+",height="+imgHeight+",status=no,toolbar=no,scrollbars=no,resizable=no");
		        imgView.document.open(); 
		        imgView.document.write("<html><title>messageTalk::Image view</title>" 
		        +"<body topmargin=0 leftmargin=0 marginheight=0 marginwidth=0>" 
		        +"<a href=javascript:self.close()><img src="+imgsrc+" width="+imgWidth+" height="+imgHeight+" border=0></a>" 
		        +"</body></html>");
		        imgView.document.close(); 
			}, 500);
		} 
	</script> 
<script type="text/javascript">
	function talkAudio() {
		jQuery('audio').get(0).play();
	}
	var pusher = new Pusher('<?php echo $addon_info->pusher_key; ?>');
	var channel = pusher.subscribe('<?php echo $member_srl; ?>');

	channel.bind('talk', function(data) {
		if (data.target_srl == '<?php echo $target_srl; ?>') {
			getMessageRealtime();
			$.post('readMessage.php', {message_srl: data.message_srl, target_srl: "<?php echo $target_srl; ?>" }, function(resp){
			
			});
			setTimeout("talkAudio()", 0);
			setTimeout("talkAudio()", 500);
		}
  		//alert('An event was triggered with messag22e: ' + data.message);
		//var htmlStr = jQuery('#messageTalk').html();
		//jQuery('#messageTalk').html(htmlStr + data.message);
	});
	channel.bind('read', function(data) {
		jQuery('#' + data.message_srl).remove();
		jQuery('#profile' + data.message_srl).attr('src', '<?php echo $profile_member_image; ?>');
	});
</script>
<?php echo colorset($addon_info->colorset_background, $addon_info->colorset_bar, $addon_info->colorset_tabfont); ?>
</head>
<body onload="resize()">
<audio id="audio" src="../lib/talk.wav" style="display:none;"></audio> 
<div class="sm_box_detail">
	<div class="sm_top r3_color">
        <style>
             .sm_top{position:relative;height:80px; width: 355px; padding:0px; margin:0 auto;*margin-bottom:5px; *z-index:1;}
             .message_btn{position: absolute; right:3px; bottom:0; color: #777; z-index:1000;}
             .iframe_box{position:fixed; top:5px; z-index:55; margin:0;}
        </style>
        <div id="iframe_box" class="iframe_box">
        <iframe src="http://www.2sisstore.com/support/messageTalk/head/index.php?domain=<?php echo $domain; ?>" frameborder="0" width="355px" height="80" marginwidth="0" marginheight="0" scrolling="no" allowtransparency="true"></iframe>
        </div>
		<div id="talk_tab" class="message_btn">
			<a href="index.php"><span class="r3_tab <?php echo $classAll; ?>"><?php echo $cmd_view_messageTalk_All; ?></span></a><a href="index.php?type=NEW"><span class="r3_tab <?php echo $classNew; ?>"><?php echo $cmd_view_messageTalk_New; ?></span></a><a href="findFriends.php"><span class="r3_tab"><?php echo $cmd_view_messageTalk_FindFriends; ?></span></a>
		</div>
	</div>
	<?php
		if ($messageCount > $addon_info->show_message_default) {
			echo "<div id=\"showMoreMessage\"><div class=\"showMoreMessage r3_btn\"><a href='#' onclick=\"getMessage('')\">이전 메시지 보기</a></div></div>";
		}
	?>
	<div id="messageTalk"></div>
	<a id='last'></a>
</div>
<div id="sm_sendbox_bg" class="sm_sendbox_bg">
    <div id="progressbox" style=""><div id="progressbar"></div ><div id="statustxt">0%</div ></div>
    <form action="processupload.php" method="post" enctype="multipart/form-data" id="UploadForm">
	<div class="sm_name">
        <input type="hidden" name="target_srl" value="<?php echo $target_srl; ?>">
        <input name="ImageFile" type="file" onchange="document.getElementById('SubmitButton').click();" accept="image/gif, image/jpeg, image/png, image/pjpeg" class="filebtn"/>
        <span class="filebtn_icon"><img src="images/photo.png" alt="photo" title="Photo File"></span>&nbsp;
        <input type="submit"  id="SubmitButton" value="Upload" style="display:none;" />
        <a href="#last" onclick="getMessage('star')"><img src="images/favorite.png" alt="favorite"></a>&nbsp;
        <!--img src="images/sticker.png" alt="sticker"-->
		<a href="index.php"><img src="images/back.png" alt="back" title="Back"></a><span style="vertical-align: 50%; ">&nbsp;<?php echo $linkbreaks; ?></span>
	</div>
    </form>
    <form name="message" id="message" action="<?php echo $_SERVER['PHP_SELF']; ?>#last" method="post" enctype="multipart/form-data" onkeydown="processKey(this)">
        <input type="hidden" name="target_srl" value="<?php echo $target_srl; ?>">
        <input type="hidden" name="nick_name" value="<?php echo $nick_name; ?>">
        <input type="hidden" name="auction" value="send">
        <input type="hidden" name="limitpos" value=0>
        <input type="hidden" name="messagecount" value=<?php echo $messageCount; ?>>
    	<div class="sm_sendbox">
    		<textarea name="memo" onKeyDown="setLine( this )" class="r3_box"></textarea>
    		<input id="btn_submit" class="btn_submit" type="button" onclick="sendMessage(this.form)">
    	</div>
	</form>
</div>
<script>
	getMessage('');
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