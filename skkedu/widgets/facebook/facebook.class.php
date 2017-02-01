<?php
    /**
     * @class facebook widgets
     * @author Study4U
     * @version 0.1
     * @brief 페이스북 위젯
     *
     **/

    class facebook extends WidgetHandler {

        function proc($args) {

			// 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
			$tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);

			// 페이스북 url
			if($args->skin=="LikeButton" && $args->facebook_url=="") return Context::getLang('msg_not_founded');
			elseif($args->skin=="LikeBox" && $args->facebook_url=="") return Context::getLang('msg_not_founded');
			elseif($args->skin=="LikeButton" && !preg_match('/^http\:\/\//i',$args->facebook_url)) return Context::getLang('msg_not_founded');
			elseif($args->skin=="LikeBox" && !preg_match('/^http\:\/\//i',$args->facebook_url)) return Context::getLang('msg_not_founded');
			elseif($args->skin=="Facepile" && $args->facebook_url=="") $args->facebook_url = 'http://developers.facebook.com/tools/echo/' ;
			else $widget_info->facebook_url = $args->facebook_url;

			// Send Button
			$widget_info->fb_send_button = $args->fb_send_button;

			// layout
			$widget_info->fb_layout = $args->fb_layout;

			// Width
			if($args->skin=="LikeButton" && $args->fb_layout == "standard" && $args->fb_width=="") $args->fb_width = 450;
			elseif($args->skin=="LikeButton" && $args->fb_layout == "button_count" && $args->fb_width=="") $args->fb_width = 90;
			elseif($args->skin=="LikeButton" && $args->fb_layout == "box_count" && $args->fb_width=="") $args->fb_width = 55;
			elseif($args->skin=="LikeBox" && $args->fb_width=="") $args->fb_width = 292;
			elseif($args->skin=="LiveStream" && $args->fb_width == "") $args->fb_width = 400;
			elseif($args->skin=="Recommendations" && $args->fb_width == "") $args->fb_width = 300;
			elseif($args->skin=="ActivityFeed" && $args->fb_width == "") $args->fb_width = 300;
			elseif($args->skin=="Comments" && $args->fb_width == "") $args->fb_width = 400;
			elseif($args->skin=="LoginButton" && $args->fb_width == "") $args->fb_width = 200;
			elseif($args->skin=="Facepile" && $args->fb_width == "") $args->fb_width = 200;
			elseif($args->skin=="Registration" && $args->fb_width == "") $args->fb_width = 520;
			else $args->fb_width = (int)$args->fb_width;
			$widget_info->fb_width = (int)$args->fb_width;

			// show_faces
			$widget_info->fb_showfaces = $args->fb_showfaces;

			// Stream
			$widget_info->fb_stream = $args->fb_stream;

			// Header
			$widget_info->fb_header = $args->fb_header;

			// Height
			if($args->skin=="LikeBox" && $args->fb_stream == "true") $fb_stream = 300; else $fb_stream = 0;
			if($args->skin=="LikeBox" && $args->fb_header == "true") $fb_header = 30; else $fb_header = 0;
			if($args->skin=="LikeBox" && $args->fb_showfaces == "true") $fb_showfaces = 170; else $fb_showfaces = 0;
			if($fb_stream || $fb_header || $fb_showfaces) $fb_height = 23; else $fb_height = 0;
			if($args->skin=="LikeBox") $args->fb_height = $fb_height+$fb_stream+$fb_header+$fb_showfaces+67;

			if($args->skin=="LikeButton" && $args->fb_layout == "standard" && $args->fb_showfaces == "true") $args->fb_height = 80;
			if($args->skin=="LikeButton" && $args->fb_layout == "button_count" && $args->fb_showfaces == "true") $args->fb_height = 20;
			if($args->skin=="LikeButton" && $args->fb_layout == "box_count" && $args->fb_showfaces == "true") $args->fb_height = 65;

			if($args->skin=="LiveStream" && $args->fb_height == "") $args->fb_height = 500;
			if($args->skin=="Recommendations" && $args->fb_height == "") $args->fb_height = 300;
			if($args->skin=="ActivityFeed" && $args->fb_height == "") $args->fb_height = 300;
			if($args->skin=="Registration" && $args->fb_height == "") $args->fb_height = 330;

			$widget_info->fb_height = (int)$args->fb_height;

			// Verb to display
			$widget_info->fb_action = $args->fb_action;

			// Color Scheme
			$widget_info->fb_colorscheme = $args->fb_colorscheme;

			// font
			if($args->fb_font=="default") $widget_info->fb_font = ""; else $widget_info->fb_font = $args->fb_font;

			// App ID
			if($args->skin=="LiveStream" && $args->fb_appid=="") return Context::getLang('msg_not_founded');
			elseif($args->skin=="LoginButton" && $args->fb_appid=="") return Context::getLang('msg_not_founded');
			elseif($args->skin=="Registration" && $args->fb_appid=="") return Context::getLang('msg_not_founded');
			elseif($args->skin=="LiveStream" && preg_match('/^http\:\/\//i',$args->fb_appid)) return Context::getLang('msg_not_founded');
			elseif($args->skin=="LoginButton" && preg_match('/^http\:\/\//i',$args->fb_appid)) return Context::getLang('msg_not_founded');
			elseif($args->skin=="Registration" && preg_match('/^http\:\/\//i',$args->fb_appid)) return Context::getLang('msg_not_founded');
			else $args->fb_appid = $args->fb_appid;
			if(!$args->fb_appid) $args->fb_appid = 113869198637480;
			$widget_info->fb_appid = $args->fb_appid;

			// XID
			$widget_info->fb_xid = $args->fb_xid;

			// Via Attribution URL
			$widget_info->fb_via_url = $args->fb_via_url;

			// Always post to friends
			$widget_info->fb_post_to_friends = $args->fb_post_to_friends;

			// Border Color
			$widget_info->fb_border_color = $args->fb_border_color;

			// Show recommendations
			$widget_info->fb_recommendations = $args->fb_recommendations;

			// Comments
			if($args->skin=="Comments" && $args->fb_urlto=="") return Context::getLang('msg_not_founded');
			elseif($args->skin=="SendButton" && $args->fb_urlto=="") return Context::getLang('msg_not_founded');
			else $widget_info->fb_urlto = $args->fb_urlto;

			// Number of posts
			if($args->fb_num_posts=="") $args->fb_num_posts = 10;
			$widget_info->fb_num_posts = (int)$args->fb_num_posts;

			// Max Rows
			if($args->fb_max_rows=="") $args->fb_max_rows = 1;
			$widget_info->fb_max_rows = (int)$args->fb_max_rows;

			Context::set('colorset', $args->colorset);
			Context::set('widget_info', $widget_info);

			$oTemplate = &TemplateHandler::getInstance();

			// 템플릿 컴파일하여 html로 return
			return $oTemplate->compile($tpl_path, "content");
		}
    }
?>
