<?php
	define('__XE__',true);
	define('__ZBXE__',true);
	define('_XE_PATH_', str_replace('messageTalk_free/lib/function.php', '', str_replace('\\', '/', __FILE__)));
	require_once(_XE_PATH_."config/config.inc.php");
	//require_once("prepare.php");
	
	function getMemberInfo($information) {
		
		$oContext = &Context::getInstance();
		$oContext->init();
		$logged_info = Context::get('logged_info');
		
		$getInfo = "";
		
		switch ($information) {
			case 'no' : 
				$getInfo = $logged_info->member_srl; 
			break;
			
			case 'id' : 
				$getInfo = $logged_info->user_id; 
			break;
			
			case 'user_name' : 
				$getInfo = $logged_info->user_name; 
			break;
			
			case 'nick_name' : 
				$getInfo = $logged_info->nick_name; 
			break;
			
			case 'email_address' : 
				$getInfo = $logged_info->email_address; 
			break;
			
			case 'phone' :
			echo "phone function";
				echo $logged_info->extra_vars;
				$getInfo = unserialize($logged_info->extra_vars)->phone;
			break;
		}
		
		return $getInfo;
	}

	function getLangInfo($item) {
		$oContext = &Context::getInstance();
		$oContext->init();
		Context::loadLang('../lang');
		return Context::getLang($item);
	}
	
	function getLangInfoAddons($item) {
		$oContext = &Context::getInstance();
		$oContext->init();
		Context::loadLang('lang');
		return Context::getLang($item);
	}
	
	function getSite_srl() {
		$oContext = &Context::getInstance();
		$oContext->init();
		$site_module_info = Context::get('site_module_info');
        return $site_module_info->site_srl;
	}

	function nullToZero($val) {
		if ( (!$val) || ($val=="") ) {
			$res = 0;
		} else {
			$res = $val;
		}
		
		return $res;
	}
	
	function moveUrl($url) {
		echo "<script>window.location.replace('".$url."');</script>";
	}

	function message($msg) {
		echo "<script>alert('$msg');</script>";	
	}
	
	function colorset() {
		return "<style type=\"text/css\">
  body,.sm_box,.message_btn span{background-color:%s}
 .r3_color, .sm_sendbox_bg,.search_btn{background-color: %s}
 .message_btn_bold{color:%s} </style>";		
	}

	function auto_link($text) {
		//  이미 <a href 가 포함된 경우 skip
		if (strpos($text, '<a href=') == 0 ) {
			// force http: on www.
		 	$text = ereg_replace( "www\.", "http://www.", $text );
			// eliminate duplicates after force
		  	$text = ereg_replace( "http://http://www\.", "http://www.", $text );
		  	$text = ereg_replace( "https://http://www\.", "https://www.", $text );
		  
			// The Regular Expression filter
			$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			// Check if there is a url in the text
			if(preg_match($reg_exUrl, $text, $url)) {
				   // make the urls hyper links
				   $text = preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow">'.$url[0].'</a>', $text);
			}    // if no urls in the text just return the text
		}

		return ($text);
	}
	
	function getLicense() {
		$host = getLangInfo('license_host_domain');
		$productItem = getLangInfo('messageTalk');   
		$port = "80";  
		$url = $_SERVER["HTTP_HOST"];
		$fullpath = "/license/verify/messageTalk.php?site_url=$url&item=$productItem";
		$fp = fsockopen ($host, $port, $errno, $errstr, 30);
		$resLicense = '';
		if (!$fp) {
			echo "$errstr ($errno)<br>\n"; 
		} else {
			fputs($fp, "GET ".$fullpath." HTTP/1.0\r\n"."Host: $host:${port}\r\n"."User-Agent: Web 0.1\r\n"."\r\n");
			$license = '';
			while (!feof($fp)) {
				$license.= str_replace('\r\n', '', fgets ($fp,1024)); 
		  	}
			fclose ($fp); 
		}
		$arrLicense = split('@', $license);
		for ($i=1; $i < sizeof($arrLicense);$i++){
			$resLicense.=$arrLicense[$i];
		}
		
		return $resLicense; 
	}
	
	function str_cut_string($str, $limit)
	{
		$str2   = '';
		$fix    = '';
		// mb_substr 지원유무에 따라...
		if (function_exists('mb_substr')) {
			if (mb_strlen($str) > $limit) {
				$str2 = sprintf('%s...', mb_substr($str,0,$limit-4, 'UTF-8'));
			} else {
				$str2 = $str;
			}
		} else { 
			$limit = $limit * 3;
			$length = 0;
		
			for($i=0;$i<strlen($str);)
			{
				if(ord($str[$i])>127) {
					$hangul =  substr($str,$i,3);		
					$length+=3;
					$i = $i+3;
				} else {
					$hangul =  substr($str,$i,1);	
					$length+=1;
					$i++;
				}
		
				if($length > $limit - 4) {
					$fix = "...";
					break;
				} else {
					$str2=$str2.$hangul;
				}		
			}
		}

		return $str2.$fix;
	}
?>