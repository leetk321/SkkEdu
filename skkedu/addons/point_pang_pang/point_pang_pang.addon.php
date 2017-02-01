<?php
	if(!defined('__XE__')) exit();

	if($called_position == 'after_module_proc'){
		//메세지가 있으면 출력후 패스
		if($_SESSION['addon_point_pang_pang_msg']&&$this->act=='dispBoardContent'){
			Context::addHtmlFooter( '<script type="text/javascript"> alert("'.$_SESSION['addon_point_pang_pang_msg'].'"); </script>' );
			unset($_SESSION['addon_point_pang_pang_msg']);
			return;
		}

		//에러시 패스
		if($this->error) return;

		if(($addon_info->is_document == 'Y' || $addon_info->is_comment == 'Y') && $this->act=='procFileUpload'){
			//업로드시 새문서 체크
			$_SESSION['addon_point_pang_pang_uploadTargetSrl'] = Context::get('uploadTargetSrl')?false:true;
		}

		if(($addon_info->is_document == 'Y' && $this->act=='procBoardInsertDocument') || ($addon_info->is_comment == 'Y' && $this->act=='procBoardInsertComment')){
			$upload_srl = $_SESSION['addon_point_pang_pang_uploadTargetSrl'];
			unset($_SESSION['addon_point_pang_pang_uploadTargetSrl']);

			//해당 액션이고 신규 문서이면
			if($this->act=='procBoardInsertDocument' && (!Context::get('document_srl') || (Context::get('document_srl') && $upload_srl)))
				$addon_act = 'document';
			elseif($this->act=='procBoardInsertComment' && (!Context::get('comment_srl') || (Context::get('comment_srl') && $upload_srl)))
				$addon_act = 'comment';
			else return;

			//최고 관리자이거나 로그인  유저가 아니면 패스
			$oLogIfo = Context::get('logged_info');
			if(!$oLogIfo->member_srl || ($oLogIfo->is_admin == 'Y'&&$addon_info->is_admin != 'Y') || $oLogIfo->denied =='Y') return;

			$def_point = '10,50,100';
			$def_pbility = '10';
			$def_title = '$addon_info->pang_name';
			$def_pang = '$addon_info->message_pang';

			$msg_title = cut_str(strip_tags((($addon_info->message_title)?$addon_info->message_title:$def_title)), 40, '...');
			$msg_pang = ($addon_info->message_pang)?$addon_info->message_pang:$def_pang;

			$set_point = $addon_info->{'set_'.$addon_act.'_point'};
			$set_point = explode(',',(($set_point)?$set_point:$def_point));
			$set_pbility = (int) $addon_info->{'set_'.$addon_act.'_probability'};
			if(!$set_pbility) $set_pbility = $def_pbility;

			mt_srand((double)microtime() * 1000) ;

			if(!count($set_point)) return;
			if($set_pbility < mt_rand(1,100)) return;

			$bonus = $set_point[(mt_rand(1,count($set_point))-1)];
			$ccPoint = &getController('point');
			$ccPoint->setPoint(abs($oLogIfo->member_srl), $bonus, 'add');

			$success = true;

			if($success){
				$_fullUrl = getFullUrl('','mid',$this->mid,'document_srl',$this->variables['document_srl'],'comment_srl',$this->variables['comment_srl']);

				for($i=0;$i<2;$i++){
					if($i===0) $_msg = &$msg_title; else $_msg = &$msg_pang;
					$_msg = str_replace('%URL%',$_fullUrl,$_msg);
					$_msg = str_replace('%NAME%',$oLogIfo->nick_name,$_msg);
					$_msg = str_replace('%POINT%',$bonus,$_msg);
					$_msg = str_replace('%DOCUMENT%',$this->variables['document_srl'],$_msg);
					$_msg = str_replace('%COMMENT%',$this->variables['comment_srl'],$_msg);
				}

				if($this->variables['document_srl']){
					$args->document_srl = $this->variables['document_srl'];
					$tmpout = executeQuery('addons.point_pang_pang.getDocumentExtra', $args);

					if($tmpout->data->extra_vars){
						$exvars=unserialize($tmpout->data->extra_vars);

						if($addon_act == 'comment'){
							$exvars->ppang->c[$this->variables['comment_srl']]->p = $bonus;
							$exvars->ppang->c[$this->variables['comment_srl']]->m = $oLogIfo->member_srl;
							//text 필드의 한정된 용량 관계상 날짜는 문서번호로 구해올 수 있기에 빼기로함
							//$exvars->ppang->c[$this->variables['comment_srl']]->d = date('YmdHis');
						}else {
							$exvars->ppang->d->p = $bonus;
							$exvars->ppang->d->m = $oLogIfo->member_srl;
							//$exvars->ppang->d->d = date('YmdHis');
						}

						$args->extra_vars = serialize($exvars);
						// 최대 크기를 설정해서 오류 방지 mssql = 8000
						if(strlen($args->extra_vars) > 8000) return;
						$tmpout = executeQuery('addons.point_pang_pang.updateDocumentExtra', $args);
					}
				}

				if($addon_info->alert_reply=='Y'){

					if($addon_act == 'comment')
						$comObj->parent_srl = $this->variables['comment_srl'];
					else unset($comObj->parent_srl);

					$comObj->member_srl = $addon_info->who_you;
					$comObj->email_address = $comObj->homepage = $comObj->user_id = '';
					$comObj->user_name = 'point_pang';
					$comObj->nick_name = $addon_info->pang_name;

					$comObj->module_srl = $this->module_srl;
					$comObj->document_srl = $this->variables['document_srl'];
					$comObj->content = $msg_pang;

					$ccComment = &getController('comment');

					$bk_avoidlog = $_SESSION['avoid_log'];
					$_SESSION['avoid_log'] = true;
					$tmpout = $ccComment->insertComment($comObj, true);
					$_SESSION['avoid_log'] = $bk_avoidlog;
					unset($_SESSION['own_comment'][$tmpout->get('comment_srl')]);
				}

				if($oLogIfo->allow_message!='N'&&$addon_info->send_message =='Y') {
					$ccCommunication = &getController('communication');
					$tmpout = $ccCommunication->sendMessage(0, $oLogIfo->member_srl, $msg_title, $msg_pang, false);
					//정상적으로 쪽지가 보내지면 false 를 보내 메세지 출력은 패스
					$success = !$tmpout->toBool();
				}

				if($success){
					$msg_pang = eregi_replace("\r|\n", "", strip_tags($msg_pang,'<br>'));
					$msg_pang = str_replace('&amp;', '&', $msg_pang);
					$_SESSION['addon_point_pang_pang_msg'] = str_replace('"','`',preg_replace('/\<br(\s*)?\/?\>/i', '\n', $msg_pang));
				}
			}
		}
	}elseif($called_position == 'before_module_proc'){
		//글 삭제시
		if(($this->act=='procBoardDeleteDocument' && Context::get('document_srl')) || ($this->act=='procBoardDeleteComment' && Context::get('comment_srl'))){

			$args->document_srl =  Context::get('document_srl');
			$tmpout = executeQuery('addons.point_pang_pang.getDocumentExtra', $args);
			$exvars=unserialize($tmpout->data->extra_vars);

			if($exvars){
				if($this->act=='procBoardDeleteComment'){
					$com_srl = Context::get('comment_srl');
					$mbr_srl = $exvars->ppang->c[$com_srl]->m;

					if($mbr_srl){
						$bonus = $exvars->ppang->c[$com_srl]->p;

						if($addon_info->is_keep_point!='Y'){
							$ccPoint = &getController('point');
							$ccPoint->setPoint(abs($mbr_srl), $bonus, 'minus');
						}

						unset($exvars->ppang->c[$com_srl]);
						$args->extra_vars = serialize($exvars);
						$tmpout = executeQuery('addons.point_pang_pang.updateDocumentExtra', $args);
					}
				}elseif($addon_info->is_keep_point!='Y'){
					$ccPoint = &getController('point');
					$mbr_srl = abs($exvars->ppang->d->m);

					foreach($exvars->ppang->c as $val){
						//문서를 지우면 부정 방지를 위해 글쓴이의 댓글중 보너스를 받았다면 되돌림
						if($mbr_srl==abs($val->m))
							$ccPoint->setPoint($mbr_srl, $val->p, 'minus');
					}

					$bonus = $exvars->ppang->d->p;
					$ccPoint->setPoint($mbr_srl, $bonus, 'minus');
				}
			}
		//업데이트시
		}elseif($this->act=='procBoardInsertDocument' && Context::get('document_srl')){
			$args->document_srl =  Context::get('document_srl');
			$tmpout = executeQuery('addons.point_pang_pang.getDocumentExtra', $args);

			$exvars=unserialize($tmpout->data->extra_vars);
			if($exvars) Context::set('extra_vars',$exvars, true);
		}
	}
?>
