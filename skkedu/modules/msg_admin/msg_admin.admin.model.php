<?php
    /**
     * @class  msg_adminAdminModel
     * @author Lucky (admin@barch.kr)
     * @brief  msg_admin module의 admin model class
     **/

    class msg_adminAdminModel extends msg_admin {
        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 쪽지 목록을 구함 (관리자)
         **/
        function getMsgList() {

            $message_type = Context::get('message_type');
            if(!$message_type || !in_array($message_type, array('R','S','T'))) $this->stop('msg_not_founded');
    		$oMemberModel = &getModel('member');

			// 검색 옵션 정리
			$args->message_type = $message_type;
            $args->is_readed = Context::get('is_readed');
            $search_target = trim(Context::get('search_target'));
            $search_keyword = trim(Context::get('search_keyword'));

            if($search_target && $search_keyword) {
                switch($search_target) {
                    case 'sender_srl' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_sender_srl = $search_keyword;
                        break;
                    case 'sender_id' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
							$args->s_sender_srl = $oMemberModel->getMemberSrlByUserID($search_keyword);
                        break;
                    case 'receiver_srl' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_receiver_srl = $search_keyword;
                        break;
                    case 'receiver_id' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
							$args->s_receiver_srl = $oMemberModel->getMemberSrlByUserID($search_keyword);
                        break;
                    case 'title' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_title = $search_keyword;
                        break;
                    case 'content' :
                            if($search_keyword) $search_keyword = str_replace(' ','%',$search_keyword);
                            $args->s_content = $search_keyword;
                        break;
					case 'regdate' :
                            $args->s_regdate = ereg_replace("[^0-9]","",$search_keyword);
                        break;
                    case 'regdate_more' :
                            $args->s_regdate_more = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                    case 'regdate_less' :
                            $args->s_regdate_less = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
					case 'readed_date' :
                            $args->s_readed_date = ereg_replace("[^0-9]","",$search_keyword);
                        break;
                    case 'readed_date_more' :
                            $args->s_readed_date_more = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                    case 'readed_date_less' :
                            $args->s_readed_date_less = substr(ereg_replace("[^0-9]","",$search_keyword) . '00000000000000',0,14);
                        break;
                }
            }

            // 정렬
            $args->sort_index = "message_srl"; 
            $args->sort_order = "desc";

			// query id 지정
            $query_id = 'msg_admin.getMsgList';

            // 기타 변수들 정리
            $args->page = Context::get('page');
            $args->list_count = 40;
            $args->page_count = 10;
            return executeQuery($query_id, $args);
		}


        /**
         * @brief 쪽지 목록을 구함 (관리자)
         **/
        function getMsg($message_srl) {
			$args->message_srl = $message_srl;
            return executeQuery('msg_admin.getMsg',$args);
        }
	}
?>
