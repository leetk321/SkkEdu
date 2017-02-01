<?php 
require_once(_XE_PATH_.'modules/memberextend/memberextend.view.php');
/**
 * @class memberextendMobile
 * @author skullacy
 * @brief memberextend Mobile Class
 */
class memberextendMobile extends memberextendView
{
	
	var $memberextend_config = null;
	var $type_list = null;
	var $active_type_list = null;
	var $skin = null;
	
	function init()
	{
		$oMemberextendModel = &getModel('memberextend'); /* @var $oMemberextendModel memberextendModel */
		$this->memberextend_config = $oMemberextendModel->getMemberextendConfig();
		Context::set('memberextend_config', $this->memberextend_config);
		//스킨설정, 테마설정기능 미구현
		$this->skin = $this->memberextend_config->skin;
		if(!$this->skin)
		{
			$this->skin = 'default';
		}
		
		$template_path = sprintf('%sm.skins/%s', $this->module_path, $this->skin);
		$this->setTemplatePath($template_path);
		
		//회원타입 리스트 코드 
		$this->type_list = $oMemberextendModel->getMemberTypes();
		$this->active_type_list = $oMemberextendModel->getMemberTypes('active');
		
		//회원타입 카운트 템플릿으로 전송. (헤더파일에서 타입선택메뉴 표시유무 결정)
		Context::set('type_count', count($this->active_type_list));
		
		$oMemberModel = &getModel('member'); /* @var $oMemberModel memberModel */
		$member_config = $oMemberModel->getMemberConfig();
		Context::set('member_config', $member_config);
	}
	
}




?>