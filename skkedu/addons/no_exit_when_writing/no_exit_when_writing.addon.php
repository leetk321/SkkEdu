<?php

/* Copyright (C) 주파르탄(joo.partan@gmail.com) */

if(!defined('__XE__')) exit();

if($called_position == 'before_display_content') {
if(Context::get('grant')->write_document && Context::get('act') == 'dispBoardWrite') {

$output = $output . '<script>var usersubmit=false;
jQuery("form").submit(function() {
usersubmit = true;
});
window.onbeforeunload = function() {
    if(!usersubmit)
        return "이 페이지를 나가시겠습니까?\n(임시저장을 하지 않았으면 데이터가 그대로 유실됩니다)";
};</script>';

}elseif(Context::get('grant')->write_comment && (Context::get('act') == 'dispBoardReplyComment' || Context::get('act') == 'dispBoardModifyComment')){
$output = $output . '<script>var usersubmit=false;
jQuery("form").submit(function() {
usersubmit = true;
});
window.onbeforeunload = function() {
    if(!usersubmit)
        return "이 페이지를 나가시겠습니까?\n(이대로 나가시면 작성중이었던 데이터가 유실됩니다.)";
};</script>';
}

}