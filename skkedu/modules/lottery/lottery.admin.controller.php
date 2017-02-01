<?php
    /**
     * @class  lotteryAdminController
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 admin controller class
     **/

    class lotteryAdminController extends lottery {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 기본설정
         **/
        function procLotteryAdminConfig() {
            // 설정 정보 가져오기
            $oModuleModel = &getModel('module');
            $lottery_config = $oModuleModel->getModuleConfig('lottery');

            // 변수 정리
            $args = Context::getRequestVars();
            $first_per = (int)$args->first_per;
            $two_per = (int)$args->two_per;
            $three_per = (int)$args->three_per;
            $four_per = (int)$args->four_per;
            $five_per = (int)$args->five_per;
            $fail_per = (int)($first_per+$two_per+$three_per+$four_per+$five_per);

            // 확률이 100을 넘을경우...
            if($fail_per > 100) return new Object(-1,'lottery_per_overflow');

            $lottery_config->title = trim($args->title);
            $lottery_config->skin = $args->skin;
            $lottery_config->message = $args->message;
            $lottery_config->fail_message = $args->fail_message;
            $lottery_config->price = (int)$args->price;
            $lottery_config->today_max_count = (int)$args->today_max_count;
            $lottery_config->first_per = $first_per;
            $lottery_config->two_per = $two_per;
            $lottery_config->three_per = $three_per;
            $lottery_config->four_per = $four_per;
            $lottery_config->five_per = $five_per;
            $lottery_config->first_point = (int)$args->first_point;
            $lottery_config->two_point = (int)$args->two_point;
            $lottery_config->three_point = (int)$args->three_point;
            $lottery_config->four_point = (int)$args->four_point;
            $lottery_config->five_point = (int)$args->five_point;

            // 저장
            $oModuleController = &getController('module');
            $oModuleController->insertModuleConfig('lottery', $lottery_config);

            $this->setMessage('success_updated');
        }

        /**
         * @brief 로그삭제
         **/
        function procLotteryAdminLogDelete() {
            $data_srls = Context::get('data_srls');
            if(!$data_srls) return new Object(-1,'msg_cart_is_null');

            $data_srl_list = explode(",",$data_srls);
            foreach($data_srl_list as $key => $val) {
                // 데이터 삭제
                $args = null;
                $args->data_srl = $val;
                $this->DeleteLog($args);
            }
            $this->setMessage('success_deleted');
        }
        /**
         * @brief 로그초기화
         **/
        function procLotteryAdminLogReset() {
            $this->ResetLog();
            $this->setMessage('success_deleted');
        }
        function DeleteLog($args) {
            $output = executeQuery("lottery.deleteLog",$args);
            if(!$output->toBool()) return $output;
        }
        function ResetLog() {
            $output = executeQuery("lottery.deleteLog");
            if(!$output->toBool()) return $output;
        }
    }
?>
