<?php
	require_once "../lib/function.php";
	require_once "../lib/sql_message.php";
	require "../lib/Pusher.php";

	// 로그인 check
	$member_srl = getMemberInfo('no');
	if (!$member_srl) moveUrl("/");
	$now = new DateTime;
	
	$memo = isset($_POST['memo']) ? $_POST['memo'] : '';
	$target_srl = isset($_POST['target_srl']) ? $_POST['target_srl'] : '';
	if (!$target_srl) return;
	
	// 애드온 설정정보를 가져온다
	$addon_info = getAddonInfo(getSite_srl(), 'messageTalk');

	$profile_member_image = getProfileImage($member_srl);
	$msgDate = $now->format('H:i:s');
	$msgWeek = $now->format('D');

if(isset($_POST))
{
     //Some Settings
    $ThumbSquareSize        = 100; //Thumbnail will be 100x100
    $BigImageMaxSize        = 500; //Image Maximum height or width
    $ThumbPrefix            = "thumb_"; //Normal thumb Prefix
    $DestinationDirectory   = _XE_PATH_.'messageTalk/files/'; //Upload Directory ends with / (slash)
    $Quality                = 90;
    
    if (!is_dir($DestinationDirectory)) {
    	die('Error!! - Create Upload Directory ('.$DestinationDirectory.')');
    }
    
    if(!isset($_FILES['ImageFile']) || !is_uploaded_file($_FILES['ImageFile']['tmp_name']))
    {
    	die('Something went wrong with Upload!'); // output error when above checks fail.
    }

    // Random number for both file, will be added after image name
    $RandomNumber   = rand(0, 9999999999);

    $ImageName      = str_replace(' ','-',strtolower($_FILES['ImageFile']['name']));
    $ImageSize      = $_FILES['ImageFile']['size']; // Obtain original image size
    $TempSrc        = $_FILES['ImageFile']['tmp_name']; // Tmp name of image file stored in PHP tmp folder
    $ImageType      = $_FILES['ImageFile']['type']; //Obtain file type, returns "image/png", image/jpeg, text/plain etc.

    switch(strtolower($ImageType))
    {
        case 'image/png':
            $CreatedImage =  imagecreatefrompng($_FILES['ImageFile']['tmp_name']);
            break;
        case 'image/gif':
            $CreatedImage =  imagecreatefromgif($_FILES['ImageFile']['tmp_name']);
            break;
        case 'image/jpeg':
        case 'image/pjpeg':
            $CreatedImage = imagecreatefromjpeg($_FILES['ImageFile']['tmp_name']);
            break;
        default:
            die('Unsupported File!'); //output error and exit
    }

    list($CurWidth,$CurHeight)=getimagesize($TempSrc);

    $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
    $ImageExt = str_replace('.','',$ImageExt);

    $ImageName      = preg_replace("/\.[^.\s]{3,4}$/", "", $ImageName);

    $NewImageName = $ImageName.'-'.$RandomNumber.'.'.$ImageExt;
    $thumb_DestRandImageName    = $DestinationDirectory.$ThumbPrefix.$NewImageName; //Thumb name
    $DestRandImageName          = $DestinationDirectory.$NewImageName; //Name for Big Image

    if(resizeImage($CurWidth,$CurHeight,$BigImageMaxSize,$DestRandImageName,$CreatedImage,$Quality,$ImageType))
    {
        if(!cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$thumb_DestRandImageName,$CreatedImage,$Quality,$ImageType))
        {
            echo 'Error Creating thumbnail';
        }

        //Get New Image Size
        list($ResizedWidth,$ResizedHeight)=getimagesize($DestRandImageName);
		$memo = '<img src="../files/'.$ThumbPrefix.$NewImageName.'" height="'.$ThumbSquareSize.'" width="'.$ThumbSquareSize.'">';
		$memo.='<br /><a href="javascript:imageView(\'../files/'.$NewImageName.'\')">'.$NewImageName.'</a>';

        $message_srl = postMessage($member_srl, $target_srl, $memo);
        
		$formatMe = "
		<div class=\"sm_message_me\">
			<img id=\"profile$message_srl\" src=\"%s\" class=\"profile_img_me r3\">%s
			<img src=\"images/message_read_34.png\" style=\"position: absolute; bottom:10px;right:63px;z-index: 10;\">
			<ul>
				<li style=\"height:30px;\">
	    			<span class=\"sm_message_time r3_btn\">
	    			<span class=\"r3_day\">%s</span>
	    			 %s
	    			</span>%s%s
				</li>
				<li class=\"center_message r3\" >%s</li>
			</ul>
		</div> ";
		$starMessage = "<span id=\"star$message_srl\"><a href='javascript:starMessageTalk($message_srl,\"☆\", \"me\");'><img src=\"images/whitestar.icon_me.png\" class=\"profile_star_me\"></a></span>";
		echo sprintf($formatMe, "images/message_unread_31.png", $starMessage, $msgWeek, $msgDate, "<span id=\"$message_srl\" class=\"sm_message_del r3_btn\"><a href=\"javascript:cancelMessageTalk($message_srl);\">x</a></span>", "<span id=\"star$message_srl\" class=\"sm_message_del r3_btn\"><a href=\"javascript:starMessageTalk($message_srl, '☆');\">☆</a></span>", auto_link(nl2br($memo)));
		
		$pusher = new Pusher($addon_info->pusher_key, $addon_info->pusher_secret, $addon_info->pusher_app_id);
		$pusher->trigger(''.$target_srl.'', 'talk', array('message_srl' => '' .$message_srl. '', 'target_srl' => ''.$member_srl.'') );
    }else{
    	die('Resize Error'); //output error
    }
}

function resizeImage($CurWidth,$CurHeight,$MaxSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0)
    {
        return false;
    }

    //Construct a proportional size of new image
    $ImageScale         = min($MaxSize/$CurWidth, $MaxSize/$CurHeight);
    $NewWidth           = ceil($ImageScale*$CurWidth);
    $NewHeight          = ceil($ImageScale*$CurHeight);

    if($CurWidth < $NewWidth || $CurHeight < $NewHeight)
    {
        $NewWidth = $CurWidth;
        $NewHeight = $CurHeight;
    }
    $NewCanves  = imagecreatetruecolor($NewWidth, $NewHeight);
    // Resize Image
    if(imagecopyresampled($NewCanves, $SrcImage,0, 0, 0, 0, $NewWidth, $NewHeight, $CurWidth, $CurHeight))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png':
                imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif':
                imagegif($NewCanves,$DestFolder);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
    //Destroy image, frees up memory
    if(is_resource($NewCanves)) {imagedestroy($NewCanves);}
    return true;
    }
}

function cropImage($CurWidth,$CurHeight,$iSize,$DestFolder,$SrcImage,$Quality,$ImageType)
{
    //Check Image size is not 0
    if($CurWidth <= 0 || $CurHeight <= 0)
    {
        return false;
    }

    if($CurWidth>$CurHeight)
    {
        $y_offset = 0;
        $x_offset = ($CurWidth - $CurHeight) / 2;
        $square_size    = $CurWidth - ($x_offset * 2);
    }else{
        $x_offset = 0;
        $y_offset = ($CurHeight - $CurWidth) / 2;
        $square_size = $CurHeight - ($y_offset * 2);
    }

    $NewCanves  = imagecreatetruecolor($iSize, $iSize);
    if(imagecopyresampled($NewCanves, $SrcImage,0, 0, $x_offset, $y_offset, $iSize, $iSize, $square_size, $square_size))
    {
        switch(strtolower($ImageType))
        {
            case 'image/png':
                imagepng($NewCanves,$DestFolder);
                break;
            case 'image/gif':
                imagegif($NewCanves,$DestFolder);
                break;
            case 'image/jpeg':
            case 'image/pjpeg':
                imagejpeg($NewCanves,$DestFolder,$Quality);
                break;
            default:
                return false;
        }
    //Destroy image, frees up memory
    if(is_resource($NewCanves)) {imagedestroy($NewCanves);}
    return true;
    }
}
?>