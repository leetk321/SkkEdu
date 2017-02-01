<?php
    if(!defined("__XE__")) exit;

    /**
    * @file best_comment.addon.php
    * @author CONORY (http://www.conory.com)
    * @brief 베스트 댓글 애드온
    **/
	
	if(Context::getResponseMethod() != 'HTML' || !Context::get('document_srl') || !Context::get('oDocument')) return;
	
	if($called_position == 'after_module_proc')
	{
		$document_srl = Context::get('document_srl');
		$oDocument = Context::get('oDocument');
		$comment_list = $oDocument->getComments();
		if(!$comment_list) return;
		
		$args = new stdClass;
		$args->document_srl = $document_srl;
		$args->list_count = $addon_info->comment_count;
		$args->more_voted_count = $addon_info->more_voted_count;
		$output = executeQueryArray('addons.best_comment.getBestCommentList',$args);
		
		if(!$output->data)
		{
			return;
		}
		else
		{
			$best_list = array();
			require_once(_XE_PATH_ . 'modules/comment/comment.item.php');
			foreach($output->data as $key=> $val)
			{
				if($addon_info->show_best != 'N')
				{
					$val->nick_name = '{best_comment}' . '&nbsp;&nbsp;' . $val->nick_name;
                    //$val->regdate = &val->regdate . ' ' . '{best_comment}';
				}
				$val->best_comment = 'Y';
				$oCommentItem = new commentItem();
				$oCommentItem->setAttribute($val);
				$best_list[$val->comment_srl] = $oCommentItem;
			}
		}
		
		if(is_array($best_list))
		{
			$comment_list = array_merge($best_list,$comment_list);
		}
		
		require_once(_XE_PATH_.'addons/best_comment/document.add.php');
		
		$documentAdd = new documentAdd();
		$documentAdd->setDocument($document_srl);
		$documentAdd->setCommentList($comment_list);
		$documentAdd->comment_page_navigation = $oDocument->comment_page_navigation;
		Context::set('oDocument',$documentAdd);
	}
	else if($called_position == 'before_display_content' && $addon_info->show_best != 'N')
	{
		Context::addHtmlHeader('<style>.best_comment{padding:0 3px 1px 3px; background-color:#FF0000; color:#fff; margin-left:5px;  font-family:Tahoma,Geneva,sans-serif; font-weight:bold;}</style>');
		
		$output = str_replace('{best_comment}</a>', '</a><span class="best_comment">Best</span>', $output);
		$output = str_replace('{best_comment}', '<span class="best_comment">Best</span>', $output);
	}