<?php
    /**
     * @class  lotteryController
     * @author 러키군 (admin@barch.kr)
     * @brief  lottery 모듈의 Controller class
     **/

    class lotteryController extends lottery {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 복권 구입처리
         **/
        function procLotteryBuy() {
            $oModuleModel = &getModel('module');
            $oPointModel = &getModel('point');
            $oLotteryModel = &getModel('lottery');

            // 로그인정보 가져오기
            $logged_info = Context::get('logged_info');
            if(!$logged_info->member_srl) return $this->stop('msg_not_permitted');

            // 설정 정보 가져오기
            $lottery_config = $oModuleModel->getModuleConfig('lottery');
            
            // 포인트를 구해옴
            $logged_info->point = $oPointModel->getPoint($logged_info->member_srl);

            // 스크래치(?)를 긁은상태에서 구입할경우..
            if($_SESSION['lottery'][$logged_info->member_srl]['temp']) return new Object(-1,'already_temp');

            // 수령하지 않은 포인트가 있을때...
            if($_SESSION['lottery'][$logged_info->member_srl]['rank']) return new Object(-1,sprintf(Context::getLang('already_lottery'),$_SESSION['lottery'][$logged_info->member_srl]['rank']));

            // 오늘 참여횟수 구해와서 검사함
            $my_today_count = $oLotteryModel->MyLogCount($logged_info->member_srl,1);
            if($lottery_config->today_max_count && $my_today_count >= $lottery_config->today_max_count) return new Object(-1,sprintf(Context::getLang('today_count_overflow'),$lottery_config->today_max_count));

            // 포인트검사
            if($lottery_config->price > $logged_info->point) return new Object(-1,'lottery_point_error');

            // 당첨번호 생성
            $number_list = $oLotteryModel->NumberCreation($lottery_config);
            $my_number = rand(1,100);
            foreach($number_list as $key => $val) {
                if($my_number >= $val['min'] && $my_number <= $val['max']) {
                    $my_rank = $key;
                    $my_point = $val['point'];
                    break;
                }
            }

            $data = array();
            if($my_rank) {
                $data['text'] = sprintf(Context::getLang('lottery_rank'),$my_rank);
                $data['message'] = str_replace(array("[title]","[price]","[point]","[rank]"),array($lottery_config->title,$lottery_config->price,$my_point,$my_rank),$lottery_config->message);
                $_SESSION['lottery'][$logged_info->member_srl]['rank'] = $my_rank;
            } else {
                $data['text'] = Context::getLang('fail_text');
                $data['message'] = str_replace(array("[title]","[price]","[point]","[rank]"),array($lottery_config->title,$lottery_config->price,0,$data['text']),$lottery_config->fail_message);
            }

            // 포인트차감
            if($lottery_config->price) {
                $oPointController = &getController('point');
                $oPointController->setPoint($logged_info->member_srl,$lottery_config->price, 'minus');
            }
            $_SESSION['lottery'][$logged_info->member_srl]['temp'] = true;

            // 구입로그 남기기
            $args = null;
            $args->data_srl = getNextSequence();
            $args->member_srl = $logged_info->member_srl;
            $args->category_srl = 1;
            $args->rank = $my_rank;
            $args->point = $lottery_config->price;
            $args->content = sprintf("%s(%s) 복권 구입-%s",$logged_info->user_id,$logged_info->nick_name,$data['text']);
            $this->InsertLog($args);

            // 성공 메세지 등록
            $this->add('data',$data);
            $this->setMessage("confirm_lottery_buy");
        }


        /**
         * @brief 당첨금수령 (세션에 등수정보가 있으면, 그에 해당하는 포인트를 지급함)
         **/
        function procLotteryPointReceive() {
            $oModuleModel = &getModel('module');
            $oPointModel = &getModel('point');
            $oLotteryModel = &getModel('lottery');

            // 로그인정보 가져오기
            $logged_info = Context::get('logged_info');
            if(!$logged_info->member_srl) return $this->stop('msg_not_permitted');

            // 설정 정보 가져오기
            $lottery_config = $oModuleModel->getModuleConfig('lottery');

            // 수령받을 포인트가 없으면..
            if(!$_SESSION['lottery'][$logged_info->member_srl]['rank'] || $_SESSION['lottery'][$logged_info->member_srl]['rank'] > 5) return new Object(-1,'lottery_receive_error');

            // 등수에따른 받을포인트 설정
            $rank = $_SESSION['lottery'][$logged_info->member_srl]['rank'];
            switch($rank) {
                case 1:
                    $point = $lottery_config->first_point;
                    break;
                case 2:
                    $point = $lottery_config->two_point;
                    break;
                case 3:
                    $point = $lottery_config->three_point;
                    break;
                case 4:
                    $point = $lottery_config->four_point;
                    break;
                default:
                    $point = $lottery_config->five_point;
                    break;
            }
            unset($_SESSION['lottery'][$logged_info->member_srl]['rank']);

            // 포인트증가
            $oPointController = &getController('point');
            $oPointController->setPoint($logged_info->member_srl,$point, 'add');

            // 당첨금수령 로그 남기기
            $args = null;
            $args->data_srl = getNextSequence();
            $args->member_srl = $logged_info->member_srl;
            $args->category_srl = 2;
            $args->rank = $rank;
            $args->point = $point;
            $args->content = sprintf("%s(%s) 복권 당첨금 수령",$logged_info->user_id,$logged_info->nick_name);
            $this->InsertLog($args);

            // 성공 메세지 등록
            $this->add('reload',1);
            $this->setMessage(sprintf(Context::getLang('lottery_receive_message'),$rank,$point));
        }

        /**
         * @brief 로그처리 함수
         * category_srl (1구매 2수령)
         **/
        function InsertLog($args) {
            if(!$args->data_srl) return;
            $output = executeQuery("lottery.insertLog",$args);
            if(!$output->toBool()) return $output;
        }
    }
?>
