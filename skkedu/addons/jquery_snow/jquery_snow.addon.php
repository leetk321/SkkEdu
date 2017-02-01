<?php
    if(!defined("__ZBXE__") && !defined('__XE__')) exit();

    /**
     * @file snow.addon.php 
     * @author PureAni (pureani.tistory.com)
     **/

    // called_position가 before_module_init 이고 module이 admin이 아닐 경우
    //if($called_position == 'before_module_init' && !$GLOBALS['__referer_addon_called__']) {
	if($called_position == 'before_module_init' && !($this->module == "admin" || $this->module == "addon" || $this->module == "widget")) {
		Context::addJsFile('./addons/jquery_snow/js/jquery.snow.min.js', false ,'', null, 'body');
		Context::addJsFile('./addons/jquery_snow/js/snow.js', false ,'', null, 'body');
    }
?>
