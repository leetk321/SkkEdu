<?php
require_once(_XE_PATH_.'modules/document/document.item.php');

class documentAdd extends documentItem
{
	var $comment_list = NULL;
	
	function getComments()
	{
		return $this->comment_list;
	}
	
	function setCommentList($comment_list)
	{
		$this->comment_list = $comment_list;
	}
}