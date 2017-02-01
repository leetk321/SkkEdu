<?php
    if(!defined("__ZBXE__")) exit();
    if(!defined("__XE__")) exit();

    // 관리자 모듈 리턴
    if(Context::get('module')=='admin') return;

    if($GLOBALS['_called_addon_naver_analytics_']) return;
    $GLOBALS['_called_addon_naver_analytics_'] = true;

    $js_code = <<<EndOfGA

<script type="text/javascript" src="https://wcs.naver.net/wcslog.js"></script>
<script type="text/javascript">
if(!wcs_add) var wcs_add = {};
wcs_add["wa"] = "{$addon_info->n_id}";
wcs_do();
</script>

EndOfGA;
    Context::addHtmlHeader($js_code);
?>