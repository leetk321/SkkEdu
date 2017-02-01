<?php
    if(!defined("__XE__")) exit();
	
	//파일 로딩
	if($called_position == 'before_display_content') {
		if (!$addon_info->load_jquery) $addon_info->load_jquery = 'yes';
		if ($addon_info->load_jquery == 'yes') {
			Context::addJSFile("//code.jquery.com/jquery.min.js", false,'',-100000);
			Context::addJSFile("//code.jquery.com/jquery-migrate-1.2.1.min.js", false,'',-100000);
			Context::addJSFile("//code.jquery.com/ui/1.10.4/jquery-ui.js", false,'',-100000);
			Context::addJSFile("./addons/layerpopup/source/unload.js", false,'',-100000);
		}elseif ($addon_info->load_jquery == 'no') {
			Context::addJSFile("./addons/layerpopup/source/varjQuery.js", false,'',-100000);
		}
		if ($addon_info->load_jquery_ui == 'yes') {
			Context::addJSFile("//code.jquery.com/ui/1.11.0/jquery-ui.min.js", false,'',0);
		}
		Context::addJSFile("./addons/layerpopup/source/jquery.mousewheel-3.0.6.pack.js", false);
		Context::addJSFile("./addons/layerpopup/source/jquery.fancybox.pack.js", false);
		Context::addCSSFile("./addons/layerpopup/source/jquery.fancybox.css", false);
		Context::addJSFile("./addons/layerpopup/source/cookie.js", false);
   	}
	
	if($called_position == 'before_module_proc' && Context::get('module') != 'admin' && $this->module_info->module_srl){
		
        // 기본값 설정
        if (!$addon_info->width || !is_numeric($addon_info->width)) $addon_info->width = 9999;
        if (!$addon_info->height || !is_numeric($addon_info->height)) $addon_info->height = 9999;
		if (!$addon_info->padding || !is_numeric($addon_info->padding)) $addon_info->padding = 0;
		if (!$addon_info->openeffect) $addon_info->openeffect = 'fade';
		if (!$addon_info->closeeffect) $addon_info->closeeffect = 'fade';
		if (!$addon_info->img_open) $addon_info->img_open = '_blank';
		if (!$addon_info->openspeed || !is_numeric($addon_info->openspeed)) $addon_info->openspeed = 1000;
		if (!$addon_info->closespeed || !is_numeric($addon_info->closespeed)) $addon_info->closespeed = 150;
		if (!$addon_info->topratio || !is_numeric($addon_info->topratio)) $addon_info->topratio = 0.95;
		if (!$addon_info->leftratio || !is_numeric($addon_info->leftratio)) $addon_info->leftratio = 0.05;
		if (!$addon_info->shadow || !is_numeric($addon_info->shadow)) $addon_info->shadow = 10;
 		if (!$addon_info->img_href) $addon_info->img_href = '#';
		if (!$addon_info->popup_con) $addon_info->popup_con = 'img';
		if (!$addon_info->nopopup || !is_numeric($addon_info->nopopup)) $addon_info->nopopup = 1;
		if (!$addon_info->nopopup_color) $addon_info->nopopup_color = '#ffffff';
		if (!$addon_info->popup_time) $addon_info->popup_time = 'yes';
		if (!$addon_info->starttime_auto) $addon_info->starttime_auto = 'yes';
		if (!$addon_info->popup_drag) $addon_info->popup_drag = 'no';
		if (!$addon_info->popup_pos) $addon_info->popup_pos = 'true';
		if ($addon_info->starttime_auto == 'yes'){
			$addon_info->s_year = 1980;
			$addon_info->s_month = 1;
			$addon_info->s_day = 1;
			$addon_info->s_hours = 0;
			$addon_info->s_minutes = 0;
		}
		
		if ($addon_info->popup_drag == 'all' || $addon_info->popup_drag == 'handle'){
			$autoResize = "false";
		}else $autoResize = "true";
		
 		//레이어 팝업 출력
		if ($addon_info->popup_time == 'no') {
        	$script = "<script type=\"text/javascript\">jQuery(document).ready(function() {if(getCookie('lpopup') == 'no') {}else{jQuery('.fancybox').fancybox({autoResize:".$autoResize.",autoCenter:".$addon_info->popup_pos.",minWidth:0,minHeight:0,padding:".$addon_info->padding.",openEffect:'".$addon_info->openeffect."',closeEffect:'".$addon_info->closeeffect."',openSpeed:".$addon_info->openspeed.",closeSpeed:".$addon_info->closespeed.",topRatio:".$addon_info->topratio.",leftRatio:".$addon_info->leftratio.",helpers:{overlay : null}}).trigger('click');}});</script>";
        	Context::addHtmlHeader($script);
		}else if ($addon_info->popup_time == 'yes') {
			$script = "<script type=\"text/javascript\">var startdate = new Date(".$addon_info->s_year.", ".$addon_info->s_month.", ".$addon_info->s_day.", ".$addon_info->s_hours.", ".$addon_info->s_minutes.");var enddate = new Date(".$addon_info->e_year.", ".$addon_info->e_month.", ".$addon_info->e_day.", ".$addon_info->e_hours.", ".$addon_info->e_minutes.");var nowdate = new Date();jQuery(document).ready(function() {if (nowdate.getTime() >= startdate.getTime() && nowdate.getTime() <= enddate.getTime()){if(getCookie('lpopup') == 'no') {}else{jQuery('.fancybox').fancybox({autoResize:".$autoResize.",autoCenter:".$addon_info->popup_pos.",minWidth:0,minHeight:0,padding:".$addon_info->padding.",openEffect:'".$addon_info->openeffect."',closeEffect:'".$addon_info->closeeffect."',openSpeed:".$addon_info->openspeed.",closeSpeed:".$addon_info->closespeed.",topRatio:".$addon_info->topratio.",leftRatio:".$addon_info->leftratio.",helpers:{overlay : null}}).trigger('click');}}});</script>";
        	Context::addHtmlHeader($script);
		}
		
		//드래그 기능
		if ($addon_info->popup_drag == 'all'){
			$script = "<script type=\"text/javascript\">jQuery(document).ready(function() {\$('.fancybox-wrap').draggable({cursor:'move',cancel:'.fancybox-close'});\$('.fancybox-wrap').disableSelection();});</script>";
			Context::addHtmlHeader($script);
		}elseif ($addon_info->popup_drag == 'handle'){
			$script = "<script type=\"text/javascript\">jQuery(document).ready(function() {\$('.fancybox-wrap').draggable({handle:'.lCheck',cancel:'.fancybox-close'});\$('.fancybox-wrap').disableSelection();});</script>";
			Context::addHtmlHeader($script);
			$style = "<style>.fancybox-wrap .lCheck { cursor: move; }</style>";
			Context::addHtmlHeader($style);
		}
		
		//스타일, HTML 출력
		$style = "<style>.fancybox-desktop {box-shadow: 0 0 ".$addon_info->shadow."px #666666;-webkit-box-shadow: 0 0 ".$addon_info->shadow."px #666666;-moz-box-shadow: 0 0 ".$addon_info->shadow."px #666666;}#dialoga .lCheck {background-color:#FFF;box-shadow:0 0 2px #666666;-webkit-box-shadow:0 0 2px #666666;-moz-box-shadow: 0 0 2px $666666;line-height:20px;height:20px;margin-top:-2px;padding-bottom:2px}#dialoga img{height:auto;max-width:100%;vertical-align:middle;border:0;margin:auto;-ms-interpolation-mode:bicubic}#dialoga label{font-size:12px;font-family:'NanumGothic','Segoe UI','Segoe UI','Malgun Gothic',Meiryo,'Trebuchet MS','Dotum',AppleGothic,sans-serif;color:#000;vertical-align:middle}#dialoga input[type=checkbox]{width:12px;height:12px;margin-left:3px;margin-right:3px;display:inline-block;vertical-align:middle}</style>";
		Context::addHtmlHeader($style);
		
		$html1 = "<a class=\"fancybox\" href=\"#dialoga\" style=\"display: none;\">LayerPopupAddon</a>";
		Context::addHtmlHeader($html1);
		
		if($addon_info->image_size == 'true') $image_style = "style=\"width:".$addon_info->width."px;height:".$addon_info->height."px;\" ";
		else $image_style = "";
		
		if ($addon_info->popup_con == 'img'){
			$html2 = "<div id=\"dialoga\" style=\"display: none;\"><div><a href=\"".$addon_info->img_href."\" target=\"".$addon_info->img_open."\"><img src=\"".$addon_info->img_path."\" ".$image_style."/></a></div><div class=\"lCheck\" style=\"background-color: ".$addon_info->nopopup_color.";\"><input type=\"checkbox\" id=\"nopopup\" onClick=\"jQuery.fancybox.close(); setCookie('lpopup', 'no',".$addon_info->nopopup.")\"><label for=\"nopopup\" style=\"font-size: 12px;\"> ".$addon_info->nopopup."일간 열지않기</label></div></div>";
			Context::addHtmlHeader($html2);
		}else if ($addon_info->popup_con == 'html') {
			$html2 = "<div id=\"dialoga\" style=\"display: none;\"><iframe src=\"".$addon_info->html_path."\" style=\"width:".$addon_info->width."px;height:".$addon_info->height."px;border:0;margin:0;overflow:auto\"></iframe><div class=\"lCheck\" style=\"background-color: ".$addon_info->nopopup_color.";\"><input type=\"checkbox\" id=\"nopopup\" onClick=\"jQuery.fancybox.close(); setCookie('lpopup', 'no', ".$addon_info->nopopup.")\"><label for=\"nopopup\" style=\"font-size: 12px;\">".$addon_info->nopopup."일간 열지않기</label></div></div>";
			Context::addHtmlHeader($html2);
		}else if ($addon_info->popup_con == 'line_auto') {
			$html2 = "<div id=\"dialoga\" style=\"display: none;\">".$addon_info->html_line."<div class=\"check\" style=\"background-color: ".$addon_info->nopopup_color.";\"><input type=\"checkbox\" id=\"nopopup\" onClick=\"jQuery.fancybox.close(); setCookie('lpopup', 'no', ".$addon_info->nopopup.")\"><label for=\"nopopup\" style=\"font-size: 12px;\">".$addon_info->nopopup."일간 열지않기</label></div></div>";
			Context::addHtmlHeader($html2);
		}else if ($addon_info->popup_con == 'line_noauto') {
			$html2 = "<div id=\"dialoga\" style=\"display: none;\"><div style=\"width:".$addon_info->width."px; height:".$addon_info->height."px;\">".$addon_info->html_line."</div><div class=\"check\" style=\"background-color: ".$addon_info->nopopup_color.";\"><input type=\"checkbox\" id=\"nopopup\" onClick=\"jQuery.fancybox.close(); setCookie('lpopup', 'no', ".$addon_info->nopopup.")\"><label for=\"nopopup\" style=\"font-size: 12px;\">".$addon_info->nopopup."일간 열지않기</label></div></div>";
			Context::addHtmlHeader($html2);
		}
	}
?>