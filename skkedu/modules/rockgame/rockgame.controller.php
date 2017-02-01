<?php
    
	//사용자 컨트롤러

    class rockgameController extends rockgame {

        //초기화
        function init() {
		
        }

		//게임결과연산
        function procRockgameUserView() {
			
			//로그인 하지 않을경우 리턴
			$logged_info = Context::get('logged_info');
			if(!$logged_info) return new Object(-1,'로그인해주세요');
			
			//입력값받음
			$args = Context::getRequestVars();
			
			//참여포인트 입력값		
			$setpoint = $args->game_point;
			
			//포인트제한 설정값
			$maxpoint = $this->module_info->maxpoint;
			
			//참여포인트가 0보다 작거나 제한값보다 높으면 리턴
			$over_point_msg = sprintf("참가포인트를 1 ~ %s 사이로 입력해주세요",$maxpoint);
			if($setpoint > $maxpoint || $setpoint <= 0) $this->setMessage($over_point_msg);
			if($setpoint > $maxpoint || $setpoint <= 0) return $this->setRedirectUrl(getNotEncodedUrl('act','dispRockgameUserView')); //초기화면
			
			//포인트 부족할 경우 리턴
			$args->member_srl = $logged_info->member_srl;
			$oPointModel = &getModel('point');
			$point = $oPointModel->getPoint($logged_info->member_srl);
			if($point < $setpoint) $this->setMessage('포인트가 부족합니다');
			if($point < $setpoint) return $this->setRedirectUrl(getNotEncodedUrl('act','dispRockgameUserView')); //초기화면
						
			//하루 게임횟수제한 설정값
			$maxgame = $this->module_info->maxgame;
			
			//오늘 게임횟수 불러옴
			$oRockgameModel = &getModel('rockgame');
			$game_count = $oRockgameModel->getRockgameCount(); //오늘 게임횟수 불러옴
			
			//게임횟수가 제한값을 넘으면 리턴
			if($game_count >= $maxgame) $this->setMessage('오늘은 더이상 게임에 참여하실수 없습니다');
			if($game_count >= $maxgame) return $this->setRedirectUrl(getNotEncodedUrl('act','dispRockgameUserView')); //초기화면
			
			
			//유저선택
			$user_select = $args->rsp_slect;
			
			//컴터선택
			$rps = rand(1,3);
			if($rps == 1){$com_select = 'rock';}
			if($rps == 2){$com_select = 'scissors';}
			if($rps == 3){$com_select = 'paper';}
			
			
			//게임결과
			if($user_select == 'rock' && $com_select == 'rock')		{$result = 'draw';}
			if($user_select == 'rock' && $com_select == 'paper')	{$result = 'lose';}
			if($user_select == 'rock' && $com_select == 'scissors')	{$result = 'win';}
			
			if($user_select == 'paper' && $com_select == 'paper')	{$result = 'draw';}
			if($user_select == 'paper' && $com_select == 'scissors'){$result = 'lose';}
			if($user_select == 'paper' && $com_select == 'rock')	{$result = 'win';}
			
			if($user_select == 'scissors' && $com_select == 'scissors') {$result = 'draw';}
			if($user_select == 'scissors' && $com_select == 'rock')		{$result = 'lose';}
			if($user_select == 'scissors' && $com_select == 'paper')	{$result = 'win';}
			
						
			//입력값설정 (db입력용)
			$logged_info = Context::get('logged_info');
			$args->member_srl = $logged_info->member_srl;
			$args->nick_name = $logged_info->nick_name;
			$args->regdate = date('Y-m-d H:i:s');
			$args->com_select = $com_select;
			$args->user_select = $user_select;
			$args->result = $result;
			$args->set_point = $args->game_point;
			if($result == 'win') $args->game_point = +$args->game_point;
			if($result == 'lose')$args->game_point = -$args->game_point;
			if($result == 'draw')$args->game_point = 0;
						
						
			//이상없으면 게임내용 db입력
			$output = executeQuery("rockgame.insert_game",$args);
			
			
			//db입력후 게임결과에 따라 포인트처리
			$oPointController = &getController('point');
			if($result == 'lose'){$oPointController->setPoint($logged_info->member_srl, $setpoint, 'minus');}
			if($result == 'win'){$oPointController->setPoint($logged_info->member_srl, $setpoint, 'add');}
			
			//게임결과로 돌아감
			$this->setRedirectUrl(getNotEncodedUrl('act','dispRockgameUserResult'));  //결과화면
		}
		
	}
?>