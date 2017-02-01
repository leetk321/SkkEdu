<?php 

/**
* @class  memberextend
* @author skullacy
* @brief  memberextend 최상위 클래스
**/

class memberextend extends ModuleObject {

	/**
	 * @brief 트리거 리스트 배열지정후 일괄 수정,추가,삭제 
	 */
	var $triggers_list= array(
			array('member.insertMember', 'memberextend', 'controller', 'triggerInsertMember', 'after'),
			array('member.insertMember', 'memberextend', 'controller', 'triggerInsertMemberBefore', 'before'),
			array('member.updateMember', 'memberextend', 'controller', 'triggerUpdateMember', 'after'),
			array('member.updateMember', 'memberextend', 'controller', 'triggerUpdateMemberBefore', 'before'),
			array('member.deleteMember', 'memberextend', 'controller', 'triggerDeleteMember', 'after'),
			array('display', 'memberextend', 'controller', 'triggerDisplay', 'before'),
			array('member.dispmemberSignUpForm', 'memberextend', 'controller', 'triggerDispMemberSignupFormBefore', 'before'),
			array('member.procMemberInsert', 'memberextend', 'controller', 'triggerProcMemberInsert', 'after'),
			array('moduleHandler.proc', 'memberextend', 'controller', 'triggerModuleHandlerProc', 'after'),
	);
	
	/**
	 * @brief 설치시 해야할 일
	 **/
	function moduleInstall() 
	{
		$oModuleController = &getController('module'); /* @var $oModuleController moduleController */ 
		
		// 트리거 등록.
		foreach($this->triggers_list as $trigger)
		{
			$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}
		
		//기본 멤버타입 등록, 기존 회원리스트와 연결.
		$this->setDefaultSetting();
		

		return new Object();
	}

	/**
	 * @brief 업데이트 확인
	 **/
	function checkUpdate() 
	{
		$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
		$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
		
		// 트리거 체크.
		foreach($this->triggers_list as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4])) return true;
		}
		
		// 회원타입 체크
		$memberTypes = $oMemberextendModel->getMemberTypes();
		if(!count($memberTypes)) return true;
		
		// DB 칼럼 체크
		$oDB = &DB::getInstance();
		
		// check is_Active column in memberextend_type table
		if(!$oDB->isColumnExists('memberextend_type', 'is_active')) return true;
		
		return false;
	}

	/**
	 * @brief 업데이트시 할일
	 **/
	function moduleUpdate() 
	{
		$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
		$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
		
		$args->module = 'memberextend';
		$output = executeQuery('memberextend.deleteMemberextendTrigger', $args);
		
		// 트리거 일괄등록.
		foreach($this->triggers_list as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}
		
		// 멤버타입 디폴트 등록.
		$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
		$memberTypes = $oMemberextendModel->getMemberTypes();
		if(!count($memberTypes)) $this->setDefaultSetting();
		
		$oDB = &DB::getInstance();
		// add column(is_active) to memberextend_type table
		if(!$oDB->isColumnExists('memberextend_type', 'is_active'))
		{
			$oDB->addColumn('memberextend_type', 'is_active', 'char', 1, 'N', true);
		}
		
		return new Object(0, 'success_updated');
	}
	
	function moduleUninstall()
	{
		$oModuleModel = &getModel('module'); /* @var $oModuleModel moduleModel */
		$oModuleController = &getController('module'); /* @var $oModuleController moduleController */
		
		foreach($this->triggers_list as $trigger)
		{
			if($oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
				$oModuleController->deleteTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
		}
		
		return new Object();
	}

	/**
	 * @brief 컴파일
	 **/
	function recompileCache() {
	}
	
	/**
	 * @brief 디폴트 설정 세팅. (기본 회원타입 생성, 기존 회원 기본회원타입으로 설정)
	 */
	function setDefaultSetting()
	{
		// Set an administrator, regular member(group1), and associate member(group2)
		
		$oMemberextendAdminController = &getAdminController('memberextend'); /* @var $oMemberextendAdminController memberextendAdminController */
		
		$type_args->type_srl = 1;
		$type_args->list_order = 1;
		$type_args->description = 'Default Membertype 기본 회원타입입니다. 삭제불가능.';
		$type_args->title = 'Default';
		$type_args->is_default = 'Y';
		$type_args->is_active = 'Y';
		$output = $oMemberextendAdminController->insertMemberType($type_args);
		
		
		$member_list = executeQueryArray('member.getMembers', $args=null)->data;
		foreach($member_list as $key=>$val)
		{
			$member_args->member_srl = $val->member_srl;
			$member_args->type_srl = 1;
			$member_args->type_extra_vars = '';
			
			executeQuery('memberextend.insertMember', $member_args);
			unset($member_args);
		}
		
	}

}

?>
