<?php
    
	//사용자 뷰

    class rockgameView extends rockgame {

        //초기화
        function init() {
            // 사용자 템플릿 파일의 경로 설정 (skins)
			$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $this->module_info->skin = 'default';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }
            $this->setTemplatePath($template_path);
        }

        //게임화면
        function dispRockgameUserView() {
			
			$oRockgameModel = &getModel('rockgame');
			$game_count = $oRockgameModel->getRockgameCount(); //오늘 게임횟수 불러옴
			$game_rate = $oRockgameModel->getRockgameRateToday(); //오늘 승률 불러옴
			$game_rank_desc = $oRockgameModel->getRockgamePointRankDesc(); //랭킹 불러옴(오름순)
			$game_rank_asc = $oRockgameModel->getRockgamePointRankAsc(); //랭킹 불러옴(내림순)
			$game_point_sum = $oRockgameModel->getRockgamePointSum(); //오늘 포인트 합계 불러옴
				
			
			//불러온 값 템플릿으로 보냄
			Context::set('game_count',$game_count);
			Context::set('game_rate',$game_rate);
			Context::set('game_rank_desc',$game_rank_desc);
			Context::set('game_rank_asc',$game_rank_asc);
			Context::set('game_point_sum',$game_point_sum);
			
			
			// 템플릿 지정(skins/스킨/view.html)
            $this->setTemplateFile('view');
        }
		
		//게임결과
		function dispRockgameUserResult() {
			
			$oRockgameModel = &getModel('rockgame');
			$game_count = $oRockgameModel->getRockgameCount(); //오늘 게임횟수 불러옴
			$game_rate = $oRockgameModel->getRockgameRateToday(); //오늘 승률 불러옴
			$game_rank_desc = $oRockgameModel->getRockgamePointRankDesc(); //랭킹 불러옴(오름순)
			$game_rank_asc = $oRockgameModel->getRockgamePointRankAsc(); //랭킹 불러옴(내림순)
			$game_point_sum = $oRockgameModel->getRockgamePointSum(); //오늘 포인트 합계 불러옴
			$game_result = $oRockgameModel->getRockgameResult(); //게임결과 메세지 구해옴
			
			
			//게임결과에 따른 메세지 입력
			if($game_result->result == 'draw'){$result_msg = '비겼습니다\n다시 도전하세요!';}
			if($game_result->result == 'win') {$result_msg = '축하합니다\n게임에서승리하였습니다\n참가포인트만큼 획득하였습니다.';}
			if($game_result->result == 'lose'){$result_msg = '안타깝네요\n게임에서 졌습니다\n참가포인트만큼 차감하였습니다.';}
			
			
			//불러온 값 템플릿으로 보냄
			Context::set('game_count',$game_count);			//잔여횟수
			Context::set('game_rate',$game_rate);			//오늘승률
			Context::set('game_rank_desc',$game_rank_desc); //랭킹
			Context::set('game_rank_asc',$game_rank_asc); 	//랭킹
			Context::set('game_point_sum',$game_point_sum); //포인트합계
			Context::set('game_result',$game_result);		//게임결과
			Context::set('result_msg',$result_msg);			//게임결과메세지
			
			
			// 템플릿 지정(skins/스킨/result.html)
            $this->setTemplateFile('result');
		
		}
    }
?>