<?php
    
	//관리자 컨트롤러

    class rockgameAdminController extends rockgame {

        //초기화
        function init() {           
        }

		//관리자 모듈 설정
		function procRockgameAdminStart(){
			
			//입력값을 모두 받음
            $args = Context::getRequestVars();
			$args->module = 'rockgame';

			//모듈등록 유무에 따라 insert/update
			$oModuleController = &getController('module');
			if(!$args->module_srl){
				$output = $oModuleController->insertModule($args); //모듈insert
				$this->setMessage('success_registed');
			}else{ 
				$output = $oModuleController->updateModule($args); //모듈update
				$this->setMessage('success_updated');
			}
            
			if(!$output->toBool()) return $output;
			
			//모듈시작 화면으로 돌아감
			$this->setRedirectUrl(getNotEncodedUrl('','module','admin','act','dispRockgameAdminStart')); 

		}
		
		//선택로그 삭제
        function procRockgameAdminLogDelete() {
            $game_srls = Context::get('game_srls');
            if(!$game_srls) return new Object(-1,'선택 대상이 없습니다');

            $game_srl_list = explode("@",$game_srls);
            foreach($game_srl_list as $key => $val) {
                // 루프돌면서 선택된 로그 삭제
                $args = null;
                $args->game_srl = $val;
                $this->DeleteLog($args);
            }
            $this->setMessage('success_deleted');
        }
        
		//전체로그 삭제
        function procRockgameAdminLogDeleteAll() {
            $this->DeleteLogAll();
            $this->setMessage('success_deleted');
        }
		
		////////////// 로그삭제를 위한 메서드, module.xml에 등록하지 않음 ★시작★ ////////////////////
       
	   function DeleteLog($args) { //game_srl을 값으로 받아서 삭제
            $output = executeQuery("rockgame.deleteLog",$args);
            if(!$output->toBool()) return $output;
        }
        function DeleteLogAll() {
            $output = executeQuery("rockgame.deleteLog");
            if(!$output->toBool()) return $output;
        }

		////////////// 로그삭제를 위한 메서드, module.xml에 등록하지 않음 ★끝★  ////////////////////
       
    }
?>