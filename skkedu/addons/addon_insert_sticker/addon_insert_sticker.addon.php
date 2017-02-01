<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @addon_insert_sticker.addon.php
     * @author XENARA (kolaskks@naver.com)
     * @brief XENARA 스티커 삽입 애드온
     **/

    if($called_position == 'after_module_proc') {
			Context::addCSSFile('./addons/addon_insert_sticker/css/addon.css');

      $insert_sticker = 0;
      if($addon_info->insert_sticker_editor=='document'){
        if(Context::get('act')=='dispBoardWrite'){
          $insert_sticker = 1;
        }
      } else if($addon_info->insert_sticker_editor=='comment'){
        if(Context::get('document_srl')){
          $insert_sticker = 1;
        }
      } else{
        $insert_sticker = 1;
      }

      if($insert_sticker==1){
        $addon_path = getUrl('').'addons/addon_insert_sticker/';

        $header_content = '';
        $header_content .= '
          <script type="text/javascript">
            function insertSticker(editorSequence,sticker_src) {
              // html 모드
              if(editorMode[editorSequence]=="html"){
                if(text.length>0 && get_by_id("editor_textarea_"+editorSequence)){
              	  get_by_id("editor_textarea_"+editorSequence).value += text.join("");
                }

              // 위지윅 모드
              } else{
                var iframe_obj = editorGetIFrame(editorSequence);
                if(!iframe_obj) return;
                var sticker_img = "<img src=\'"+sticker_src+"\' />";
                if(sticker_src) editorReplaceHTML(iframe_obj, sticker_img);
              }
            }
          </script>
        ';
        Context::addHtmlheader($header_content);


        $stickter_list = array();
        $stickter_temp_list = FileHandler::readDir(_XE_PATH_.'addons/addon_insert_sticker/stickers');
        foreach($stickter_temp_list as $key => $val){
          $filename = strtolower($val);
          if(substr($filename,-3)=='gif' || substr($filename,-3)=='jpg' || substr($filename,-3)=='png'){
            $stickter_list[] = $val;
          }
        }

        $stickter_list_html = '';
        if(count($stickter_list)>0){
          $stickter_list_html .= '<div class="addon_insert_sticker"><ul class="stickter_list">';
          $sticker_url = addslashes('jQuery(this).children("img").attr("src")');
          foreach($stickter_list as $key => $val){
            $stickter_list_html .= '<li><a href="#" onclick="insertSticker(jQuery(\\\'.xpress_xeditor_editing_area_container\\\').attr(\\\'id\\\').substr(20,10),jQuery(this).children(\\\'img\\\').attr(\\\'src\\\')); return false;"><img src="'.$addon_path.'stickers/'.$val.'" /></a></li>';
          }
          $stickter_list_html .= '</ul></div>';
        }

        $footer_content = '';
        $footer_content = $footer_content .'
          <script type="text/javascript">
            jQuery(document).ready(function(){
              jQuery(".xpress-editor").append(\''.$stickter_list_html.'\');
            });
          </script>
        ';
        Context::addHtmlFooter($footer_content);
      }
    }
?>
