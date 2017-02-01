<?php
    /**
     * @class  lotteryAdminModel
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 AdminModel class
     **/

    class lotteryAdminModel extends module {
        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 로그리스트 구해옴
         **/
        function getLogList() {
            $search_keyword = trim(Context::get('search_keyword'));
            $search_target = Context::get('search_target');
            $search_target_list = array("s_data_srl","s_member_srl","s_rank","s_point","s_point_more","s_point_less","s_content","s_ipaddress");
            $s_regdate_less = Context::get('s_regdate_less');
            $s_regdate_more = Context::get('s_regdate_more');

            $args = null;
            if($s_category_srl) $args->s_category_srl = (int)$s_category_srl;
            if($s_regdate_less) $args->s_regdate_less = (int)$s_regdate_less;
            if($s_regdate_more) $args->s_regdate_more = (int)$s_regdate_more;
            if($search_keyword && in_array($search_target,$search_target_list)) {
                if(array_search($search_target,$search_target_list) < 4) $args->{$search_target} = (int)$search_keyword;
                else $args->{$search_target} = $search_keyword;
            }
            $args->sort_index = "regdate";
            $args->sort_order = "desc";
            // 기타 변수들 정리
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $output = executeQuery("lottery.getLogList",$args);
            return $output;
        }
    }
?>
