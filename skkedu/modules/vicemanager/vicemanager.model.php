<?php
/**
     * @class  ViceManagerModel
     * @author showjean	
     * @brief  ViceManager module model class
     **/

    class vicemanagerModel extends vicemanager {

        /**
         * @brief 
         **/
        function init() {
        }
		
        /**
         * @brief 
         **/
        function isVicemanager($member_srl) {
			$oAdminModel = &getAdminModel('vicemanager');
			$config = $oAdminModel->getConfig();
			
			$managers = array();
				
			if(strlen($config->vicemanager_srls) > 0){
				$managers = explode(',', $config->vicemanager_srls);
			}
			
			if($config->permit_module_admin == true){
				$result = executeQuery('vicemanager.getModuleAdminMemberSrl');
				$result = $result->data;
				if($result){
					if(is_array($result)){
						foreach($result as $key=>$val){
							$managers[] = $val->member_srl;
						}
					}else if($result->member_srl){
						$managers[] = $result->member_srl;
					}
				}
			}
			
			return in_array($member_srl, $managers);
        }
    }
?>
