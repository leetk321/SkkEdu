<?php
    /**
     * @class  lotteryView
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 View class
     **/

    class lotteryView extends lottery {

        /**
         * @brief 초기화
         * board 모듈은 일반 사용과 관리자용으로 나누어진다.
         **/
        function init() {
            $oModuleModel = &getModel('module');

            // 설정 정보 가져오기
            $lottery_config = $oModuleModel->getModuleConfig('lottery');

            // 브라우저 타이틀 설정
            Context::addBrowserTitle($lottery_config->title);

            // 설정 변수 지정
            Context::set('lottery_config', $lottery_config);

            // template path지정
            $template_path = sprintf("%sskins/%s/",$this->module_path, $lottery_config->skin);
            if(!is_dir($template_path)||!$lottery_config->skin) {
                $lottery_config->skin = 'default';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $lottery_config->skin);
            }
            $this->setTemplatePath($template_path);

            // module에서 공통으로 쓰이는 js 파일 로드
            Context::addJsFile($this->module_path.'tpl/js/lottery.js');
        }

        /**
         * @brief 복권구매페이지 출력
         **/
        function dispLotteryBuy() {
            $this->setLayoutFile('popup_layout');
            $oPointModel = &getModel('point');
            $oLotteryModel = &getModel('lottery');

            // 로그인정보 가져오기
            $logged_info = Context::get('logged_info');
            if(!$logged_info->member_srl) return $this->stop('msg_not_permitted');
            
            // 포인트/레벨을 구해옴
            $logged_info->point = $oPointModel->getPoint($logged_info->member_srl);
            Context::set('logged_info',$logged_info);

            // 오늘 참여횟수 구해옴
            $my_today_count = $oLotteryModel->MyLogCount($logged_info->member_srl,1);
            Context::set('my_today_count',$my_today_count);

            unset($_SESSION['lottery'][$logged_info->member_srl]['temp']);
            $this->setTemplateFile('buy');
        }
    }
?>
