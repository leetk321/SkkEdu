<?php
if(!defined("__ZBXE__")) exit();

/**
 * @file member_pointsend.addon.php
 * @author 퍼니엑스이 (admin@funnyxe.com)
 * MemberModel::getMemberMenu 호출시 대상이 회원일 경우 포인트 선물 기능 추가합니다.
 **/

// 비 로그인 사용자면 중지
$logged_info = Context::get('logged_info');
if(!$logged_info) return;

if(!isset($_GLOBAL['__POINTSEND_ADDON__']['__IS_GRANTED__'])) {
	$oPointsendModel = &getModel('pointsend');
	// 포인트 선물 모듈이 설치되지 않았다면
	if(!$oPointsendModel)
	{
		if($logged_info->is_admin == 'Y')
		{
			Context::addHtmlHeader('<script type="text/javascript">alert(\'포인트 선물 모듈이 설치되지 않았습니다.\n\n설치했는데도 불구하고 이 문구가 나오는 경우 설치 경로를 점검해보시기 바랍니다.\n\n(설치 경로 : [XE설치경로]/modules/pointsend/)\');</script>');
		}
		return;
	}
	$GLOBALS['__POINTSEND_ADDON__']['__IS_GRANTED__'] = $oPointsendModel->isGranted();
}

if(!$GLOBALS['__POINTSEND_ADDON__']['__IS_GRANTED__']) return;

if(!in_array($called_position, array('before_module_init', 'before_module_proc'))) return;

/**
 * 기능 수행 : 회원정보 보기에서 포인트 선물 내역 메뉴 추가.
 **/
if($called_position == 'before_module_init' && $this->module != 'member') {
	// 언어 파일 로드
	Context::loadLang($addon_path.'lang');

	// 회원 로그인 정보에 메뉴를 추가
	$oMemberController = &getController('member');
	$oMemberController->addMemberMenu('dispPointsendLog', 'cmd_view_pointsend_log');
/**
 * 기능 수행 : 사용자 이름을 클릭시 요청되는 팝업 메뉴의 메뉴에 포인트 선물 링크 추가
 **/
} elseif($called_position == 'before_module_proc' && $this->act == 'getMemberMenu') {
	$oMemberController = &getController('member');
	$member_srl = Context::get('target_srl');
	$mid = Context::get('cur_mid');

	// 자신이 아니라면 포인트 선물 기능 추가
	if($logged_info->member_srl != $member_srl) {
		// 대상 회원의 정보를 가져옴
		$oMemberModel = &getModel('member'); 
		$target_member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
		if(!$target_member_info->member_srl) return;

		// 언어 파일 로드
		Context::loadLang($addon_path.'lang');

		// 포인트 선물
		$oMemberController->addMemberPopupMenu(getUrl('','module','pointsend','act','dispPointsend','receiver_srl', $member_srl), 'pointsend', './modules/pointsend/tpl/img/icon_pointsend.gif', 'popup');
	}
}