<?php
    /**
     * @class  lotteryModel
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 Model class
     **/

    class lotteryModel extends module {
        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 회원의 로그갯수 리턴(오늘)
         **/
        function MyLogCount($member_srl,$category_srl) {
            $args = null;
            $args->member_srl = $member_srl;
            $args->category_srl = $category_srl;
            $args->regdate = date("Ymd");

            $output = executeQuery("lottery.getLogCount",$args);
            return (int)$output->data->count;
        }
        /**
         * @brief 당첨번호 생성
         **/
        function NumberCreation($lottery_config) {
            $arr = array(
                1 => array("min" => 0,"max" => 0),
                2 => array("min" => 0,"max" => 0),
                3 => array("min" => 0,"max" => 0),
                4 => array("min" => 0,"max" => 0),
                5 => array("min" => 0,"max" => 0)
            );
            $fail_per = (int)(100 - ($lottery_config->first_per+$lottery_config->two_per+$lottery_config->three_per+$lottery_config->four_per+$lottery_config->five_per));
            if($fail_per < 1) $arr[0] = array("min" => 0, "max" => 0);
            else $arr[0] = array("min" => 1, "max" => $fail_per);

            $arr[1] = array("min" => $arr[0]['max']+1,"max" => $arr[0]['max']+$lottery_config->first_per,"point" => $lottery_config->first_point);
            $arr[2] = array("min" => $arr[1]['max']+1,"max" => $arr[1]['max']+$lottery_config->two_per,"point" => $lottery_config->two_point);
            $arr[3] = array("min" => $arr[2]['max']+1,"max" => $arr[2]['max']+$lottery_config->three_per,"point" => $lottery_config->three_point);
            if($lottery_config->four_per) $arr[4] = array("min" => $arr[3]['max']+1,"max" => $arr[3]['max']+$lottery_config->four_per,"point" => $lottery_config->four_point);
            if($lottery_config->four_per && $lottery_config->five_per) $arr[5] = array("min" => $arr[4]['max']+1,"max" => $arr[4]['max']+$lottery_config->five_per,"point" => $lottery_config->five_point);
            return $arr;
        }
    }
?>
