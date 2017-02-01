<?
	/**
	 * @class ViceManager
	 * @author showjean
	 * @brief ViceManager 모듈의 high class 
	 */
	 
	class vicemanager extends ModuleObject {
		/**
		 * @brief 모듈 설치
		 */
		function moduleInstall() {
		
			return new Object();
		}

		/**
		 * @brief 모듈 삭제
		 */		
		function moduleUninstall() {

			return new Object();
		}

		/**
		 * @brief 업데이트가 필요한지 확인
		 **/
		function checkUpdate() {

			return false;
		}

		/**
		 * @brief 모듈 업데이트
		 **/		
		function moduleUpdate() {

			return new Object(0,'success_updated');
		}
		
		/**
		 * @brief 캐시 파일 재생성
		 **/
		function recompileCache() {
		}
	}