<?
require_once(_XE_PATH_.'modules/rockgame/rockgame.view.php');

class rockgameMobile extends rockgameView {
	function init()
	{
		if(!$this->module_info->mskin) $this->module_info->mskin = 'default';
		$template_path = sprintf("%sm.skins/%s/", $this->module_path, $this->module_info->mskin);
    	$this->setTemplatePath($template_path);
	}
}
?>