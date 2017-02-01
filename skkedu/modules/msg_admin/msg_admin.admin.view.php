<?php
    /**
     * @class  msg_adminAdminView
     * @author Lucky (admin@barch.kr)
     * @brief  msg_admin module의 admin view class
     **/

    class msg_adminAdminView extends msg_admin {
        /**
         * @brief 초기화
         **/
        function init() {
            // 커뮤니케이션 애드온이 활성화 되어있는지 검사
            $oAddonAdminModel = &getAdminModel('addon');
            $communication_check = $oAddonAdminModel->isActivatedAddon("member_communication");
            if(!$communication_check) return $this->stop('communication_addon_null');

            $message_type = Context::get('message_type');
            if(!$message_type || !in_array($message_type, array('R','S','T'))) $message_type = "R";
            Context::set('message_type', $message_type);

			// template path 지정
            $this->setTemplatePath($this->module_path.'tpl');
        }

        /**
         * @brief 쪽지 목록 출력
         **/
        function dispMsg_adminAdminList() {

            // 변수 설정
            $message_srl = Context::get('message_srl');
			$oMemberModel = &getModel('member');

            // message_srl이 있으면 내용가져옴
		  	if($message_srl) {
                $oMsgadminAdminModel = &getAdminModel('msg_admin');
			    $output = $oMsgadminAdminModel->getMsg($message_srl);
		    	$message = $output->data;
				if($message->message_srl == $message_srl) Context::set('message', $message);
			}

			// msg model 객체 생성후 목록을 구해옴
            $oMsgadminAdminModel = &getAdminModel('msg_admin');
			$output = $oMsgadminAdminModel->getMsgList();
            if(!$output->toBool()) return $output;

			// 템플릿에 쓰기 위해서 context::set
			Context::set('oMemberModel', $oMemberModel);
			Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('msg_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('list');
        }

        /**
         * @brief 쪽지 보내기 출력
         **/
        function dispMsg_adminAdminInsert() {
            // 로그인정보 구해옴
			$logged_info = Context::get('logged_info');

			// 에디터 모듈의 getEditor를 호출하여 서명용으로 세팅
            $oCommunicationModel = &getModel('communication');
            $this->communication_config = $oCommunicationModel->getConfig();
			$oEditorModel = &getModel('editor');
            $option->primary_key_name = 'message_srl';
            $option->content_key_name = 'content';
            $option->allow_fileupload = false;
            $option->enable_autosave = false;
            $option->enable_default_component = true;// false;
            $option->enable_component = false;
            $option->resizable = false;
            $option->disable_html = true;
            $option->height = 300;
            $option->skin = $this->communication_config->editor_skin;
            $option->colorset = $this->communication_config->editor_colorset;
            $editor = $oEditorModel->getEditor($logged_info->member_srl, $option);
            Context::set('editor', $editor);

            // 그룹 목록 구해옴
			$oMemberModel = &getModel('member');
            $group_list = $oMemberModel->getGroups();
            Context::set('group_list', $group_list);

			$this->setTemplateFile('insert_form');
        }

        /**
         * @brief 쪽지 수정폼 출력
         **/
        function dispMsg_adminAdminModify() {

            // 변수 설정
            $message_srl = Context::get('message_srl');
            $logged_info = Context::get('logged_info');
			if(!$message_srl) return $this->stop('msg_cart_is_null');

            $oMsgadminAdminModel = &getAdminModel('msg_admin');
		    $output = $oMsgadminAdminModel->getMsg($message_srl);
	    	$message = $output->data;
			if(!$message) $this->stop('invalid_msg');
			$message->content = htmlspecialchars($message->content);
			if($message->message_srl == $message_srl) Context::set('message', $message);

			$oMemberModel = &getModel('member');
			Context::set('oMemberModel', $oMemberModel);

			// 에디터 모듈의 getEditor를 호출하여 서명용으로 세팅
            $oCommunicationModel = &getModel('communication');
            $this->communication_config = $oCommunicationModel->getConfig();
			$oEditorModel = &getModel('editor');
            $option->primary_key_name = 'message_srl';
            $option->content_key_name = 'content';
            $option->allow_fileupload = false;
            $option->enable_autosave = false;
            $option->enable_default_component = true;// false;
            $option->enable_component = false;
            $option->resizable = false;
            $option->disable_html = true;
            $option->height = 300;
            $option->skin = $this->communication_config->editor_skin;
            $option->colorset = $this->communication_config->editor_colorset;
            $editor = $oEditorModel->getEditor($logged_info->member_srl, $option);
            Context::set('editor', $editor);

			// 템플릿 파일 지정
			$this->setTemplateFile('modify_form');
        }

        /**
         * @brief 쪽지 삭제폼 출력
         **/
        function dispMsg_adminAdminDelete() {
            // 변수 설정
            $message_srl = Context::get('message_srl');
			if(!$message_srl) return $this->stop('msg_cart_is_null');

            $oMsgadminAdminModel = &getAdminModel('msg_admin');
		    $output = $oMsgadminAdminModel->getMsg($message_srl);
	    	$message = $output->data;
			if(!$message) $this->stop('invalid_msg');
            if($message->message_srl == $message_srl) Context::set('message', $message);

			$oMemberModel = &getModel('member');
			Context::set('oMemberModel', $oMemberModel);

			// 템플릿 파일 지정
			$this->setTemplateFile('delete_form');
        }

        /**
         * @brief 쪽지 일괄삭제
         **/
        function dispMsg_adminAdminAllDelete() {

            // msg model 객체 생성후 목록을 구해옴
            $oMemberAdminModel = &getAdminModel('member');
            $oMemberModel = &getModel('member');
            $output = $oMemberAdminModel->getMemberList();

            // 템플릿에 쓰기 위해서 context::set
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('member_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            // 템플릿 파일 지정
            $this->setTemplateFile('list');
        }
	}
?>
