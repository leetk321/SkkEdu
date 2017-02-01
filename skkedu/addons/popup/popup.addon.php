<?php
    if (!defined("__ZBXE__")) exit();

    /**
     * @file popup.addon.php
     * @author mooo (hhyoon@mooo.org)
     * @brief popup management addon
     **/

    if (Context::getResponseMethod() == "XMLRPC" or Context::getResponseMethod() == "JSON") return;
    if ($called_position != 'after_module_proc' or Context::get('module') == 'admin') return;
    if ($this->getLayoutFile() == 'popup_layout.html') return;
    if (!$addon_info->start_date or !$addon_info->end_date) return;
    if ($addon_info->content_type == 'image' and !$addon_info->image_url) return;

    // check date
    $now = date("YmdHi");
    if ($now < $addon_info->start_date or $now > $addon_info->end_date) return;

    // check cookie for "hide today"
    if ($_COOKIE['_hide_popup_today_'] == 'true') return;

    // set initial values
    if (!$addon_info->popup_type) $addon_info->popup_type = 'layer';
    if (!$addon_info->popup_position) $addon_info->popup_position = 'center';
    if (!$addon_info->pos_top or !is_numeric($addon_info->pos_top)) $addon_info->pos_top = 100;
    if (!$addon_info->pos_left or !is_numeric($addon_info->pos_left)) $addon_info->pos_left = 100;
    if (!$addon_info->width or !is_numeric($addon_info->width)) $addon_info->width = 400;
    if (!$addon_info->padding or !is_numeric($addon_info->padding)) $addon_info->padding = 0;
    if ($addon_info->width_type == 'auto') {
        if ($addon_info->content_type == 'html') $addon_info->width = 400;
        else  $addon_info->width = 0;
    }
    if ($addon_info->content_type == 'html' and !$addon_info->html_path) $addon_info->html_path = './addons/popup/popup.content.html';

    // generate codes
    if ($addon_info->popup_type == 'popup') {
        // generate HTML code
        $code = '<script type="text/javascript">popopen("/addons/popup/popup_window.html", "_popup");</script>';
    }
    else {

        // generate javascript code
        $script = sprintf('<script type="text/javascript">this_popup.type = "%s"; this_popup.position = "%s"; this_popup.pos_top = %d; this_popup.pos_left = %d; this_popup.width = %d; this_popup.padding = %d;</script>', $addon_info->popup_type, $addon_info->popup_position, $addon_info->pos_top, $addon_info->pos_left, $addon_info->width, $addon_info->padding);

        if ($addon_info->content_type == 'html') {
            $content = FileHandler::readFile($addon_info->html_path);
            if (!$content) return;
        }
        else {
            $content = sprintf('<img id="addon-popup-image" src="%s" alt="%s" />', $addon_info->image_url, htmlspecialchars($addon_info->title));
            if ($addon_info->link_url)
                $content = sprintf('<a href="%s" return false;">%s</a>', $addon_info->link_url, $content);
        }

        // load language file
        Context::loadLang(_XE_PATH_ .'addons/popup/lang');
        $msg_hide_today = Context::getLang('msg_hide_today');

        // get base url
        $base_url = getUrl('');

        // generate HTML code
        $code =<<<EOF
<div id="addon-popup-layer">
    <div id="addon-popup-modal"></div>
    <div id="addon-popup-container">
        <div id="addon-popup-content">{$content}</div>
        <div id="addon-popup-menu">
            <label for="addon-popup-hide-check">{$msg_hide_today}</label> <input id="addon-popup-hide-check" type="checkbox" name="hide_check" value="true" onclick="this_popup.close();" />
            <img id="addon-popup-button-close" src="{$base_url}addons/popup/images/close.gif" alt="Close" onclick="this_popup.close();" />
        </div>
    </div>
</div>
EOF;

        // add script code to HTML header
        Context::addHtmlHeader($script);

        // add javascript file
        Context::addJsFile('./addons/popup/js/popup_layer.js');

        // add CSS file
        // for modal layer
        if ($addon_info->popup_type == 'modal') {
            Context::addCssFile('./addons/popup/css/popup_modal.css');
        }
        // for fixed layer (absolute position)
        else {
            Context::addCssFile('./addons/popup/css/popup_layer.css');
        }
    }

    // add code to HTML source
    Context::addHtmlFooter($code);

?>
