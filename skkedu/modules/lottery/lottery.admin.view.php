<?php
    /**
     * @class  lotteryAdminView
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 admin view class
     **/

    class lotteryAdminView extends lottery {

        /**
         * @brief 초기화
         *
         **/
        function init() {
            // template path지정
            $this->setTemplatePath($this->module_path.'tpl');
        }

        /**
         * @brief 기본 설정
         **/
        function dispLotteryAdminConfig() {
            // 설정 정보 가져오기
            $oModuleModel = &getModel('module');
            $lottery_config = $oModuleModel->getModuleConfig('lottery');
            $module_category = $oModuleModel->getModuleCategories();

            // 템플릿에서 사용하기 위 set
            Context::set('lottery_config', $lottery_config);
            Context::set('module_category', $module_category);

            // 스킨목록 가져오기
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            // 템플릿 파일 지정
            $this->setTemplateFile('config');
        }

        /**
         * @brief 로그관리
         **/
        function dispLotteryAdminLogList() {
            // 로그정보 가져오기
            $oLotteryAdminModel = &getAdminModel("lottery");
            $log_list = $oLotteryAdminModel->getLogList();

            Context::set('total_count', $log_list->total_count);
            Context::set('total_page', $log_list->total_page);
            Context::set('page', $log_list->page);
            Context::set('log_list',$log_list->data);
            Context::set('page_navigation', $log_list->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('log_list');
        }
    }
?>
