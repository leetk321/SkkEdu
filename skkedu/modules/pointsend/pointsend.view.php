<?php
/**
 * @class  pointsendView
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief  pointsend 모듈의 view 클래스
 **/

class pointsendView extends pointsend
{
	/**
	 * @brief 초기화
	 **/
	function init()
	{
		// 로그인 상태가 아니면 에러
		$logged_info = Context::get('logged_info');
		if(!$logged_info) $this->stop('msg_need_login');

		// 설정을 구함
		$oPointsendModel = getModel('pointsend');
		$config = $oPointsendModel->getConfig();

		// 권한을 구함
		$grant = Context::get('grant');

		$grant->pointsend = true;

		// 최고 관리자가 아닌 경우 권한이 있는지 확인
		if($logged_info->is_admin != 'Y')
		{
			// 설정된 권한이 있다면
			if(count($config->grants))
			{
				$grant->pointsend = false;
				// 그룹이 하나라도 설정된 권한에 포함되어 있다면 권한이 있음
				if(count($logged_info->group_list))
				{
					foreach($logged_info->group_list as $key => $val)
					{
						if(in_array($key,$config->grants))
						{
							$grant->pointsend = true;
							break;
						}
					}
				}
			}
		}

		$this->grant = $grant;

		// 권한이 없으면 에러 출력
		if(!$grant->pointsend)
		{
			$this->stop('msg_not_permitted_pointsend');
		}

		Context::set('grant', $grant);
		Context::set('pointsend_config', $config);

		/**
		 * 스킨 경로를 미리 template_path 라는 변수로 설정함
		 * 스킨이 존재하지 않는다면 xe_default로 변경
		 **/
		$template_path = sprintf('%sskins/%s/',$this->module_path, $config->skin);
		if(!is_dir($template_path))
		{
			$config->skin = 'default';
			$template_path = sprintf('%sskins/%s/',$this->module_path, $config->skin);
		}
		$this->setTemplatePath($template_path);
	}

	/**
	 * @brief 포인트 선물 화면 출력
	 **/
	function dispPointsend()
	{
		if(Mobile::isFromMobilePhone())
		{
			return getMobile('pointsend')->dispPointsend();
		}

		$this->setLayoutFile('popup_layout');

		// 로그인 정보 구함
		$logged_info = Context::get('logged_info');

		// 받는이가 없으면 에러
		$member_srl = Context::get('receiver_srl');
		if(!$member_srl)
		{
			return new Object(-1, 'msg_not_exists_receiver');
		}

		// 받는이가 로그인 한 회원과 같으면 에러
		if($logged_info->member_srl == $member_srl)
		{
			return new Object(-1, 'msg_invalid_request');
		}

		// 존재 하지 않는 회원이면 에러
		$oMemberModel = getModel('member');
		$member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
		if(!$member_info)
		{
			return new Object(-1, 'msg_not_exists_member');
		}

		$config = Context::get('pointsend_config');
		if($logged_info->is_admin != 'Y')
		{
			$oModel = getModel('pointsend');
			// 일일 포인트 선물 제한을 설정한 경우 체크
			$daily_limit = (int)$config->daily_limit;
			if($daily_limit >0)
			{
				$total_point = $oModel->getTodaySentPoint($logged_info->member_srl);
				if($daily_limit < $total_point)
				{
					return new Object(-1, sprintf(Context::getLang('msg_pointgift_daily_limit_over'),$daily_limit));
				}
			}

			// 선물 제한 그룹을 설정한 경우 체크
			$deny_group = $config->deny_group;
			if(count($deny_group) > 0)
			{
				$groups = $oMemberModel->getMemberGroups($receiver_srl);
				if(count($groups))
				{
					foreach($groups as $group_srl => $group_title)
					{
						if(in_array($group_srl, $deny_group)) return new Object(-1, sprintf(Context::getLang('msg_pointgift_denied_group'),$group_title));
					}
				}
			}
		}

		// 보내는이와 받는이의 회원 정보를 템플릿에서 쓸 수 있도록 set
		Context::set('member_info', $member_info);

		// 현재 포인트 구함
		$oPointModel = getModel('point');
		$current_point = $oPointModel->getPoint($logged_info->member_srl);
		Context::set('current_point', $current_point);

		// Javascript Filter 적용
		Context::addJsFilter($this->module_path.'tpl/filter/','pointsend.xml');

		$this->setTemplateFile('PointSend');
	}

	/**
	 * @brief 회원 정보 보기 > 포인트 선물 내역 보기
	 */
	function dispPointsendLog()
	{
		// 로그인이 되어 있지 않으면 오류 표시
		if(!Context::get('is_logged'))
		{
			return $this->stop('msg_need_login');
		}

		// 로그인 정보 구함
		$logged_info = Context::get('logged_info');

		$oPointsendModel = getModel('pointsend');

		$log_type = Context::get('log_type');
		if(!in_array($log_type, array('S','R')))
		{
			$log_type = 'R';
			Context::set('log_type', 'R');
		}

		$args = new stdClass;

		switch($log_type)
		{
			case 'S':
				$args->sender_srl = $logged_info->member_srl;
				break;
			case 'R':
				$args->receiver_srl = $logged_info->member_srl;
				break;
		}

		// 포인트 선물 내역 구함
		$output = $oPointsendModel->getLogList($args);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('log_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		$this->setTemplateFile('pointsend_log');
	}
}
/* End of file : pointsend.view.php */
/* Location : ./modules/pointsend/pointsend.view.php */
