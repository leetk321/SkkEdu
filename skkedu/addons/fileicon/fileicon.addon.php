<?php
	if($called_position!='before_display_content') return;
	if(Context::get('module') == 'admin') return;
	
	if($addon_info->icon_color == 'c') $icon = $addon_info->icon_url;
	else if($addon_info->icon_color == 'w') $icon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAALCAYAAACprHcmAAAAS0lEQVQYV2P8DwQMUMAIBDA2SBzEh8mD2HAOSBE+xWB5XCYj2wC3GVkxTBAXjWLyIFCMHBLozoEHH3I4YvMsSlgjK8blQawmE1IMANPcU+TMVR6PAAAAAElFTkSuQmCC';
	else $icon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAALCAYAAACprHcmAAAASUlEQVQYV2M0Njb+zwAFZ8+eZYSxQeIgPkwexIZzQIrwKQbJ41SMbAOMjaIYJoiLHmyKkUMC3c2w4EMJR+QwRw57rIpxhQJZigHeNjv8RtiwcQAAAABJRU5ErkJggg';
	if(!$addon_info->icon_width) $addon_info->icon_width = '13px';
	if(!$addon_info->icon_height) $addon_info->icon_height = '13px';
	if(!$addon_info->icon_pos) $addon_info->icon_pos = 'center';
	
	$script = '<script type="text/javascript">(function($){var a=/^\\S+procFileDownload\\S+$/i;var b=document.querySelectorAll(".xe_content a");var c=\'<span style="display:inline-block;width:'.$addon_info->icon_width.';height:'.$addon_info->icon_height.';vertical-align:middle;background: url('.$icon.') center '.$addon_info->icon_pos.' no-repeat;margin-right:3px;"></span>\';for(var d in b){if(a.test(b[d].href)){if(b[d].innerHTML!="")b[d].innerHTML=c+b[d].innerHTML}}})(jQuery)</script>';
	
	Context::addHtmlFooter($script);
?>