<?php
require_once(_XE_PATH_.'modules/sejin7940_comment/sejin7940_comment.view.php');

class sejin7940_commentMobile extends sejin7940_commentView {
	function init()
	{
		$oModuleModel = &getModel('module');
		$this->config = $config = $oModuleModel->getModuleConfig('sejin7940_comment');
        Context::set('module_config', $config);		

		$template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->config->mskin);
		if(!is_dir($template_path)||!$config->mskin) {
			$config->mskin = 'default';
			$template_path = sprintf("%sm.skins/%s/",$this->module_path, $config->mskin);
		}
		$this->setTemplatePath($template_path);

		$oLayoutModel = &getModel('layout');
		$layout_info = $oLayoutModel->getLayout($config->mlayout_srl);
		if($layout_info)
		{
			$this->module_info->mlayout_srl = $config->mlayout_srl;
			$this->setLayoutPath($layout_info->path);
		}

	}

}


?>
