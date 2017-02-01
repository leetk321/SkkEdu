<?php
    
	//관리자 뷰

    class rockgameAdminView extends rockgame {

        //초기화
        function init() {
		
			//모듈정보구함
			$oModuleModel = &getModel('module');
			$oRockgameModel = &getModel('rockgame');
            $this->module_info = $oRockgameModel->getRockgameInfo();
            $this->module_config = $oModuleModel->getModuleConfig('rockgame');		
			
			//모듈정보세팅
			Context::set('module_config', $this->module_config);
            Context::set('module_info', $this->module_info);
			
            // 관리자 템플릿 파일의 경로 설정 (tpl)
            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);
        }

        //관리자 모듈설정
        function dispRockgameAdminStart() {
		
			// 모듈 카테고리 목록 구함
			$oModuleModel = &getModel('module');
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);
			
			// 스킨 목록 구함
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

			//모바일 스킨 목록 구함
			$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
			Context::set('mskin_list', $mskin_list);

            // 레이아웃 목록 구함
            $oLayoutModel = &getModel('layout');
            $layout_list = $oLayoutModel->getLayoutList();
            Context::set('layout_list', $layout_list);
			
			// 모바일 레이아웃 목록 구함
			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('mlayout_list', $mobile_layout_list);
			
            // 템플릿 지정(tpl/index.html)
            $this->setTemplateFile('index');
        }
		
		
		//게임로그
		function dispRockgameAdminLog(){
			$args->page = Context::get('page');
			
			//검색기능사용시
			$args->search_target = Context::get('search_target');
			$args->search_keyword = Context::get('search_keyword');
			$search_target = trim($args->search_target);
            $search_keyword = trim($args->search_keyword);
			
			//검색결과 변수에 넣음
			if($search_target && $search_keyword) {
                switch($search_target) {
                    case 'member_srl' :
						$args->s_member_srl = (int)$search_keyword;
                        break; 
                    case 'ipaddress' :
						$args->s_ipaddress = $search_keyword;
                        break;
                    case 'result' :
						if($search_keyword == '승리') $args->s_result = 'win';
						if($search_keyword == '패배') $args->s_result = 'lose';
						if($search_keyword == '비김') $args->s_result = 'draw';
                        break;
                }
            }
			
			//로그 가져옴
			$output = executeQuery('rockgame.getRockgameLog',$args);
			
			//결과값 세팅
			Context::set('game_log',$output->data);
			
			//페이지 세팅
			Context::set('total_count', $output->total_count);
			Context::set('total_page', $output->total_page);
			Context::set('page_list', $output->data);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);
			
			//템플릿 파일 지정
			$this->setTemplateFile('game_log');
		}
		
		
		//스킨관리 
		function dispRockgameAdminSkinInfo() {
			$oModuleAdminModel = &getAdminModel('module');
			$skin_content = $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl);
			Context::set('skin_content', $skin_content);

			// 템플릿 파일 지정			
			$this->setTemplateFile('skin_info');
        }	
		
		//권한관리
		function dispRockgameAdminGrantSet() {
			$oModuleAdminModel = &getAdminModel('module');
			$grant_content = $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant);
			Context::set('grant_content', $grant_content);
			
			//템플릿 파일 지정
			$this->setTemplateFile('grant_list');
		}
    }
?>