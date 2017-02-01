<?php
    /**
     * @file   ko.lang.php
     * @author 러키군 (admin@barch.kr)
     * @brief  복권 모듈의 기본 언어팩
     **/
    $lang->lottery = "포인트복권";
    $lang->lottery_rank = "%d등";
    $lang->lottery_point = "%d등 당첨금";
    $lang->lottery_per = "%d등 당첨확률";
    $lang->lottery_price = "참가비용";
    $lang->today_max_count = "하루 횟수제한";
    $lang->lottery_message = "당첨 메세지";
    $lang->lottery_fail_message = "꽝 메세지";
    $lang->lottery_sample_code = "샘플코드";
    $lang->fail_text = "꽝!"; // 복권 긁을때 꽝메세지..
    $lang->data_srl = "고유번호";
    $lang->rank = "순위";
    $lang->point = "포인트";

    //  메뉴별 이름
    $lang->log_list = "로그관리";

    // 관리자페이지 -> 로그관리 검색대상
    $lang->lottery_search_target = array(
        "s_data_srl" => "고유번호",
        "s_member_srl" => "회원번호",
        "s_rank" => "순위",
        "s_point" => "포인트",
        "s_point_more" => "포인트(이상)",
        "s_point_less" => "포인트(이하)",
        "s_content" => "설명",
        "s_ipaddress" => "IP"
    );

    $lang->confirm_lottery_buy = "구매 했습니다. 복권을 마우스로 긁어보세요.";
    $lang->already_temp = "새로고침 후 다시 구입해주시기 바랍니다.";
    $lang->already_lottery = "수령하지 않은 포인트가 남아있습니다. (%d등)";
    $lang->lottery_per_overflow = "당첨확률의 합이 100을 초과할수 없습니다.";
    $lang->today_count_overflow = "복권은 하루 %d회만 참여 가능합니다.";
    $lang->lottery_point_error = "포인트가 부족합니다.";
    $lang->lottery_receive_error = "수령받을 포인트가 없습니다.";

    $lang->lottery_receive_message = "%d등 당첨금 %d포인트를 수령했습니다.";
    $lang->today_max_count_about = "하루 참여횟수를 제한할수 있습니다. (0 입력시 제한없음)";
    $lang->lottery_per_about = "꽝 걸릴확률 = (100- (1+2+3+4+5등확률) 입니다.<br />현재 설정대로라면 꽝 걸릴확률은 %s 입니다.<br />+ 만약 1~5등 당첨확률이 100을 넘으면 꽝은 없습니다.";
    $lang->lottery_message_about = "당첨됬을경우 출력되는 메세지를 설정할수 있습니다.<br />[rank] - 순위<br />[price] - 참가비용<br />[title] - 브라우저 제목<br />[point] - 당첨금<br/><br />(↓ 꽝 메세지에도 치환이 적용됩니다.)";
    $lang->lottery_sample_code_about = "포인트복권  모듈은 별개의 mid없이 ?module=lottery 를통해 접근합니다.";
?>