<?php

require_once(_XE_PATH_.'modules/ximember/ximember.view.php');

class ximemberMobile extends ximemberView {
	/**
	 * Config Load
	 * @var array
	 */
	var $xiConfig = NULL;
	var $member_config = NULL;
	var $memberInfo = NULL;

    function init() 
	{	
		//get Config
		$oModuleModel = &getModel('module');
		$this->xiConfig = $oModuleModel->getModuleConfig('ximember');
		Context::set('xiConfig', $this->xiConfig);
		
		// Get the member configuration
		$oMemberModel = &getModel('member');
		$this->member_config = $oMemberModel->getMemberConfig();
		Context::set('member_config', $this->member_config);

		$mskin = $this->xiConfig->mskin;
		// Set the template path
		if(!$mskin)
		{
			$mskin = 'default';
			$template_path = sprintf('%sm.skins/%s', $this->module_path, $mskin);
		}
		else
		{
			$template_path = sprintf('%sm.skins/%s', $this->module_path, $mskin);
		}
		$template_path = sprintf('%sm.skins/%s', $this->module_path, "default");
		// if member_srl exists, set memberInfo
		$member_srl = Context::get('member_srl');
		if($member_srl) 
		{
			$oMemberModel = &getModel('member');
			$this->memberInfo = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
			if(!$this->memberInfo)
			{
				Context::set('member_srl','');
			}
			else
			{
				Context::set('member_info',$this->memberInfo);
			}
		}

        $this->setTemplatePath($template_path);

		$oLayoutModel = &getModel('layout');
		$layout_info = $oLayoutModel->getLayout($this->xiConfig->mlayout_srl);
		if($layout_info)
		{
			$this->module_info->mlayout_srl = $this->xiConfig->mlayout_srl;
			$this->setLayoutPath($layout_info->path);
		}
		
			Context::addJsFile("./common/js/jquery.min.js");
			Context::addJsFile("./common/js/x.min.js");
			Context::addJsFile("./common/js/xe.min.js");
			Context::addCssFile("./common/css/xe.min.css");
	}
}


?>
