<?php
class pointsendMobile extends pointsendView
{
	public function init()
	{
		parent::init();
	}

	/**
	 * @brief 포인트 선물 화면 출력
	 **/
	public function dispPointsend()
	{
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
}
