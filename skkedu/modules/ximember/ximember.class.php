<?php
    /**
     * @class  ximember
     * @author Xiso (xiso@xiso.co.kr)
     * @brief  Xiso Member module
     **/
	
	require_once(_XE_PATH_.'modules/member/member.class.php');
    class ximember extends member {
        /**
         * @brief install the module
         **/
        function moduleInstall() {
			
			//memberInsert Trigger
            $oModuleController = &getController('module');
            $oModuleController->insertTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMemberBefore', 'before');
            $oModuleController->insertTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMember', 'after');
            
            //Addon Trigger
            $oModuleController->insertTrigger('moduleHandler.init', 'ximember', 'controller', 'triggerModuleHandlerInit', 'after');
            $oModuleController->insertTrigger('moduleHandler.proc', 'ximember', 'controller', 'triggerModuleHandlerProc', 'after');
            $oModuleController->insertTrigger('display', 'ximember', 'controller', 'triggerDisplay', 'before');
            
            //MemberLeave Trigger
            $oModuleController->insertTrigger('member.deleteMember', 'ximember', 'controller', 'triggerDeleteMember', 'before');
			
            return new Object();
        }

        /**
         * @brief chgeck module method
         **/
        function checkUpdate() {
			
			//check Trigger
			$oModuleModel = &getModel('module');
            if(!$oModuleModel->getTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMemberBefore', 'before')) return true;
            if(!$oModuleModel->getTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMember', 'after')) return true;
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'ximember', 'controller', 'triggerModuleHandlerInit', 'after')) return true;
            if(!$oModuleModel->getTrigger('moduleHandler.proc', 'ximember', 'controller', 'triggerModuleHandlerProc', 'after')) return true;
            if(!$oModuleModel->getTrigger('display', 'ximember', 'controller', 'triggerDisplay', 'before')) return true;
            if(!$oModuleModel->getTrigger('member.deleteMember', 'ximember', 'controller', 'triggerDeleteMember', 'before')) return true;
			
			//okname 모듈이 설치된 경우
			
			//membership 모듈이 설치된 경우
			
			//회원가입 확장모듈이 설치된 경우
			
			return false;
        }

        /**
         * @brief update module
         **/
        function moduleUpdate() {
			
			//check Trigger
			$oModuleController = &getController('module');
			$oModuleModel = &getModel('module');
            if(!$oModuleModel->getTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMemberBefore', 'before'));
				$oModuleController->insertTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMemberBefore', 'before');
            if(!$oModuleModel->getTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMember', 'after'));
				$oModuleController->insertTrigger('member.insertMember', 'ximember', 'controller', 'triggerInsertMember', 'after');
            if(!$oModuleModel->getTrigger('moduleHandler.init', 'ximember', 'controller', 'triggerModuleHandlerInit', 'after'));
				$oModuleController->insertTrigger('moduleHandler.init', 'ximember', 'controller', 'triggerModuleHandlerInit', 'after');
            if(!$oModuleModel->getTrigger('moduleHandler.proc', 'ximember', 'controller', 'triggerModuleHandlerProc', 'after'));
				$oModuleController->insertTrigger('moduleHandler.proc', 'ximember', 'controller', 'triggerModuleHandlerProc', 'after');
            if(!$oModuleModel->getTrigger('display', 'ximember', 'controller', 'triggerDisplay', 'before'));
				$oModuleController->insertTrigger('display', 'ximember', 'controller', 'triggerDisplay', 'before');
            if(!$oModuleModel->getTrigger('member.deleteMember', 'ximember', 'controller', 'triggerDeleteMember', 'before'));
				$oModuleController->insertTrigger('member.deleteMember', 'ximember', 'controller', 'triggerDeleteMember', 'before');
			
            return new Object(0, 'success_updated');
        }

		function moduleUninstall() {
			return new Object();
		}

        /**
         * @brief re-generate the cache files
         **/
        function recompileCache() {
        }

    }
?>
