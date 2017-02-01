<?php
    if(!defined("__ZBXE__")) exit();
	if(Context::getResponseMethod()!='HTML') return;
	if($called_position != 'before_display_content') return;

	if (!$addon_info->position){ $addon_info->position = "bottomright"; }
	Context::addHtmlHeader('<script type="text/javascript" src="http://radarurl.com/js/radarurl_widget.js"></script><script type="text/javascript">radarurl_call_radar_widgetv2({edition:"Dynamic",location:"' . $addon_info->position . '"})</script><noscript><a href="http://radarurl.com/">RadarURL</a></noscript>');

	if ($addon_info->target=="blank" || !$addon_info->target){ Context::addHtmlHeader('<script>jQuery(document).ready(function ($){ $("a[href=\'http://radarurl.com/monitor\']").attr("target", "_blank"); });</script>'); }
	if ($addon_info->target=="none"){ Context::addHtmlHeader('<script>jQuery(document).ready(function ($){ $("a[href=\'http://radarurl.com/monitor\']").click(function (){ return false; }); });</script>'); }

	$member = Context::get('logged_info');
	if ($addon_info->admin=="Y" && $member->is_admin!="Y"){
		Context::addHtmlHeader('<script>jQuery(document).ready(function ($){ $("a[href=\'http://radarurl.com/monitor\']").remove(); });</script>');
	}
?>