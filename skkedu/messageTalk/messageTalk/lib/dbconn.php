<?php
	define('__XE__',true);
	define('__ZBXE__',true);
	function dbconn() {
	    global $connect, $_dbconn_is_included, $prefix;
	
	    if($_dbconn_is_included) return;
		$_dbconn_is_included = true;
	
		// 경로가 다른 경우 수정하세요.
		include "../../files/config/db.config.php";
		
	    $connect = @mysql_connect($db_info->master_db["db_hostname"],$db_info->master_db["db_userid"], $db_info->master_db["db_password"]);
	    
	    @mysql_select_db($db_info->master_db["db_database"]);
	    @mysql_query('set names utf8');
	    @mysql_query('SET character_set_client = utf8');
	    @mysql_query('SET character_set_connection = utf8');
	    @mysql_query('SET character_set_results = utf8');
	
		$prefix = $db_info->master_db["db_table_prefix"];
	    return $connect;
	}

	if (!$connect) $connect = dbconn();
	if (!$connect) { echo "DB Connect false. Please Check db.config.php file path "; }
	
	$nowdate = " date_add(now(), interval 8 hour) ";
	$ipAddress = $_SERVER["REMOTE_ADDR"];
?>