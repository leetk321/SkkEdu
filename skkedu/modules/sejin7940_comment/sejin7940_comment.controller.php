<?php
	/**
	 * @class  sejin7940_commentController
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_comment 모듈의 Controller class
	 **/

	class sejin7940_commentController extends sejin7940_comment {

		/**
		 * @brief Initialization
		 **/
		function init() {
			
		}

		// 회원 메뉴 쪽에 추가
		function triggerAddMemberMenu(&$obj) {
			if(!Context::get('is_logged')) return new Object();
			$target_srl = Context::get('target_srl');

			$oMemberController = &getController('member');
			$oMemberController->addMemberMenu('dispSejin7940_commentOwnComment', 'cmd_my_comment');
			return new Object();
		}

		// 내 댓글 삭제
		function procSejin7940_commentDeleteComment() {
			if(!Context::get('is_logged')) return new Object(-1,'msg_invalid_request');
			$comment_srl = Context::get('target_srl');
			if(!$comment_srl) return new Object(-1,'msg_invalid_request');

			$oCommentController = &getController('comment');
			$output = $oCommentController->deleteComment($comment_srl);

			if(!$output->toBool())
			{
				return new Object(-1,'대댓글이 존재하여 댓글삭제가 불가능합니다.');
			}
			return new Object(0, '댓글이 삭제되었습니다. ');

		}

	}
?>