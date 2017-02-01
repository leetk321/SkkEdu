<?php
    /**
     * @class  lottery
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 high class
     **/

    class lottery extends ModuleObject {

        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            // lottery 모듈의 기본설정 저장
            $config = null;
            $config->title = "포인트복권";
            $config->skin = "default";
            $config->message = "축하드립니다.!!\n[title] [rank]등에 당첨되셨습니다.\n왼쪽 수령버튼을 통해 포인트를 받아가세요 ~";
            $config->fail_message = "꽝!";
            $config->price = 50;
            $config->today_max_count = 0;
            $config->first_point = 220;
            $config->two_point = 170;
            $config->three_point = 120;
            $config->four_point = 80;
            $config->five_point = 50;
            $config->first_per = 1;
            $config->two_per = 2;
            $config->three_per = 3;
            $config->four_per = 6;
            $config->five_per = 8;

            $oModuleController->insertModuleConfig('lottery', $config);

            return new Object();
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');

            // lottery 모듈의 기본설정이 없으면...
            $lottery_config = $oModuleModel->getModuleConfig('lottery');
            if(!$lottery_config) return true;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            $oDB = &DB::getInstance();
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            // lottery 모듈의 기본설정 저장
            $lottery_config = $oModuleModel->getModuleConfig('lottery');
            if(!$lottery_config) {
                $config = null;
                $config->title = "포인트복권";
                $config->skin = "default";
                $config->message = "축하드립니다.!!\n[title] [rank]등에 당첨되셨습니다.\n왼쪽 수령버튼을 통해 포인트를 받아가세요 ~";
                $config->fail_message = "꽝!";
                $config->price = 50;
                $config->today_max_count = 0;
                $config->first_point = 220;
                $config->two_point = 170;
                $config->three_point = 120;
                $config->four_point = 80;
                $config->five_point = 50;
                $config->first_per = 1;
                $config->two_per = 2;
                $config->three_per = 3;
                $config->four_per = 6;
                $config->five_per = 8;
                $oModuleController->insertModuleConfig('lottery', $config);
            }

            return new Object(0, 'success_updated');
        }

		function moduleUninstall() {
            return new Object();
		}

        /**
         * @brief 캐시 파일 재생성
         **/
        function recompileCache() {
        }

    }
?>
