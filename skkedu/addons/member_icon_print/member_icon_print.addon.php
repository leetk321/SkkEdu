<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file member_icon_print.addon.php
     * @author 러키군 (admin@barch.kr)
     * @brief 아이콘샵의 대표아이콘을 출력
     **/

    /**
     * 출력되기 바로 직전일 경우에 이미지이름/이미지마크등을 변경
     **/
    if($called_position != "before_display_content" || Context::get('act')=='dispPageAdminContentModify') return;
    require_once('./addons/member_icon_print/member_icon_print.lib.php');

    // 1. 출력문서중에서 <div class="member_번호">content</div>를 찾아 MemberController::transImageName() 를 이용하여 이미지이름/마크로 변경
    $output = preg_replace_callback('!<(div|span|a)([^\>]*)member_([0-9]+)([^\>]*)>(.*?)\<\/(div|span|a)\>!is', 'IconshopMemberIconPrint', $output);
?>
