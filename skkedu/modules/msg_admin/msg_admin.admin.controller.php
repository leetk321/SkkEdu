<?php
    /**
     * @class  Msg_adminAdminController
     * @author Lucky (admin@barch.kr)
     * @brief  msg_admin module의 admin controller class
     **/

    class msg_adminAdminController extends msg_admin {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 쪽지보내기 (관리자용)
         **/
        function procMsg_adminAdminInsert() {
			// 필수 정보들을 미리 추출
            $args = Context::gets('title','content','receiver_target');

			// 변수 검사
			$title = trim($args->title);
			$content = trim($args->content);
			if(!$title) return new Object(-1, 'msg_title_is_null');
			if(!$content) return new Object(-1, 'msg_content_is_null');

            // 로그인정보 구해옴
			$logged_info = Context::get('logged_info');

			// receiver_target에 따라 받는이 지정
			$oMemberModel = &getModel('member');
            $oCommunicationController = &getController('communication');
			$target = array();
			switch ($args->receiver_target) {
            case -1: // 특정 사용자
			    $args->receiver_value = Context::get('receiver_value');
                $receiver_list = explode('|@|', $args->receiver_value);
                if(!count($receiver_list)) return new Object(-1, 'null_user_id');

			    $receiver_count = count($receiver_list);
                for($i=0;$i<$receiver_count;$i++) {
                    $user_id = trim($receiver_list[$i]);
					$member_srl = $oMemberModel->getMemberSrlByUserID($user_id);
                    if(!$user_id || !$member_srl) continue;
                    $target[] = $member_srl;
                }
				break;
            case -2: // 특정 그룹 
			    $args->receiver_group = Context::get('receiver_group');
                $receiver_list = explode('|@|', $args->receiver_group);
                if(!count($receiver_list)) return new Object(-1, 'msg_check_group');

			    $receiver_count = count($receiver_list);
                $group_srl_list = array();
                for($i=0;$i<$receiver_count;$i++) {
                    $group_srl = (int)trim($receiver_list[$i]);
                    if(!$group_srl) continue;
					$group_srl_list[] = $group_srl;
                }

                // 그룹에 해당하는 회원정보 구해옴
                $output = $this->getMemberListWithin($group_srl_list);
                if(!$output->toBool()) return $output;
				$groups_data = $output->data;
                foreach($groups_data as $key => $val) {
                    $target[] = $val->member_srl;
                }
				break;
            default: // 전체 사용자
                // 전체 회원정보 구해옴
                $output = $this->getMemberTotal();
                if(!$output->toBool()) return $output;
                if(!$output->data) break;
                $members_data = $output->data;
                foreach($members_data as $key => $val) {
                    $target[] = $val->member_srl;
				}
                break;
            }

            if(!count($target)) return new Object(-1,'msg_not_founded');

			// 쪽지 발송
			foreach($target as $receiver_srl) {
				if($receiver_srl == $logged_info->member_srl) continue;
			    $output = $oCommunicationController->sendMessage($logged_info->member_srl, $receiver_srl, $title, $content);
                if(!$output->toBool()) continue;
			}

            $this->add('message_type', Context::get('message_type'));
			$this->add('page', Context::get('page'));

			$this->setMessage("success_registed");
        }

		/**
         * @brief 쪽지수정 (관리자용)
         **/
        function procMsg_adminAdminModify() {
            // 필수 정보들을 미리 추출
            $args = Context::gets('message_srl','message_type','title','content','readed','readed_date');

            // 쪽지수정
			$output = $this->updateMsg($args);
            if(!$output->toBool()) return $output;

            $this->add('message_srl', Context::get('message_srl'));
            $this->add('message_type', Context::get('message_type'));
			$this->add('page', Context::get('page'));
			$this->setMessage("success_updated");
        }


		/**
         * @brief 그룹에 해당하는 회원목록을 구함 (관리자)
         **/
        function getMemberListWithin($group_srl) {
            $args->group_srls = implode(',',$group_srl);
            return executeQuery('msg_admin.getMemberTotalsByGroup',$args);
        }
		/**
         * @brief 전체 회원목록을 구함 (관리자)
         **/
        function getMemberTotal() {
            return executeQuery('msg_admin.getMemberTotals');
        }

		/**
         * @brief 쪽지 수정처리 (관리자용)
         **/
        function updateMsg($args) {
			if(!$args) return new Object(-1, 'invalid_msg');

			// 변수 검사
			$args->title = trim($args->title);
			$args->content = trim($args->content);
			if(!$args->message_srl) return new Object(-1, 'invalid_msg');
			if(!$args->title) return new Object(-1, 'msg_title_is_null');
			if(!$args->content) return new Object(-1, 'msg_content_is_null');
            if(!$args->readed || !in_array($args->readed, array('Y','N'))) $args->readed = "N";
			if($args->readed != "Y" || $args->readed_date) $args->readed_date = "";
            if(!$args->message_type || !in_array($args->message_type, array('R','S','T'))) return new Object(-1, 'msg_invalid_request');

            // 쪽지가 존재하는지 검사함
            $oMsgadminAdminModel = &getAdminModel('msg_admin');
			$output = $oMsgadminAdminModel->getMsg($args->message_srl);
			$message = $output->data;
			if(!$message->message_srl) return new Object(-1, 'invalid_msg');
			if($args->message_srl != $message->message_srl) $args->message_srl = $message->message_srl;
			if($args->message_type != $message->message_type) $args->message_type = $message->message_type;

            return executeQuery('msg_admin.updateMsg', $args);
		}

		/**
         * @brief 쪽지 삭제 (관리자용)
         **/
        function procMsg_adminAdminDelete() {
            $message_srl = Context::get('message_srl');
			if(!$message_srl) return new Object(-1, 'msg_invalid_request');
            $args->message_srls = $message_srl;

            $oMsgadminAdminModel = &getAdminModel('msg_admin');
			$output = $oMsgadminAdminModel->getMsg($args->message_srls);
			$message = $output->data;

            if(!$message || ($message_srl != $message->message_srl)) return new Object(-1, 'invalid_msg');

            // 삭제
            $args->message_type = $message->message_type;
            $output = $this->deleteMsg($args);
            if(!$output->toBool()) return $output;

            $this->add('page',Context::get('page'));
            $this->add('message_type',Context::get('message_type'));
			$this->setMessage("success_deleted");
        }

		/**
         * @brief 쪽지 일괄삭제 (관리자용)
         **/
        function procMsg_adminAdminAllDelete() {

            // 변수 체크
            $cart = trim(Context::get('cart'));
            if(!$cart) return new Object(-1, 'msg_cart_is_null');

            $cart_list = explode('|@|', $cart);
            if(!count($cart_list)) return new Object(-1, 'msg_cart_is_null');

            $message_type = Context::get('message_type');
            if(!$message_type || !in_array($message_type, array('R','S','T'))) return new Object(-1, 'msg_invalid_request');

			$message_count = count($cart_list);
            $target = array();
            for($i=0;$i<$message_count;$i++) {
                $message_srl = (int)trim($cart_list[$i]);
                if(!$message_srl) continue;
                $target[] = $message_srl;
            }
            if(!count($target)) return new Object(-1,'msg_cart_is_null');

            // 삭제
            $args->message_srls = implode(',',$target);
            $args->message_type = $message_type;
            $output = $this->deleteMsg($args);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_deleted');
		}

		/**
         * @brief 쪽지 삭제처리 (관리자용)
         **/
        function deleteMsg($args) {
			if(!$args) return false;
            return executeQuery('msg_admin.deleteMsg', $args);
		}
	}
?>
