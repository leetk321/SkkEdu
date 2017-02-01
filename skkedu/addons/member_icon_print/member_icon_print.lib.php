<?php
    /**
     * @brief div 또는 span에 member_번호 가 있을때 해당 회원 번호에 맞는 대표아이콘으로 대체
     **/
    function IconshopMemberIconPrint($matches) {
        if(strpos($matches[0],'icon_print="no"')!==false)  return $matches[0];

        // 회원번호를 추출하여 0보다 찾으면 본문중 text만 return
        $member_srl = $matches[3];

        // 회원이 아닐경우(member_srl = 0) 본문 전체를 return
        if($member_srl<0) return $matches[5];
        $nick_name = $matches[5];

        // 아이콘샵 모델을 구해옴
        $oIconshopModel = &getModel('iconshop');

        // 전역변수에 미리 설정한 데이터가 있다면 그걸 return 하고, 없으면 구해옴
        if(!$GLOBALS['_iconshopMember'][$member_srl]->cached) {
            $GLOBALS['_iconshopMember'][$member_srl]->cached = true;

            // 회원의 대표아이콘 구해옴
            $icon_data = $oIconshopModel->getMemberIconBySelected($member_srl);
            if($icon_data && file_exists($icon_data->file1)) $GLOBALS['_iconshopMember'][$member_srl]->icon = $icon_data;
            else $icon_data = '';
        } else {
            $icon_data = $GLOBALS['_iconshopMember'][$member_srl]->icon;
        }

        // 대표아이콘이 없으면 원본 정보를 세팅
        if(!$icon_data) return $matches[0];

        // 남은시간 검사
        $now = date("YmdHis");
        $end_date = $icon_data->end_date;

        // 시간이 지났을경우 삭제
        if(($now > $end_date) && ($icon_data->minute_limit == "Y")) {
            $oIconshopController = &getController('iconshop');
            $args = null;
            $args->member_srl = $member_srl;
            $args->icon_srl = $icon_data->icon_srl;
            $oIconshopController->deleteMemberIcon($args);

            // 설정 정보 가져오기
            if(!$GLOBALS['_iconshopConfig']) {
                $oModuleModel = &getModel('module');
                $GLOBALS['_iconshopConfig'] = $oModuleModel->getModuleConfig('iconshop');
            }
            $config = $GLOBALS['_iconshopConfig'];

            // 쪽지발송 Y이면 발송..
            if($config->item_delete_event == "Y") {
                $title = str_replace(array("[member_srl]","[nick_name]","[icon_srl]","[icon_title]","[start_date]","[end_date]"),array($member_srl,strip_tags($nick_name),$icon_data->icon_srl,$icon_data->title,zDate($start_date,"Y/m/d H:i"),zDate($end_date,"Y/m/d H:i")),htmlspecialchars(trim($config->item_delete_title)));
                $content = str_replace(array("[member_srl]","[nick_name]","[icon_srl]","[icon_title]","[start_date]","[end_date]"),array($member_srl,strip_tags($nick_name),$icon_data->icon_srl,$icon_data->title,zDate($start_date,"Y/m/d H:i"),zDate($end_date,"Y/m/d H:i")),htmlspecialchars(trim(nl2br($config->item_delete_message))));
                $sender_srl = ($config->sender_srl)? $config->sender_srl : $member_srl;
                $oCommunicationController = &getController('communication');
                $oCommunicationController->sendMessage($sender_srl,$member_srl,$title,nl2br($content)); // 쪽지발송
            }
            unset($GLOBALS['_iconshopMember'][$member_srl]->icon);
        } else {
            // 아이콘 표시
            $nick_name = sprintf('<img src="%s%s" border="0" alt="title: %s" title="title : %s" style="vertical-align:middle;margin-right:3px"/>%s', Context::getRequestUri(),$icon_data->file1, strip_tags($icon_data->title), strip_tags($icon_data->title), $nick_name);
        }

        $orig_text = preg_replace('/'.preg_quote($matches[5],'/').'<\/'.$matches[6].'>$/', '', $matches[0]);
        return $orig_text.$nick_name.'</'.$matches[6].'>';
    }
?>
