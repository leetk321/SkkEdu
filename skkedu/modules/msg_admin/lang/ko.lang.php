<?php
    /**
     * @file   ko.lang.php
     * @author Lucky (admin@barch.kr)
     * @brief  한국어 언어팩 (기본적인 내용만 수록)
     **/

    $lang->message = '쪽지함';
    $lang->about_message = "쪽지를 보기/수정/삭제,발송 등을 할 수 있는 쪽지함 관리 모듈입니다.";

	// 리스트에서 검색할 대상
	$lang->readed_list = array(
            'Y' => '읽은 쪽지',
            'N' => '읽지않은 쪽지',
    );
	$lang->search_target_list = array(
        'sender_srl' => '보낸이 (회원번호)',
        'sender_id' => '보낸이 (아이디)',
		'receiver_srl' => '받는이 (회원번호)',
        'receiver_id' => '받는이 (아이디)',
		'title' => '제목',
        'content' => '내용',
        'regdate' => '보낸시간',
        'regdate_more' => '보낸시간 (이상)',
        'regdate_less' => '보낸시간 (이하)',
		'readed_date' => '읽은시간',
		'readed_date_more' => '읽은시간 (이상)',
		'readed_date_less' => '읽은시간 (이하)',
	);

    $lang->invalid_msg = "존재하지 않는 쪽지입니다.";
    $lang->msg_cart_is_null = '대상을 선택해주세요.';
	$lang->msg_view = "쪽지 보기";
	$lang->msg_readed_date_info = "yyyymmddhhiiss 형식이며, 상태가 읽음일때만 적용됩니다.";
    $lang->grant_to_member = "특정 사용자";
    $lang->communication_addon_null = "이 모듈을 사용하기위해선, 커뮤니케이션 애드온을 활성화 시켜야 합니다.\n기능설정 -> 애드온에서 활성화 시켜주세요.";
?>
