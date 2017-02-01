<?php
/**
 * @class  pointsendAdminView
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * @brief  pointsend 모듈의 admin view 클래스
 **/

class pointsendAdminView extends pointsend
{

	/**
	 * @brief 초기화
	 **/
	function init()
	{
		// 템플릿 경로 지정 (pointsend의 경우 tpl에 관리자용 템플릿 모아놓음)
		$template_path = $this->module_path.'tpl';
		$this->setTemplatePath($template_path);

		// 관리자 페이지에서 공통으로 사용하는 js 파일 로드
		Context::addJsFile($this->module_path.'tpl/js/pointsend_admin.js');
	}

	function dispPointsendAdminIndex()
	{
		// 스킨 목록을 구해옴
		$oModuleModel = getModel('module');
		$skin_list = $oModuleModel->getSkins($this->module_path);
		Context::set('skin_list',$skin_list);

		// 설정 구함
		$oPointSendModel = getModel('pointsend');
		$config = $oPointSendModel->getConfig(true);
		Context::set('config', $config);

		// 생성된 그룹 목록 구함
		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups();
		Context::set('group_list', $group_list);

		// 로그인 기록 모듈이 설치되어 있는지 검사
		$oLoginlog = getClass('loginlog');
		Context::set('loginlog_installed', is_object($oLoginlog) ? true : false);

		// 템플릿 파일 지정
		$this->setTemplateFile('config');
	}

	public function dispPointsendAdminAddons()
	{
		$this->setTemplateFile('addons');
	}

	/**
	 * 권한 설정
	 */
	public function dispPointsendAdminGrantSetup()
	{
		// 생성된 그룹 목록 구함
		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups();
		Context::set('group_list', $group_list);

		// 설정 구함
		$oPointSendModel = getModel('pointsend');
		$config = $oPointSendModel->getConfig(true);
		Context::set('config', $config);

		$this->setTemplateFile('setup_grant');
	}

	public function dispPointsendAdminDesignSetup()
	{
		// 스킨 목록을 구해옴
		$oModuleModel = getModel('module');
		$skin_list = $oModuleModel->getSkins($this->module_path);
		Context::set('skin_list',$skin_list);

		// 모바일 스킨 목록을 구해옴
		$oModuleModel = getModel('module');
		$skin_list = $oModuleModel->getSkins($this->module_path, 'm.skins');
		Context::set('mskin_list',$skin_list);

		// 설정 구함
		$oPointSendModel = getModel('pointsend');
		$config = $oPointSendModel->getConfig(true);
		Context::set('config', $config);

		$this->setTemplateFile('setup_design');
	}

	function dispPointsendAdminSend()
	{
		// 템플릿 파일 지정
		$this->setTemplateFile('send');
	}

	function dispPointsendAdminSendToGroup()
	{
		// 생성된 그룹 목록 구함
		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups();
		Context::set('group_list', $group_list);

		// 템플릿 파일 지정
		$this->setTemplateFile('send_group');
	}

	function dispPointsendAdminLogList()
	{
		// 포인트 선물 내역 목록 구함
		$oPointsendModel = getModel('pointsend');

		$args = new stdClass;
		$args->page = Context::get('page');
		$args->list_count = 30;
		$args->search_target = Context::get('search_target');
		$args->search_keyword = Context::get('search_keyword');

		$output = $oPointsendModel->getLogList($args);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('log_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('log_list');
	}

	function dispPointsendAdminBatchLogList() {
		// 포인트 선물 내역 목록 구함
		$oPointsendModel = getModel('pointsend');

		$args = new stdClass;
		$args->page = Context::get('page');
		$args->list_count = 30;
		$args->search_target = Context::get('search_target');
		$args->search_keyword = Context::get('search_keyword');

		$output = $oPointsendModel->getBatchLogList($args);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('log_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		// 템플릿 파일 지정
		$this->setTemplateFile('batch_log_list');
	}

	function dispPointsendAdminRevert()
	{
		// 템플릿 파일 지정
		$this->setTemplateFile('revert');
	}

	function dispPointsendAdminDeleteLog()
	{
		$log_srl = Context::get('log_srl');
		if(!$log_srl) return new Object(-1, 'msg_invalid_request');

		$oModel = getModel('pointsend');
		$log_info = $oModel->getLogInfoByLogSrl($log_srl);
		if(!$log_info) return new Object(-1, 'msg_invalid_request');

		Context::set('log_info', $log_info);

		// 템플릿 파일 지정
		$this->setTemplateFile('delete_log');
	}
}

/* End of file : pointsend.admin.view.php */
/* Location : ./modules/pointsend/pointsend.admin.view.php */
