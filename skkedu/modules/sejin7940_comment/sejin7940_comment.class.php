<?php
	/**
	 * @class  sejin7940_comment
	 * @author sejin7940 (sejin7940@nate.com)
	 * @brief  sejin7940_comment 모듈의 상위 class
	 **/

	class sejin7940_comment extends ModuleObject {

		/**
		 * @brief 설치시 추가 작업이 필요할시 구현
		 **/
		function moduleInstall() {
			
			return new Object();
		}

		/**
		 * @brief 설치가 이상이 없는지 체크하는 method
		 **/
		function checkUpdate() {
			$oModuleModel = &getModel('module');

			// 내 댓글 내역 트리거 추가 (2012/06/19)
			if(!$oModuleModel->getTrigger('moduleHandler.init', 'sejin7940_comment', 'controller', 'triggerAddMemberMenu', 'after')) return true;
		
			return false;
		}

		/**
		 * @brief 업데이트 실행
		 **/
		function moduleUpdate() {
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');

			// 내 댓글 내역 트리거 추가 (2012/06/19)
			if(!$oModuleModel->getTrigger('moduleHandler.init', 'sejin7940_comment', 'controller', 'triggerAddMemberMenu', 'after'))
				$oModuleController->insertTrigger('moduleHandler.init', 'sejin7940_comment', 'controller', 'triggerAddMemberMenu', 'after');

			return new Object(0, 'success_updated');
		}

		/**
		 * @brief 캐시 파일 재생성
		 **/
		function recompileCache() {
			
		}
	}
?>