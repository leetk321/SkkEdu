<?php
    
	//사용자 모델

    class rockgameModel extends rockgame {

        //초기화
        function init() {           
        }

		//모듈정보구함
		function getRockgameInfo(){
			$output = executeQuery('rockgame.getRockgameInfo');
            if(!$output->data->module_srl) return;
			
            $oModuleModel = &getModel('module');
            $module_info = $oModuleModel->getModuleInfoByModuleSrl($output->data->module_srl);
			
            return $module_info;
        }
		
		//게임결과 구함
		function getRockgameResult(){
			$args = null;
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
			
			$output = executeQuery('rockgame.getRockgameResult',$args);
			
			//배열을 풀어줌
			if($output->data) {
                foreach($output->data as $val) {
                    $obj = null;
                    $obj->result = $val->result;
					$obj->set_point = $val->set_point;
                    $obj->user_select = $val->user_select;
                    $obj->com_select = $val->com_select;
                }
                return $obj;
            }
		}
		
		//오늘의 게임참여횟수 구함 (비긴 결과는 제외)
		function getRockgameCount(){
			$args = null;
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
			$args->regdate = date('Y-m-d');
			
			$args->result = 'win';
			$output_win = executeQuery('rockgame.getRockgameCount',$args);
			
			$args->result = 'lose';
			$output_lose = executeQuery('rockgame.getRockgameCount',$args);
			
			return (int)$output_win->data->game_count + $output_lose->data->game_count;
		}
		
		//전체 게임 참여횟수 구함 (비긴결과 제외)
		function getRockgameCountAll(){
			$args = null;
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
			
			$args->result = 'win';
			$output_win = executeQuery('rockgame.getRockgameCount',$args);
			
			$args->result = 'lose';
			$output_lose = executeQuery('rockgame.getRockgameCount',$args);
			
			return (int)$output_win->data->game_count + $output_lose->data->game_count;
		}
			
		//누적승률 구함
		function getRockgameRate(){
			$args = null;
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
			
			$total_count = $this->getRockgameCountAll(); //전체 게임횟수 (비긴결과제외)
			
			$args->result = 'win';
			$win_count = executeQuery('rockgame.getRockgameCountWin',$args);
			
			//값이 있을경우만 계산 없을경우 빈문자열 반환
			if($total_count && $win_count->data->count_win){
				$total	= $total_count;
				$win 	= $win_count->data->count_win;
				$game_rate = $win / $total * 100;
				return (int)$game_rate;
			}else{
				return $game_rate = '';
			}
		}
		
		//오늘승률 구함
		function getRockgameRateToday(){
			$args = null;
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
						
			$today_count = $this->getRockgameCount(); //오늘 게임횟수 (비긴결과제외)
			
			$args->result = 'win';
			$args->regdate = date('Y-m-d');
			$win_count = executeQuery('rockgame.getRockgameCountWin',$args);
			
			//값이 있을경우만 계산 없을경우 빈문자열 반환
			if($today_count && $win_count->data->count_win){
				$today	= $today_count;
				$win 	= $win_count->data->count_win;
				$game_rate = $win / $today * 100;
				return (int)$game_rate;
			}else{
				return $game_rate = '';
			}
		}
		
		//오늘의 포인트 합계 구함 (유저)
		function getRockgamePointSum(){
			$args = null;
			$args->regdate = date('Y-m-d');
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
			
			$output = executeQuery('rockgame.getRockgamePointSum',$args);
			
			return $output->data->point_sum_user;
		}
		
		//오늘의 포인트 순위 구함 (오름순)
		function getRockgamePointRankDesc(){
			$args = null;
			$args->regdate = date('Y-m-d');
			
			$output = executeQuery('rockgame.getRockgamePointRankDesc',$args);
			
			//데이터가 없을 경우 배열생성 
			if(!$output->data) $output->data = array();
			return $output->data;
		}
		
		//오늘의 포인트 순위 구함 (내림순)
		function getRockgamePointRankAsc(){
			$args = null;
			$args->regdate = date('Y-m-d');
			
			$output = executeQuery('rockgame.getRockgamePointRankAsc',$args);
			
			//데이터가 없을 경우 배열생성 
			if(!$output->data) $output->data = array();
			return $output->data;
		}
		
    }
?>