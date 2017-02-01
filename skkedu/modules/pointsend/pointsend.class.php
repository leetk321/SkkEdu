<?php
/**
 * @class pointsend
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief  pointsend 모듈의 high class
 **/

class pointsend extends ModuleObject
{

	/**
	 * @brief 설치시 추가 작업이 필요할시 구현
	 **/
	function moduleInstall()
	{
		$oModuleController = getController('module');

		// 2010.03.04 회원 탈퇴 시 모든 정보를 삭제하는 트리거 추가
		$oModuleController->insertTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after');

		return new Object();
	}

	/**
	 * @brief 설치가 이상이 없는지 체크하는 method
	 **/
	function checkUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');

		if(!$oDB->isColumnExists('pointsend_log', 'comment')) return true;
		if(!$oModuleModel->getTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after')) return true;

		return false;
	}

	/**
	 * @brief 업데이트 실행
	 **/
	function moduleUpdate()
	{
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		if(!$oDB->isColumnExists('pointsend_log', 'comment'))
			$oDB->addColumn('pointsend_log','comment','text','',0);

		if(!$oModuleModel->getTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after')) 
			$oModuleController->insertTrigger('member.deleteMember', 'pointsend', 'controller', 'triggerDeleteMember', 'after');

		return new Object(0,'success_updated');
	}

	/**
	 * @brief 캐시 파일 재생성
	 **/
	function recompileCache()
	{
	}

	/**
	 * @brief 쪽지 내용에 포함된 치환자를 정리
	 */
	function arrangeMessageContent($sender_info, $receiver_info, $point, &$content)
	{
		$content = str_replace('%_SENDER_%', sprintf('<span class="member_%s">%s</span>', $sender_info->member_srl ,$sender_info->nick_name), $content);
		$content = str_replace('%_RECEIVER_%', sprintf('<span class="member_%s">%s</span>', $receiver_info->member_srl ,$receiver_info->nick_name), $content);
		$content = str_replace('%SENDER%', $sender_info->nick_name, $content);
		$content = str_replace('%SENDER_ID%', $sender_info->user_id, $content);
		$content = str_replace('%SENDER_SRL%', $sender_info->member_srl, $content);
		$content = str_replace('%RECEIVER%', $receiver_info->nick_name, $content);
		$content = str_replace('%RECEIVER_ID', $receiver_info->user_id, $content);
		$content = str_replace('%RECEIVER_SRL', $receiver_info->member_srl, $content);
		$content = str_replace('%POINT%', $point, $content);
	}
}

/* End of file : pointsend.admin.view.php */
/* Location : ./modules/pointsend/pointsend.admin.view.php */