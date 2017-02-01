<?php
/**
 * @class  pointsendModel
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief pointsend 모듈의 Model class
 **/

class pointsendModel extends pointsend
{

	/**
	 * @brief 초기화
	 **/
	function init()
	{
	}

	/**
	 * @brief 포인트 선물 모듈의 권한 설정을 구함
	 */
	function getGrantInfo()
	{
		static $module_config = null;
		if(is_null($module_config))
		{
			$oModuleModel = getModel('module');
			$module_config = $oModuleModel->getModuleConfig('pointsend');
			$module_config->grants = is_array($module_config->grants) ? $module_config->grants : explode('|@|',$module_config->grants);
		}

		return $module_config->grants;
	}

	/**
	 * @brief 포인트 선물 모듈의 설정을 구함
	 **/
	public function getConfig($is_admin = false)
	{
		static $config = null;
		if(is_null($config))
		{
			// 모듈 설정을 구함
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('pointsend');
			$config->skin = $config->skin ? $config->skin : 'default';

			if(!isset($config->use_fee))
			{
				$config->use_fee = 'N';
			}

			if(!in_array($config->use_fee, ['Y', 'N']))
			{
				$config->use_fee = 'N';
			}

			if(!is_array($config->fee_but_group))
			{
				$config->fee_but_group = $config->fee_but_group ? explode('|@|',$config->fee_but_group) : array();
			}
			if(!is_array($config->deny_group))
			{
				$config->deny_group = $config->deny_group ? explode('|@|', $config->deny_group) : array();
			}
			if(!is_array($config->grants))
			{
				$config->grants = $config->grants ? explode('|@|',$config->grants) : array();
			}

			$logged_info = Context::get('logged_info');

			$tmp_group = array();

			if($logged_info)
			{
				$this->_supportLegacyConfig($config);

				// 소속된 그룹을 검사하여 그룹에 맞는 수수료를 구함
				if(!$is_admin)
				{
					if(count($tmp_group) && is_array($logged_info->group_list) && count($logged_info->group_list))
					{
						foreach($logged_info->group_list as $key => $val)
						{
							if(isset($config->fee_percent[$key]) && $config->fee_percent[$key] > 0)
							{
								$config->fee = $config->fee_percent[$key];
								break;
							}
						}
					}

					// 최고 관리자이면 동일 IP 선물 제한 비활성화
					if($logged_info->is_admin == 'Y') $config->sameip_deny = 'N';

					// 최고 관리자이거나 수수료 제외 그룹에 해당하면 수수료 기능 비활성화
						if($logged_info->is_admin == 'Y') $config->use_fee = 'N';
						if(is_array($config->fee_but_group) && count($config->fee_but_group))
						{
							foreach($logged_info->group_list as $key => $val)
							{
								if(in_array($key, $config->fee_but_group))
								{
									$config->use_fee = 'N';
									break;
								}
							}
						}
					}
				}
			}
		return $config;
	}

	/**
	 * 기존 버전 호환용 코드
	 */
	private function _supportLegacyConfig(&$config)
	{
		foreach($config as $key => $val)
		{
			if(!preg_match('/^fee_([0-9]+)$/i', $key, $matches)) continue;

			$config->fee_percent[$matches[1]] = $val;

			unset($config->{$matches[0]});
		}
	}

	/**
	 * @brief 포인트 선물 권한이 있는지 확인
	 */
	function isGranted()
	{
		// 로그인 하지 않은 경우 권한이 없음
		$logged_info = Context::get('logged_info');
		if(!$logged_info) return false;

		// 설정된 권한이 없을 경우 무조건 권한이 있음
		$granted = true;

		// 최고 관리자가 아닌 경우 권한이 있는지 확인
		if($logged_info->is_admin != 'Y')
		{
			$granted = true;
			// 설정을 구함
			$grant_info = $this->getGrantInfo();

			// 설정된 권한이 있다면
			if(count($grant_info))
			{
				$granted = false;
				// 그룹이 하나라도 설정된 권한에 포함되어 있다면 권한이 있음
				foreach($logged_info->group_list as $key => $val)
				{
					if(in_array($key, $grant_info))
					{
						$granted = true;
						break;
					}
				}
			}
		}

		return $granted;
	}

	/**
	 * @brief log_srl로 포인트 선물 내역 정보 구함
	 */
	function getLogInfoByLogSrl($log_srl)
	{
		$args = new stdClass;
		$args->log_srl = $log_srl;
		$output = executeQuery('pointsend.getPointsendLogInfo',$args);
		return $output->data;
	}

	/**
	 * @brief 포인트 선물 내역 목록을 구함
	 **/
	function getLogList($obj = null) {
		$args = new stdClass;
		$args->sender_srl = $obj->sender_srl;
		$args->receiver_srl = $obj->receiver_srl;
		$args->sort_index = $obj->sort_index?$obj->sort_index:'log.regdate';
		$args->order_type = $obj->order_type?$obj->order_type:'desc';
		$args->list_count = $obj->list_count?$obj->list_count:20;
		$args->page_count = $obj->page_count?$obj->page_count:10;
		$args->page = $obj->page?$obj->page:1;

		$query_id = 'pointsend.getPointsendLogList';

		// 검색어를 입력한 경우 처리
		if($obj->search_keyword)
		{
			$search_target = $obj->search_target;
			$search_keyword = $obj->search_keyword;

			switch($search_target)
			{
				case 's_nick_name':
				case 's_user_id':
					$query_id = 'pointsend.getPointsendLogListWithinMemberS';
					$args->{'s_'.$search_target} = $search_keyword;
					break;
				case 'r_nick_name':
				case 'r_user_id':
					$query_id = 'pointsend.getPointsendLogListWithinMemberR';
					$args->{'s_'.$search_target} = $search_keyword;
					break;
				case 'point_more':
				case 'point_less':
				case 'regdate_more':
				case 'regdate_less':
				case 'ipaddress':
					$args->{'s_'.$search_target} = $search_keyword;
					break;
			}
		}

		$output = executeQuery($query_id,$args);
		return $output;
	}

	/**
	 * @brief 일괄 포인트 선물 내역 목록을 구함
	 **/
	function getBatchLogList($obj = null) {
		$args = new stdClass;
		$args->sender_srl = $obj->sender_srl;
		$args->sort_index = $obj->sort_index?$obj->sort_index:'log.regdate';
		$args->order_type = $obj->order_type?$obj->order_type:'desc';
		$args->list_count = $obj->list_count?$obj->list_count:20;
		$args->page_count = $obj->page_count?$obj->page_count:10;
		$args->page = $obj->page?$obj->page:1;


		$query_id = 'pointsend.getBatchLogList';

		if($obj->search_keyword)
		{
			$search_target = $obj->search_target;
			$search_keyword = $obj->search_keyword;

			switch($search_target)
			{
				case 'nick_name':
				case 'user_id':
					$query_id = 'pointsend.getBatchLogListWithinMember';
					$args->{'s_'.$search_target} = $search_keyword;
				case 'point_more':
				case 'point_less':
				case 'regdate_more':
				case 'regdate_less':
				case 'ipaddress':
					$args->{'s_'.$search_target} = $search_keyword;
					break;
			}
		}

		return executeQuery($query_id,$args);
	}

	/**
	 * @brief 금일 받은 포인트의 합계를 구합니다
	 */
	function getTodayReceivedPoint($member_srl)
	{
		$args = new stdClass;
		$args->receiver_srl = $member_srl;
		$args->start_date = date('Ymd000000');
		$args->end_date = date('Ymd235959');
		$output = executeQuery('pointsend.getTotalPoint',$args);
		return $output->data->total_point;
	}

	/**
	 * @brief 금일 보낸 포인트의 합계를 구합니다
	 */
	function getTodaySentPoint($member_srl)
	{
		$args = new stdClass;
		$args->sender_srl = $member_srl;
		$args->start_date = date('Ymd000000');
		$args->end_date = date('Ymd235959');
		$output = executeQuery('pointsend.getTotalPoint',$args);
		return $output->data->total_point;
	}

	/**
	 * @brief 금일의 포인트 선물 내역 목록을 구함
	 * @remarks getTodayLog() 함수와 동작이 같으나 페이징 기능 유무 차이만 있음
	 */
	function getTodayLogList($obj) {
		if(!$obj || $obj->member_srl) return;
		if(!in_array($obj->type,array('S','R'))) $obj->type = 'S';

		$member_srl = $obj->member_srl;
		$type = $obj->type;

		switch($type) {
			case 'S':
				$args->sender_srl = $member_srl;
				break;
			case 'R':
				$args->receiver_srl = $member_srl;
				break;
		}

		$args = new stdClass;
		$args->start_date = date('Ymd000000');
		$args->end_date = date('Ymd235959');

		$output = executeQuery('pointsend.getTodayPointsendLogList',$args);

		return $output->data;
	}

	/**
	 * @brief 특정 IP의 포인트 선물 내역을 구함
	 */
	function getLogsByIpaddress($ipaddress) {
		if(!$ipaddress) return;

		$args = new stdClass;
		$args->ipaddress = $ipaddress;
		$args->order_type = 'desc';
		return executeQuery('pointsend.getPointsendLogs', $args);
	}
}
/* End of file : pointsend.model.php */
/* Location : ./modules/pointsend/poinsend.model.php */
