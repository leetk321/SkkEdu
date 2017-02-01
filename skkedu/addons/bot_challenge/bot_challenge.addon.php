<?php
if(!defined('__XE__')) exit();
$self_addon_name = 'bot_challenge';
// 모듈 초기화 시점에 하는 부분
if( $called_position ==='before_module_init')
{

    if(empty($addon_info->site_secret)){
        $addon_info->site_secret = 'a';
    }

    $no_spam_target_act = array(
        'procMemberInsert',
        'procBoardInsertDocument',
        'procBoardInsertComment',
        'procMemberFindAccount',
        'procIssuetrackerInsertIssue',
        'procIssuetrackerInsertHistory',
        'procTextyleInsertComment',
        'procCommunicationSendMessage',
    );

    // Bot Challenge 인 경우
    if($this->act === 'procBot_challengeTest')
    {

        // 이미 인증이 되어 있거나 , 세션이 만들어 지지 않을 상태에서 Bot Challenge를 하면 끝낸다.
        if(isset($_SESSION[$self_addon_name]->status) === false || $_SESSION[$self_addon_name]->status === true){
            Context::close();
            exit("ERR 0");
        }

        // 가장 먼저 CSRF 체크 헤더 검증 + checkCSRF 함수 사용통해 리퍼러 검증
        if($_SERVER['HTTP_X_CSRF_PROTECT'] !== $_SESSION[$self_addon_name]->csrf || checkCSRF() !== true )
        {
            Context::close();
            exit("CSRF ERROR");
        }


        // challenge 값을 가져옵니다.
        $challenge = Context::get('challenge');
        if(empty($challenge))
        {
            Context::close();
            exit('ERR 1');
        }



        // 서버에서 challenge를 계산합니다.
        $algo = strtolower($_SESSION[$self_addon_name]->hash_type);
        if($_SESSION[$self_addon_name]->return_type === 'Hex')
        {
            $server_test = hash_hmac($algo, $_SESSION[$self_addon_name]->challenge, $addon_info->site_secret);
        }
        elseif($_SESSION[$self_addon_name]->return_type === 'Base64')
        {
            $server_test = base64_encode(hash_hmac($algo, $_SESSION[$self_addon_name]->challenge, $addon_info->site_secret, true));
        }


        // 클라이언트의 challenge가 정확하면 OK.
        if($server_test !== $challenge){
            Context::close();
            exit('ERR 2');
        }else{
            $_SESSION[$self_addon_name]->status = true;
            Context::close();
            exit('success');
        }

    }
    // 인증이 되지 않은 상태인데도 불구하고, 글을 작성하거나, 회원가입을 시도하거나, 코멘트 작성을 시도하면
    // 중단.

    elseif( (isset($_SESSION[$self_addon_name]->status) === false
            || $_SESSION[$self_addon_name]->status === false)
        &&
        (in_array($this->act,$no_spam_target_act) === true))
    {

        context::close();
        header('x-anti-spam: spam blocked',true, 500);
        echo('<h1> 500 Internal ERROR XE</h1><h3>please contact admin</h3>');

        exit();

    }


}
elseif($called_position === 'before_display_content')
{

    if(empty($addon_info->site_secret)){
        $addon_info->site_secret ='a';
    }

    // Jsdelivr RUM 이용
    // 관리자는 제외

    if($addon_info->contribute === 'Y' &&
        (Context::get('is_logged') === true && isset(Context::get('logged_info')->is_admin) && Context::get('logged_info')->is_admin === 'Y') === false )
    {
        Context::addHtmlFooter('<script>(function(a,b,c,d,e){function f(){var a=b.createElement("script");a.async=!0;a.src="//radar.cedexis.com/1/11475/radar.js";b.body.appendChild(a)}/\bMSIE 6/i.test(a.navigator.userAgent)||(a[c]?a[c](e,f,!1):a[d]&&a[d]("on"+e,f))})(window,document,"addEventListener","attachEvent","load");</script>');
    }


    // 이미 challenge에 통과했으면 나갑니다.
    if (isset($_SESSION[$self_addon_name]->status) === true && $_SESSION[$self_addon_name]->status === true){
        Context::addHtmlHeader("<!-- T-S -->");
        return;
    }


    // challenge 세션을 만들지 않았다면 만들어 줍니다.  기본 인증은 false
    if (isset($_SESSION[$self_addon_name]) === false) {
        $_SESSION[$self_addon_name] = new stdClass();
        $_SESSION[$self_addon_name]->status = false;



        // 사용할 해쉬함수를 랜덤하게 선택합니다.
        $temp = unpack('S*', openssl_random_pseudo_bytes(3));
        $what_hash = $temp[1] % 4;

        // base64/hex중 무엇을 return할지 랜덤하게 선택합니다.
        $temp = unpack('S*', openssl_random_pseudo_bytes(3));
        $what_return_type = $temp[1] % 2;

        // 길이는 몇으로 할지 랜덤하게 선택합니다.(20~80)
        $temp = unpack('S*', openssl_random_pseudo_bytes(3));
        $what_length = ($temp[1] % 60) + 20;



        // 어떤 형태의 데이터를 challenge 할지 랜덤하게 선택합니다.
        if (mt_rand(0, 1) === 1) {
            $what_challenge = base64_encode(openssl_random_pseudo_bytes($what_length));
        } else {
            $temp =unpack('H*',openssl_random_pseudo_bytes($what_length));
            $what_challenge = $temp[1];
        }

        // HashType 결정
        switch ($what_hash) {
            case 0:
                $what_hash_string = 'MD5';
                break;
            case 1;
                $what_hash_string = 'SHA1';
                break;
            case 2;
                $what_hash_string = 'SHA256';
                break;
            case 3;
                $what_hash_string = 'SHA512';
                break;
        }



        // 리턴타입 결정
        switch ($what_return_type) {
            case 0:
                $what_return_type_string = 'Base64';
                break;
            case 1:
                $what_return_type_string = 'Hex';
                break;
        }


        // csrf 방지 헤더 생성
        $csrf = str_replace(array('+','/','_'),array('-','_',''),base64_encode(openssl_random_pseudo_bytes(15)));

        $_SESSION[$self_addon_name]->csrf = $csrf;
        $_SESSION[$self_addon_name]->hash_type = $what_hash_string;
        $_SESSION[$self_addon_name]->return_type = $what_return_type_string;
        $_SESSION[$self_addon_name]->challenge = $what_challenge;

    }



    // 이미 SESSION이 생성 되 있고, 미 인증 상태에만  파일 로딩
    if(isset($_SESSION[$self_addon_name]->status) === true
        &&  $_SESSION[$self_addon_name]->status !== true)
    {
        $csrf = $_SESSION[$self_addon_name]->csrf;
        $what_hash_string = $_SESSION[$self_addon_name]->hash_type;
        $what_return_type_string = $_SESSION[$self_addon_name]->return_type;
        $what_challenge = $_SESSION[$self_addon_name]->challenge;


        $request_uri = Context::getRequestUri();

        $backup_url = $request_uri.'addons/bot_challenge/backup.js';
        $site_secret = $addon_info->site_secret;

/*

            $js = <<<EOT
        <script>
        (function(){
        if(typeof CryptoJS === 'undefined'){ document.write('<script src="./addons/bot_challenge/backup.js"></script>'); }
        "use strict";
        var r = CryptoJS.enc.$what_return_type_string.stringify(CryptoJS.Hmac$what_hash_string("$what_challenge","$addon_info->site_secret"));
        var s = {
            data : jQuery.param({ 'act' : 'procBot_challengeTest', 'challenge' : r}),
            dataType  : 'json',
            type : 'post',
            headers :{ 'X-CSRF-Protect' : '$csrf'
            }
        };
        jQuery.ajax('$request_uri', s);
        })();
        </script>
        EOT;

        // 한줄로 압축해서 보내기
*/
        $js = <<<EOT
	    <script> if(typeof CryptoJS === 'undefined'){document.write(decodeURI('%3Cscript%20src=%22$backup_url%22%3E%3C/script%3E'));};</script><script>jQuery.ajax('$request_uri', s = {data : jQuery.param({ 'act' : 'procBot_challengeTest', 'challenge' : CryptoJS.enc.$what_return_type_string.stringify(CryptoJS.Hmac$what_hash_string("$what_challenge","$site_secret"))}),dataType  : 'json',type : 'post',headers :{ 'X-CSRF-Protect' : '$csrf'}});</script>
EOT;
        Context::loadFile(array('https://cdn.jsdelivr.net/crypto-js/3.1.2/components/core-min.js','head','',1));
        Context::loadFile(array('https://cdn.jsdelivr.net/crypto-js/3.1.2/components/enc-base64-min.js','head','',1));
        Context::loadFile(array('https://cdn.jsdelivr.net/crypto-js/3.1.2/rollups/hmac-md5.js','head','',1));
        Context::loadFile(array('https://cdn.jsdelivr.net/crypto-js/3.1.2/rollups/hmac-sha1.js','head','',1));
        Context::loadFile(array('https://cdn.jsdelivr.net/crypto-js/3.1.2/rollups/hmac-sha256.js','head','',1));
        Context::loadFile(array('https://cdn.jsdelivr.net/crypto-js/3.1.2/rollups/hmac-sha512.js','head','',1));
        Context::addHtmlHeader($js);
    }





}