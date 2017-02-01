<?php
	/**
	 * @class  sejin7940_commentModel
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_comment 모듈의 Model class
	 **/

	class sejin7940_commentModel extends sejin7940_comment {

		/**
		 * @brief Initialization
		 **/
		function init() {
			
		}


		/**
		 * @brief 모듈의 global 설정 구함
		 */
		function getModuleConfig() {
			static $config = null;
			if(is_null($config)) {
				$oModuleModel = &getModel('module');
				$config = $oModuleModel->getModuleConfig('sejin7940_comment');
			}

			if(!$config->comment_cut_size) $config->comment_cut_size='200';
			if(!$config->title_cut_size) $config->title_cut_size='30';
			if(!$config->skin) $config->skin = "default";

			return $config;
		}



		function getCommnetList($member_srl, $page = 1) {
        	// cache controll
			$oCacheHandler = &CacheHandler::getInstance('object');
			if($oCacheHandler->isSupport()){
				$object_key = 'object:'.$member_srl.'_page'.$page;

				$cache_key = $oCacheHandler->getGroupKey('ownCommentList', $object_key);
				$output = $oCacheHandler->get($cache_key);

				if($output)
				{
					return $output;
				}
			}
			//$oCacheHandler->delete($cache_key);

			$args->member = $member_srl;
			$args->page = $page;
            $args->list_count = 20; // / the number of postings to appear on a single page
            $args->page_count = 10; // / the number of pages to appear on the page navigation

            $args->sort_index = 'list_order'; // /< Sorting values

            $args->module_srl = Context::get('module_srl');
			/*
			$search_target = Context::get('search_target');
			$search_keyword = Context::get('search_keyword');
			if ($search_target == 'is_published' && $search_keyword == 'Y')
			{
				$args->status = 1;
			}
			if ($search_target == 'is_published' && $search_keyword == 'N')
			{
				$args->status = 0;
			}
			*/

			$args->status = 1;  // 삭제 된 댓글  ststus =2  안 나오게 하기 위해서
				
            // get a list by using comment->getCommentList. 
            $oCommentModel = &getModel('comment');
			$secretNameList = $oCommentModel->getSecretNameList();

			$columnList = array('comment_srl', 'document_srl', 'is_secret', 'status', 'content', 'comments.member_srl', 'comments.nick_name', 'comments.regdate', 'ipaddress', 'voted_count', 'blamed_count');

            $output = $this->getTotalCommentList($args, $columnList);

			//insert in cache
			if($oCacheHandler->isSupport()) $oCacheHandler->put($cache_key,$output);

			 return $output;
		}


		// comment 모듈에 있는 함수 가져옴 - 익명 댓글 추출을 위해 조금 수정
		function getTotalCommentList($obj, $columnList = array())
		{
			$query_id = 'sejin7940_comment.getTotalCommentList';

			// Variables
			$args = new stdClass();
			$args->sort_index = 'list_order';
			$args->page = $obj->page ? $obj->page : 1;
			$args->list_count = $obj->list_count ? $obj->list_count : 20;
			$args->page_count = $obj->page_count ? $obj->page_count : 10;
			$args->s_module_srl = $obj->module_srl;
			$args->exclude_module_srl = $obj->exclude_module_srl;

			// check if module is using comment validation system
			$oCommentController = getController("comment");
			$is_using_validation = $oCommentController->isModuleUsingPublishValidation($obj->module_srl);
			if($is_using_validation)
			{
				$args->s_is_published = 1;
			}

			// Search options
			$search_target = $obj->search_target ? $obj->search_target : trim(Context::get('search_target'));
			$search_keyword = $obj->search_keyword ? $obj->search_keyword : trim(Context::get('search_keyword'));
			if($search_target && $search_keyword)
			{
				switch($search_target)
				{
					case 'content' :
						if($search_keyword)
						{
							$search_keyword = str_replace(' ', '%', $search_keyword);
						}

						$args->s_content = $search_keyword;
						break;

					case 'user_id' :
						if($search_keyword)
						{
							$search_keyword = str_replace(' ', '%', $search_keyword);
						}

						$args->s_user_id = $search_keyword;
						$query_id = 'comment.getTotalCommentListWithinMember';
						$args->sort_index = 'comments.list_order';
						break;

					case 'user_name' :
						if($search_keyword)
						{
							$search_keyword = str_replace(' ', '%', $search_keyword);
						}

						$args->s_user_name = $search_keyword;
						break;

					case 'nick_name' :
						if($search_keyword)
						{
							$search_keyword = str_replace(' ', '%', $search_keyword);
						}

						$args->s_nick_name = $search_keyword;
						break;

					case 'email_address' :
						if($search_keyword)
						{
							$search_keyword = str_replace(' ', '%', $search_keyword);
						}

						$args->s_email_address = $search_keyword;
						break;

					case 'homepage' :
						if($search_keyword)
						{
							$search_keyword = str_replace(' ', '%', $search_keyword);
						}

						$args->s_homepage = $search_keyword;
						break;

					case 'regdate' :
						$args->s_regdate = $search_keyword;
						break;

					case 'last_update' :
						$args->s_last_upate = $search_keyword;
						break;

					case 'ipaddress' :
						$args->s_ipaddress = $search_keyword;
						break;

					case 'is_secret' :
						$args->s_is_secret = $search_keyword;
						break;

					case 'is_published' :
						if($search_keyword == 'Y')
						{
							$args->s_is_published = 1;
						}

						if($search_keyword == 'N')
						{
							$args->s_is_published = 0;
						}

						break;

					case 'module':
						$args->s_module_srl = (int) $search_keyword;
						break;

					case 'member_srl' :
						$args->{"s_" . $search_target} = (int) $search_keyword;
						break;
				}
			}

			// comment.getTotalCommentList query execution
			$output = executeQueryArray($query_id, $args, $columnList);

			// return when no result or error occurance
			if(!$output->toBool() || !count($output->data))
			{
				return $output;
			}

			foreach($output->data as $key => $val)
			{
				unset($_oComment);
				$_oComment = new CommentItem(0);
				$_oComment->setAttribute($val);
				$output->data[$key] = $_oComment;
			}

			return $output;
		}

	}
?>