<?php
/**
 * @class  pointsendAdminController
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief  pointsend 모듈의 Admin Controller class
 **/

class pointsendAdminController extends pointsend
{
	/**
	 * @brief 초기화
	 **/
	function init()
	{
	}

	/**
	 * @brief 설정 저장
	 */
	function procPointsendAdminInsertConfig()
	{
		$oPointsendModel = getModel('pointsend');

		$config = $oPointsendModel->getConfig(true);
		$config->use_fee = Context::get('use_fee');
		$config->fee_apply_point = Context::get('fee_apply_point');
		$config->fee_but_group = Context::get('fee_but_group');
		$config->deny_group = Context::get('deny_group');
		$config->sameip_deny = Context::get('sameip_deny');
		// 수수료
		$config->fee_percent = Context::get('fee_percent');

		// 기본값 지정
		if(!$config->use_fee) $config->use_fee = 'N';
		if(!$config->sameip_deny) $config->sameip_deny = 'N';

		$oModuleController = getController('module');
		$oModuleController->insertModuleConfig('pointsend', $config);

		$this->setMessage('success_saved');

		if(!in_array(Context::getRequestMethod(), array('XMLRPC', 'JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminIndex');
			$this->setRedirectUrl($returnUrl);
		}
	}

	/**
	 * 권한 설정 저장
	 */
	public function procPointsendAdminGrantSetup()
	{
		$oPointsendModel = getModel('pointsend');

		$config = $oPointsendModel->getConfig(true);
		$config->grants = Context::get('grants');

		$oModuleController = getController('module');
		$oModuleController->insertModuleConfig('pointsend', $config);

		$returnUrl = Context::get('success_return_url');
		if(!$returnUrl)
		{
			$returnUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminGrantSetup');
		}

		$this->setMessage('success_updated');
		$this->setRedirectUrl($returnUrl);
	}

	/**
	 * 디자인 설정 저장
	 */
	public function procPointsendAdminDesignSetup()
	{
		$oPointsendModel = getModel('pointsend');

		$config = $oPointsendModel->getConfig(true);
		$config->skin = Context::get('skin');
		$config->mskin = Context::get('mskin');

		$oModuleController = getController('module');
		$oModuleController->insertModuleConfig('pointsend', $config);

		$returnUrl = Context::get('success_return_url');
		if(!$returnUrl)
		{
			$returnUrl = getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminDesignSetup');
		}

		$this->setMessage('success_updated');
		$this->setRedirectUrl($returnUrl);
	}

	/**
	 * @brief 포인트 선물 취소 (관리자용)
	 */
	function procPointsendAdminRevert() {
		$log_srl = (int)Context::get('log_srl');
		if(!$log_srl) return new Object(-1, 'msg_invalid_request');

		$oPointsendModel = getModel('pointsend');
		$log_info = $oPointsendModel->getLogInfoByLogSrl($log_srl);

		$sender_srl = (int)$log_info->sender_srl;
		$receiver_srl = (int)$log_info->receiver_srl;

		if(!$sender_srl || !$receiver_srl) return new Object(-1, 'msg_invalid_request');

		$oMemberModel = getModel('member');
		$config = $oPointsendModel->getConfig();

		$point = $log_info->point;
		$sender_point = $point;
		$receiver_point = $point;

		$sender_info = $oMemberModel->getMemberInfoByMemberSrl($sender_srl);
		$receiver_info = $oMemberModel->getMemberInfoByMemberSrl($receiver_srl);

		// 수수료에 따른 포인트 계산
		$fee_per = (int)$config->fee;
		$fee_apply_point = (int)$config->fee_apply_point;
		$fee = 0;
		if($config->use_fee == 'Y' && $fee_per && $point>=$fee_apply_point) $fee = $point * ($config->fee/100);

		$receiver_point -= $fee;

		$oPointController = getController('point');
		$oPointController->setPoint($sender_srl, $sender_point, 'add');
		$oPointController->setPoint($receiver_srl, $receiver_point, 'minus');

		// 쪽지 보내기
		$title = Context::getLang('pointsendc_title');
		$content = sprintf(Context::getLang('pointsendc_content2'), $sender_info->nick_name, $sender_info->user_id, $receiver_point);
		$content2 = sprintf(Context::getLang('pointsendc_content'), $receiver_info->nick_name, $receiver_info->user_id, $sender_point);

		$oCommunicationController = getController('communication');
		$oCommunicationController->sendMessage($sender_srl, $receiver_srl, $title, $content, false);
		$oCommunicationController->sendMessage($receiver_srl, $sender_srl, $title, $content2, false);

		$args = new stdClass;
		$args->log_srl = $log_srl;
		executeQuery('pointsend.deletePointsendLog',$args);

		if(!in_array(Context::getRequestMethod(), array('XMLRPC', 'JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminIndex');
			$this->setRedirectUrl($returnUrl);
		}
	}

	function procPointsendAdminSendToMember()
	{
		$obj = Context::gets('user_id', 'member_srls', 'point', 'message_title', 'message_body');

		$member_srls = array();

		if(!$obj->member_srls && $obj->user_id)
		{
			$oMemberModel = getModel('member');

			$user_ids = explode(',', $obj->user_id);
			array_walk($user_ids, create_function('&$val', '$val = trim($val);'));

			$args = new stdClass;
			$args->user_id = $obj->user_id;
			$output = executeQueryArray('pointsend.getMemberSrlByUserId', $args);
			if(!$output->toBool()) return $output;
			if($output->data)
			{
				$member_srls = array();

				foreach($output->data as $key => $val)
				{
					$member_srls[] = $val->member_srl;
				}
				$member_srls = implode(',', $member_srls);
			}


			$obj->member_srls = $member_srls;
		}

		if(!$obj->member_srls)
		{
			return new Object(-1, 'msg_invalid_request');
		}

		$oController = getController('pointsend');
		$output = $oController->pointsendToMember($obj->member_srls, $obj->point, $obj->message_title, $obj->message_body);
		if(!$output->toBool()) return $output;

		$msg = sprintf(Context::getLang('success_member_pointgift'), $output->get('member_count'));
		$this->setMessage($msg);

		if(!in_array(Context::getRequestMethod(), array('XMLRPC', 'JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminSend');
			$this->setRedirectUrl($returnUrl);
		}
	}

	/**
	 * @brief 일괄 포인트 선물 - 그룹별
	 */
	function procPointsendAdminSendToGroup() {
		$cart = Context::get('cart');
		if(!$cart) return new Object(-1, 'msg_invalid_request');

		$group_srls = str_replace('|@|', ',', Context::get('cart'));
		$send_point = Context::get('point');
		$title = Context::get('message_title');
		$content = Context::get('message_body');

		$oController = getController('pointsend');
		$output = $oController->pointsendToGroup($group_srls, $send_point, $title, $content);

		$total = $output->get('group_count');
		$success = $output->get('success_group');
		$failed = $output->get('failed_group');
		$ignore = $output->get('ignore_group');

		$msg = sprintf(Context::getLang('success_group_pointgift'), $total, $success, $failed, $ignore);
		$this->setMessage($msg);


		if(!in_array(Context::getRequestMethod(), array('XMLRPC', 'JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminSendToGroup');
			$this->setRedirectUrl($returnUrl);
		}
	}

	/**
	 * @brief 포인트 선물 내역 삭제
	 */
	function procPointsendAdminDeleteLog() {
		$log_srl = Context::get('log_srl');
		if(!$log_srl) return new Object(-1, 'msg_invalid_request');

		// 삭제
		$oController = getController('pointsend');
		$output = $oController->deleteLog($log_srl);

		// 에러가 발생하면
		if(!$output->toBool()) return $output;

		// 메시지 지정
		$this->setMessage('success_deleted');

		if(!in_array(Context::getRequestMethod(), array('XMLRPC', 'JSON')))
		{
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispPointsendAdminLogList');
			$this->setRedirectUrl($returnUrl);
		}
	}
}

/* End of file : pointsend.admin.controller.php */
/* Location : ./modules/pointsend/pointsend.admin.controller.php */
