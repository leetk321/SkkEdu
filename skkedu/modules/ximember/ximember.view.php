<?php
    /**
     * @class  ximember
     * @author Xiso (xiso@xiso.co.kr)
     * @brief  Xiso Member module
     **/
	
	require_once(_XE_PATH_.'modules/member/member.view.php');
    class ximemberView extends memberView {
		/**
		 * Config Load
		 * @var array
		 */
		var $xiConfig = NULL;
		var $member_config = NULL;

		/**
         * @brief initialization
         **/
        function init() {
			//get Config
			$oModuleModel = &getModel('module');
			$this->xiConfig = $oModuleModel->getModuleConfig('ximember');
			Context::set('xiConfig', $this->xiConfig);
			
			// Get the member configuration
			$oMemberModel = getModel('member');
			$this->member_config = $oMemberModel->getMemberConfig();
			
			$skin = $this->xiConfig->skin;
			// Set the template path
			if(!$skin)
			{
				$skin = 'default';
				$template_path = sprintf('%sskins/%s', $this->module_path, $skin);
			}
			else
			{
				//check theme
				$config_parse = explode('|@|', $skin);
				if (count($config_parse) > 1)
				{
					$template_path = sprintf('./themes/%s/modules/ximember/', $config_parse[0]);
				}
				else
				{
					$template_path = sprintf('%sskins/%s', $this->module_path, $skin);
				}
			}
			// Template path
			$this->setTemplatePath($template_path);

			$oLayoutModel = getModel('layout');
			$layout_info = $oLayoutModel->getLayout($this->xiConfig->layout_srl);
			if($layout_info)
			{
				$this->module_info->layout_srl = $this->xiConfig->layout_srl;
				$this->setLayoutPath($layout_info->path);
			}
        }
		
		function dispXimemberLoginForm(){
			//check Nomember Use KCB
			if($this->xiConfig->limit['use_nomember'] == "Y"){
				//load KCB Plugin
				if($this->xiConfig->kcb->use_auth['ipin'] || $this->xiConfig->kcb->use_auth['mobile']){
					if(!$this->xiConfig->kcb->module_path) return $this->stop('xi_kcb_notfounded_module');
				}
				if($this->xiConfig->kcb->use_auth['ipin']) $ret = $this->loadIAuthIpin();
				if($ret != "000") return $this->stop($this->loadAuthError($ret));
				if($this->xiConfig->kcb->use_auth['mobile']) $this->loadAuthMobile();
				if($ret != "000") return $this->stop($this->loadAuthError($ret));
			}
			Context::set('auth_info',$_SESSION['auth_info']);
			
            // Set a template file
			Context::set('config',$this->xiConfig);
            Context::set('referer_url', htmlspecialchars($_SERVER['HTTP_REFERER']));
			
			$this->dispMemberLoginForm();
		}
		
		function dispXimemberSignUpForm(){
			//checkLogin
			$oMemberModel = &getModel('member');
            // Get the member information if logged-in
            if($oMemberModel->isLogged()) return $this->stop('msg_already_logged');
			
			Context::set('config',$this->xiConfig);
			
			//check Agreements Useable List
			if($this->xiConfig->agreements){
				foreach($this->xiConfig->agreements as $agree_srl => $agreement){
					if($agreement->is_use == 'Y') $agree_useable[$agree_srl] = $agreement;
				}
			}
			//agree agreements?
			if(is_array($agree_useable)){
				if(!$_SESSION['print_agreement']){
					$disagree=true;
					$_SESSION['print_agreement'] = "Y";
				}
				Context::set('use_agreement','Y');
				foreach($agree_useable as $key => $val){
					if($key == "default") $agree_useable['default']->content = $this->member_config->agreement;
					if($val->required == 'Y' && $_SESSION['agreements'][$key] != "Y") $disagree=true;
				}
				if($disagree){
					Context::set('agreement_list',$agree_useable);
					//가입시 KCB인증을 사용하지 않는경우
					if($this->xiConfig->limit['use_signup'] != 'Y') return $this->setTemplateFile('signup_before');
				}
			}
			 
			//complete auth? 
			if($this->xiConfig->limit['use_signup'] == 'Y'){
				//cancel?
				if($_SESSION['auth_info']["resultCd"] == "B123"){
					unset($_SESSION['agreements']);
					unset($_SESSION['auth_info']);
					return $this->stop('msg_cancel_auth');
				}
				if(!$_SESSION['auth_info']["DI"] || !$_SESSION['auth_info']["CI"]){
					//load KCB Plugin
					if($this->xiConfig->kcb->use_auth['ipin'] || $this->xiConfig->kcb->use_auth['mobile']){
						if(!$this->xiConfig->kcb->module_path) return $this->stop('xi_kcb_notfounded_module');
					}
					if($this->xiConfig->kcb->use_auth['ipin']) $ret = $this->loadIAuthIpin();
					if($ret != "000") return $this->stop($this->loadAuthError($ret));
					if($this->xiConfig->kcb->use_auth['mobile']) $this->loadAuthMobile();
					if($ret != "000") return $this->stop($this->loadAuthError($ret));
					
					return $this->setTemplateFile('signup_before');
				}
			}
			
			//check Duplicate
			if($this->xiConfig->limit['use_duplicate'] == 'Y'){
				$chk_obj->di = $_SESSION['auth_info']["DI"];
				$chk_obj->ci = $_SESSION['auth_info']["CI"];
				$output = executeQuery('ximember.chkDuplicate',$chk_obj);
				if($output->toBool() && $output->data->count != '0') return $this->stop('msg_user_duplicated');
			}
			
			//default Method
			$this->dispMemberSignUpForm();
			
			if($this->xiConfig->extra_vars){
				$blockmethod = array();
				$disablemethod = array();
				$enabledmethod = array();
				foreach($this->xiConfig->extra_vars as $item_name => $extra_vars){
					if($extra_vars->signup == "show_disabled") $blockmethod[] = $item_name;
					if($extra_vars->signup == "modify_disabled") $disablemethod[] = $item_name;
					if($extra_vars->signup == "modify_enabled") $enabledmethod[] = $item_name;
				}
				//set blockmethod(가입폼에 노출하지않을)
				$blockmethod[] = 'signature'; //default
				Context::set('blockmethod',$blockmethod);
				Context::set('disablemethod',$disablemethod);
				Context::set('enabledmethod',$enabledmethod);
			}
			
			if($_SESSION['voted_id']) Context::set('voted_id',$_SESSION['voted_id']);
			
			Context::set('auth_info',$_SESSION['auth_info']);
		}
		
		function dispXimemberModifyInfo(){
			if($_SESSION['rechecked_password_step'] != 'VALIDATE_PASSWORD' && $_SESSION['rechecked_password_step'] != 'INPUT_DATA')
			{
				$this->dispMemberModifyInfoBefore();
				return;
			}
			$_SESSION['rechecked_password_step'] = 'INPUT_DATA';

			$member_config = $this->member_config;

			$oMemberModel = getModel('member');
			// A message appears if the user is not logged-in
			if(!$oMemberModel->isLogged()) return $this->stop('msg_not_logged');

			$logged_info = Context::get('logged_info');
			$member_srl = $logged_info->member_srl;

			$columnList = array('member_srl', 'user_id', 'user_name', 'nick_name', 'email_address', 'find_account_answer', 'homepage', 'blog', 'birthday', 'allow_mailing');
			$member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl, 0, $columnList);
			$member_info->signature = $oMemberModel->getSignature($member_srl);
			Context::set('member_info',$member_info);

			// Get a list of extend join form
			Context::set('extend_form_list', $oMemberModel->getCombineJoinForm($member_info));

			// Editor of the module set for signing by calling getEditor
			if($member_info->member_srl)
			{
				$oEditorModel = getModel('editor');
				$option = new stdClass();
				$option->primary_key_name = 'member_srl';
				$option->content_key_name = 'signature';
				$option->allow_fileupload = false;
				$option->enable_autosave = false;
				$option->enable_default_component = true;
				$option->enable_component = false;
				$option->resizable = false;
				$option->disable_html = true;
				$option->height = 200;
				$option->skin = $member_config->signature_editor_skin;
				$option->colorset = $member_config->sel_editor_colorset;
				$editor = $oEditorModel->getEditor($member_info->member_srl, $option);
				Context::set('editor', $editor);
			}

			$this->member_info = $member_info;

			$oMemberAdminView = getAdminView('member');
			$formTags = $oMemberAdminView->_getMemberInputTag($member_info);
			Context::set('formTags', $formTags);

			global $lang;
			$identifierForm = new stdClass();
			$identifierForm->title = $lang->{$member_config->identifier};
			$identifierForm->name = $member_config->identifier;
			$identifierForm->value = $member_info->{$member_config->identifier};
			Context::set('identifierForm', $identifierForm);

			$this->addExtraFormValidatorMessage();
			
			if($this->xiConfig->extra_vars){
				$blockmethod = array();
				$disablemethod = array();
				$enabledmethod = array();
				foreach($this->xiConfig->extra_vars as $item_name => $extra_vars){
					if($extra_vars->modify == "show_disabled") $blockmethod[] = $item_name;
					if($extra_vars->modify == "modify_disabled") $disablemethod[] = $item_name;
					if($extra_vars->modify == "modify_enabled") $enabledmethod[] = $item_name;
				}
				//set blockmethod(가입폼에 노출하지않을)
				//$blockmethod[] = 'voted_member_srl'; //default
				Context::set('blockmethod',$blockmethod);
				Context::set('disablemethod',$disablemethod);
				Context::set('enabledmethod',$enabledmethod);
			}
			
			// Set a template file
			$this->setTemplateFile('modify_info');
			Context::set('config',$this->xiConfig);
		}
		
		function dispXimemberAuthPage(){
			if($this->xiConfig->kcb->use_auth['ipin']) $ret = $this->loadIAuthIpin();
			if($ret != "000") return $this->stop($this->loadAuthError($ret));
			if($this->xiConfig->kcb->use_auth['mobile']) $this->loadAuthMobile();
			if($ret != "000") return $this->stop($this->loadAuthError($ret));
			
			$this->setTemplateFile('auth_page');
		}
		
		function loadIAuthIpin(){
			//Load Ipin
			$idpUrl    = "https://ipin.ok-name.co.kr/tis/ti/POTI90B_SendCertInfo.jsp";
			$returnUrl = "http://{$_SERVER['HTTP_HOST']}/?act=dispXimemberIpinResult";
			$idpCode   = "V";	
			$cpCode    = $this->xiConfig->kcb->kcb_shopid;		// 회원사 코드 (회원사 아이디)

			$exe = $this->xiConfig->kcb->module_file;
			$keyPath = $this->xiConfig->kcb->module_path."okname.key";
			$logPath = "./files/ximember/logs";
			if(!FileHandler::makeDir($logPath)) return new Object(-1, 'msg_error');
			$memid = $cpCode;	 $reserved1 = "0"; $reserved2 = "0";
			$EndPointURL = "http://www.ok-name.co.kr/KcbWebService/OkNameService";
			$option = "CLU";

			//set Cmd & Run module
			$cmd = "$exe $keyPath $memid \"{$reserved1}\" \"{$reserved2}\" $EndPointURL $logPath $option";
			exec($cmd, $out, $ret);
			
			Context::set('pubkey',$out[0]);
			Context::set('sig',$out[1]);
			Context::set('curtime',$out[2]);
			Context::set('idpCode',$idpCode);
			Context::set('idpUrl',$idpUrl);
			Context::set('cpCode',$cpCode);
			Context::set('returnUrl',$returnUrl);
			return $ret;
		}
		
		function loadAuthMobile(){
			//set Default Variables
			$name = "x"; $birthday = "x"; $gender = "x"; $nation="x"; $telComCd="x"; $telNo="x";
			// 0 = not posted data, no use Filter
			$inTpBit = 0;
			$svcTxSeqno = getNextSequence();
			
			$memId = $this->xiConfig->kcb->kcb_shopid;
			$serverIp = $_SERVER["SERVER_ADDR"]; $siteDomain = "http://{$_SERVER['HTTP_HOST']}";
			
			$rsv1 = "0"; $rsv2 = "0"; $rsv3 = "0";
			$hsCertMsrCd = "10"; //10 = is cell phone
			$hsCertRqstCausCd = "00"; // 00 = is signup
			$returnMsg = "x";
			$returnUrl = "http://{$_SERVER['HTTP_HOST']}/index.php?act=dispXimemberMobileResult";
			
			$endPointURL = "http://safe.ok-name.co.kr/KcbWebService/OkNameService"; // is real server
			
			$exe = $this->xiConfig->kcb->module_file;
			$logPath = "./files/ximember/logs";
			if(!FileHandler::makeDir($logPath)) return new Object(-1, 'msg_error');
			
			$options = "QLU";
			$cmd = "$exe $svcTxSeqno \"$name\" $birthday $gender $nation $telComCd $telNo $rsv1 $rsv2 $rsv3 \"$returnMsg\" $returnUrl $inTpBit $hsCertMsrCd $hsCertRqstCausCd $memId $serverIp $siteDomain $endPointURL $logPath $options";
			
			//run Module
			exec($cmd, $out, $ret);
			Context::set('rqst_data',$out[2]);
			return $ret;
		}
		
		//아이핀 결과처리
		function dispXimemberIpinResult(){
			@$encPsnlInfo = $_POST["encPsnlInfo"];
			@$WEBPUBKEY = trim($_POST["WEBPUBKEY"]);
			@$WEBSIGNATURE = trim($_POST["WEBSIGNATURE"]);
			//added Filter
			if(preg_match('~[^0-9a-zA-Z+/=]~', $encPsnlInfo, $match)) return $this->stop('check_parameater');
			if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBPUBKEY, $match)) return $this->stop('check_parameater');
			if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBSIGNATURE, $match)) return $this->stop('check_parameater');
			
			//make Keyfile for KCB server
			$exe = $this->xiConfig->kcb->module_file;
			$keyPath = $this->xiConfig->kcb->module_path."okname.key";
			$logPath = "./files/ximember/logs";
			if(!FileHandler::makeDir($logPath)) return new Object(-1, 'msg_error');
			$cpCode    = $this->xiConfig->kcb->kcb_shopid;
			$EndPointURL = "http://www.ok-name.co.kr/KcbWebService/OkNameService";
			$cpubkey = $WEBPUBKEY;       //server publickey
			$csig = $WEBSIGNATURE;    //server signature
			$encdata = $encPsnlInfo;     //PERSONALINFO
			$option = "SLU";
			
			//set CMD & run module
			$cmd = "$exe $keyPath $cpCode $EndPointURL $cpubkey $csig $encdata $logPath $option";
			exec($cmd, $out, $ret);
			
			Context::set('layout','none');
			if($ret != "000") return $this->stop($this->loadAuthError($ret));
			
			// 결과라인에서 값을 추출
			foreach($out as $a => $b) { if($a < 13) { $field[$a] = $b; } }
			
			//세션에 저장
			$_SESSION['auth_info'] = array();
			$_SESSION['auth_info']["auth_date"] =  date('Ymdhis');
			$_SESSION['auth_info']["DI"] = $field[0]; //DI
			$_SESSION['auth_info']["CI"] = $field[1]; //CI
			$_SESSION['auth_info']["user_name"] = $field[6]; //성명
			$_SESSION['auth_info']["birthday"] = $field[11]; //생년월일
			$_SESSION['auth_info']["age"] = substr(date('Ymd')-$field[11],0,2); //만 나이
			
			//확장변수는 변수를 교체하여 넣음.
			$_SESSION['auth_info']["sex"] = ($field[9] == 1) ? $this->xiConfig->extra_vars['sex']->typeA : $this->xiConfig->extra_vars['sex']->typeB; //성별
			$_SESSION['auth_info']["foreign"] = ($field[10] == 1) ? $this->xiConfig->extra_vars['foreign']->typeB : $this->xiConfig->extra_vars['foreign']->typeA; //내외국인구분
			$_SESSION['auth_info']["auth_type"] = $this->xiConfig->extra_vars['auth_type']->typeA; //인증수단
			//원본은 따로저장
			$_SESSION['auth_info']["ori_sex"] = ($field[9] == 1) ? "M" : "F";
			$_SESSION['auth_info']["ori_for"] = ($field[10] == 1) ? "Y" : "N";
			$_SESSION['auth_info']["ori_auth"] = "I";
			
			$auth_info = $_SESSION['auth_info'];
			
			$this->checkSignupLimit();
			
			//레이아웃과의 혹시나있을 에러를 방지하기위해 제거
			Context::set('layout','none');
			Context::set('auth_info',$auth_info);
			Context::set('encPsnlInfo',$encPsnlInfo);
			
			$this->setTemplatePath($this->module_path."tpl/");
			$this->setTemplateFile('auth_result');
		}
		
		//안심본인인증 결과처리
		function dispXimemberMobileResult(){
			$rqstSiteNm					=	$_POST["rqst_site_nm"];
			$hsCertRqstCausCd		=	$_POST["hs_cert_rqst_caus_cd"];
			$encInfo = $_POST["encInfo"];
			$WEBPUBKEY = trim($_POST["WEBPUBKEY"]);
			$WEBSIGNATURE = trim($_POST["WEBSIGNATURE"]);

			//added Filter
			if(preg_match('~[^0-9a-zA-Z+/=]~', $encInfo, $match)) return $this->stop('check_parameater');
			if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBPUBKEY, $match)) return $this->stop('check_parameater');
			if(preg_match('~[^0-9a-zA-Z+/=]~', $WEBSIGNATURE, $match)) return $this->stop('check_parameater');
			
			$memId    = $this->xiConfig->kcb->kcb_shopid;
			$exe = $this->xiConfig->kcb->module_file;
			$keyPath = $this->xiConfig->kcb->module_path."safecert_$memId.key";
			$logPath = "./files/ximember/logs";
			if(!FileHandler::makeDir($logPath)) return new Object(-1, 'msg_error');
			
			$endPointUrl = "http://safe.ok-name.co.kr/KcbWebService/OkNameService";// 운영 서버
			$options = "SLU";	// S:인증결과복호화
			
			//run CMD
			$cmd = "$exe $keyPath $memId $endPointUrl $WEBPUBKEY $WEBSIGNATURE $encInfo $logPath $options";
			exec($cmd, $out, $ret);
			
			Context::set('layout','none');
			if($ret != "000") return $this->stop($this->loadAuthError($ret));
			
			//make result Array
			foreach($out as $a => $b) { if($a < 17) { $field[$a] = $b; } }
			
			//세션에 저장
			$_SESSION['auth_info'] = array();
			$_SESSION['auth_info']["resultCd"] = $field[0]; //처리결과코드
			$_SESSION['auth_info']["resultMsg"] = $field[1]; //처리결과메시지
			$_SESSION['auth_info']["hsCertSvcTxSeqno"] = $field[2]; //거래일련번호 (sequence처리)
			$_SESSION['auth_info']["auth_date"] = $field[3]; //인증일시
			$_SESSION['auth_info']["DI"] = $field[4]; //DI
			$_SESSION['auth_info']["CI"] = $field[5]; //CI
			$_SESSION['auth_info']["user_name"] = $field[7]; //성명
			$_SESSION['auth_info']["birthday"] = $field[8]; //생년월일
			$_SESSION['auth_info']["age"] = substr(date('Ymd')-$field[8],0,2); //만 나이
			$_SESSION['auth_info']["agency"] = $field[11]; //통신사코드
			$_SESSION['auth_info']["mobile"] = $field[12]; //휴대폰번호
			
			//얘네 세명은 원값을 따로저장함
			$_SESSION['auth_info']["sex"] = ($field[9] == 1) ? $this->xiConfig->extra_vars['sex']->typeA : $this->xiConfig->extra_vars['sex']->typeB; //성별
			$_SESSION['auth_info']["foreign"] = ($field[10] == 1) ? $this->xiConfig->extra_vars['foreign']->typeA : $this->xiConfig->extra_vars['foreign']->typeB; //내외국인구분
			$_SESSION['auth_info']["auth_type"] = $this->xiConfig->extra_vars['auth_type']->typeB; //인증수단
			
			$_SESSION['auth_info']["ori_sex"] = ($field[9] == 1) ? "M" : "F";
			$_SESSION['auth_info']["ori_for"] = ($field[10] == 1) ? "N" : "Y";
			$_SESSION['auth_info']["ori_auth"] = "M";
			
			$auth_info = $_SESSION['auth_info'];
			
			$this->checkSignupLimit();
			
			Context::set('auth_info',$auth_info);
			Context::set('hsCertMsrCd',$hsCertMsrCd);
			
			$this->setTemplatePath($this->module_path."tpl/");
			$this->setTemplateFile('auth_result');
		}
		
		function checkSignupLimit(){
			//가입 및 접근 연령, 성별제한이 있는지 체크
			if($this->xiConfig->limit['use_signup_limit'] == "Y"){
				$limit_age = $this->xiConfig->limit['fullage'];
				$limit_type = $this->xiConfig->limit['limit_type'];
				$limit_sex = $this->xiConfig->limit['limit_sex'];
				$block_message = $this->xiConfig->limit['block_message'];
				
				if($limit_type == 'limit_up') if($_SESSION['auth_info']["age"] <= $limit_age) $join_block = true;
				if($limit_type == 'limit_down') if($_SESSION['auth_info']["age"] >= $limit_age) $join_block = true;
				if($limit_sex == "limit_M") if($_SESSION['auth_info']['ori_sex'] == "M") $join_block = true;
				if($limit_sex == "limit_F") if($_SESSION['auth_info']['ori_sex'] == "F") $join_block = true;
				
				if($join_block){
					//가입거부 메세지
					Context::set('is_signup_limit','Y');
					unset($_SESSION['auth_info']);
					$this->setError(-1);
					$this->setMessage($block_message);
					return false;
				}else{
					return true;
				}
			}else{
				return true;
			}
		}
		
		function loadAuthError($code){
			$error_list = array(
			"000" => "정상처리",
			"001" => "해당 주민번호 미존재 오류",
			"002" => "해당 이름 미존재 오류 ",
			"003" => "주민번호 형식 체계 오류",
			"004" => "요청 서버 IP 오류",
			"005" => "요청 서버 도메인 오류",
			"006" => "잔여건수 사용 초과, 충전제사용시 잔액부족.",
			"007" => "제휴가맹점 유효기간 만료",
			"008" => "제휴가맹점 코드 오류",
			"009" => "제휴가맹점 키 오류",
			"010" => "계약되지 않은 서비스 타입",
			"011" => "데이터 복호화 오류 또는 서버OS의 종류/비트가 맞지 않는 경우",
			"012" => "데이터 암호화 오류",
			"013" => "미승인 가맹점",
			"014" => "클라이언트 체크타입 오류",
			"015" => "접근 가능 대역 오류",
			"016" => "명의차단상태",
			"017" => "입력값 오류(이름또는주민번호 Null)",
			"018" => "실명요청 승인 대기 상태",
			"019" => "실명요청 반려 상태",
			"020" => "통신오류(KAIT)",
			"021" => "입력값 오류(조회점포코드 Null)",
			"022" => "입력값 오류(조회점포명 Null)",
			"023" => "입력값 오류(조회점포ID Null)",
			"024" => "조회대상자 구분 코드 오류",
			"025" => "실명 요청사유 코드 오류",
			"026" => "요청 인터페이스 오류",
			"027" => "성인인증일 경우 미성년",
			"028" => "외국인번호 형식 체계 오류",
			"029" => "KAIT 지연",
			"030" => "네트워크 오류 (타임아웃, 웹서비스 연결지연 등)",
			"031" => "요청시도IP차단상태",
			"032" => "사용자 미존재 오류(사망자)",
			"033" => "사용자 미존재 오류(예외자)",
			"035" => "성별코드오류",
			"081" => "이통사DB오류",
			"082" => "이통사SCI통신오류",
			"083" => "이통사DB암호화서버연결실패",
			"084" => "이통사CI/DI연동오류",
			"085" => "이통사CP코드없음",
			"086" => "이통사기타오류",
			"091" => "인증시도횟수초과",
			"092" => "이통사 연동 오류",
			"097" => "서비스거래번호 없음",
			"098" => "서비스거래번호 오류(길이/형식)",
			"099" => "기타오류",
			"100" => "본인인증 처리 실패",
			"101" => "기요청 서비스 거래번호",
			"102" => "선행 요청정보 없음",
			"103" => "서비스 사용 가능일이 아닙니다.",
			"104" => "서비스 사용 중지 상태 입니다.",
			"105" => "서비스 기간이 만료 되었습니다.",
			"106" => "요청사이트도메인 없음",
			"107" => "본인인증수단 없음",
			"108" => "본인인증 요청사유 없음",
			"109" => "타겟ID 없음",
			"110" => "리턴URL 없음",
			"111" => "파라메터체크(본인인증수단)",
			"112" => "파라메터체크(주민번호형식)",
			"113" => "파라메터체크(휴대폰 통신사 정보)",
			"114" => "파라메터체크(휴대폰 번호 앞자리)",
			"115" => "파라메터체크(휴대폰 번호)",
			"116" => "파라메터체크(신용카드번호)",
			"117" => "파라메터체크(신용카드유효기간)",
			"118" => "파라메터체크(신용카드비밀번호)",
			"119" => "공인인증서 없음",
			"120" => "인증번호 재전송 건수 초과",
			"121" => "서비스오류",
			"122" => "DB 오류",
			"123" => "본인인증 취소",
			"124" => "회원사 허용대역 오류",
			"125" => "인증번호 문자길이 오류 ( 80byte 초과시 )",
			"126" => "실행모듈 파일의 권한이 없는 경우",
			"-1" => "실행모듈 파일의 권한이 없는 경우(Windows Server)",
			"127" => "실행모듈 파일이 없거나 패스가 잘못지정된 오류",
			"129" => "인증번호입력시간초과",
			"130" => "인증번호 오류입력건수 초과",
			"131" => "인증번호 불일치",
			"132" => "해당건 미존재",
			"133" => "잘못된 접근 (페이지 리로딩 포함)",
			"135" => "등록되지않은 서비스구분",
			"136" => "인증번호재전송 요청시간이 초과되었습니다.",
			"137" => "SMS발송에 실패했습니다.",
			"138" => "잘못된 DI 정보가 수신되었습니다.",
			"139" => "잘못된 CI 정보가 수신되었습니다.",
			"140" => "CI 검증 실패",
			"141" => "파라메터체크(성명)",
			"142" => "파라메터체크(생년월일)",
			"143" => "파라메터체크(성별)",
			"144" => "파라메터체크(내외국인구분)",
			"145" => "파라메터체크(개인정보동의여부)",
			"146" => "파라메터체크(식별정보동의여부)",
			"147" => "파라메터체크(통신약관동의여부)",
			"148" => "파라메터체크(SMS/LMS구분)",
			"149" => "파라메터체크(SMS 인증번호)",
			"150" => "파라메터체크(원거래 주민번호 상이)",
			"151" => "파라메터체크(원거래 휴대폰번호 상이)",
			"152" => "허용되지 않는 정책의 공인인증서",
			"153" => "공인인증서 유효성 검사 실패",
			"154" => "휴대폰인증보호서비스앱 미설치",
			"155" => "차단회원사",
			"156" => "SMS발송차단회원사",
			"201" => "웹서비스초기화실패",
			"-55" => "웹서비스초기화실패 (WindowsServer)",
			"202" => "서버로부터 키 수신 실패",
			"-54" => "서버로부터 키 수신 실패(WindowsServer)",
			"203" => "클라이언트 키 생성 실패",
			"204" => "암호화/복호화 실패",
			"205" => "실명 인증 서비스 호출 실패",
			"206" => "요청 인터페이스 오류, 실행모듈 파라미터 개수 또는 타입 오류",
			"207" => "EndPointURL 에러",
			"208" => "EndPointURL는 정상이나 입력 파라메터의 포맷오류(순서나 암호화 등)",
			"209" => "프로퍼티 파일 로드 실패",
			"210" => "프로퍼티 파일 저장 실패",
			"211" => "private_key 길이 오류(32바이트)",
			"212" => "public_key 길이 오류(28바이트)",
			"213" => "signature 길이 오류(24바이트)",
			"214" => "필수 파라미터 누락"
			);
			
			return "KCB 인증모듈 호출 에러 : ".$code."<br />".$error_list[$code];
		}
    }
?>
