
//약관세션생성
function setAgree(agree_srl,obj){
	var params = new Array();
	params["agree_srl"] = agree_srl;
	params["value"] = jQuery(obj).is(":checked") ? "Y" : "N";
	exec_xml("ximember","procXimemberSetAgree",params,false);
}
//약관세션체크
function checkAgree(next_func){
	var agreecount = jQuery(".agreements").size();
	var checkcount = 0;
	jQuery(".agreements").each(function(){
		if(jQuery(this).is(":checked")) checkcount ++;
	});
	if(checkcount == agreecount){
		var popupWindow = window.open( "", "auth_popup", "left=200, top=100, status=0, width=450, height=550" );
		return eval(next_func+"(popupWindow)");
	}else{
		alert("모든 필수 약관에 동의하셔야 가입하실 수 있습니다.");
	}
}

function jsSubmit(popupWindow){
	var okname_frm = document.okname_frm;
	if(!popupWindow){
		window.open("", "auth_popup", "width=432,height=560,scrollbar=yes");
	}
	window.name = "";
	document.auth_frm.action = "http://safe.ok-name.co.kr/CommonSvl";
	document.auth_frm.target = "auth_popup";
	document.auth_frm.method = "post";
	document.auth_frm.submit();
	
}
function certKCBIpin(popupWindow){
	if(!popupWindow){
		var popupWindow = window.open( "", "auth_popup", "left=200, top=100, status=0, width=450, height=550" );
	}
		document.kcbInForm.target = "auth_popup";
		document.kcbInForm.action = "https://ipin.ok-name.co.kr/tis/ti/POTI01A_LoginRP.jsp";
		document.kcbInForm.submit();
		popupWindow.focus();	
	return;	
}

/* 사용자 추가 */
function completeInsert(ret_obj, response_tags, args, fo_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var redirect_url = ret_obj['redirect_url'];

    alert(message);

    if(current_url.getQuery('popup')==1) {
        if(typeof(opener)!='undefined') opener.location.reload();
        window.close();
    } else {
        if(redirect_url) location.href = redirect_url;
        else location.href = current_url.setQuery('act','');
    }
}

/* 정보 수정 */
function completeModify(ret_obj, response_tags, args, fo_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);

    location.href = current_url.setQuery('act','dispMemberInfo');
}

/* 회원 탈퇴 */
function completeLeave(ret_obj, response_tags, args, fo_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);

    location.href = current_url.setQuery('act','');
}

/* 이미지 업로드 */
function _doUploadImage(fo_obj, act) {
    fo_obj.act.value = act;
    fo_obj.submit();
}

/* 프로필 이미지/ 이미지 이름/마크 등록 */
function doUploadProfileImage() {
    var fo_obj = get_by_id("fo_insert_member");
    if(!fo_obj.profile_image.value) return;
    _doUploadImage(fo_obj, 'procMemberInsertProfileImage');
}
function doUploadImageName() {
    var fo_obj = get_by_id("fo_insert_member");
    if(!fo_obj.image_name.value) return;
    _doUploadImage(fo_obj, 'procMemberInsertImageName');
}

function doUploadImageMark() {
    var fo_obj = get_by_id("fo_insert_member");
    if(!fo_obj.image_mark.value) return;
    _doUploadImage(fo_obj, 'procMemberInsertImageMark');
}


/* 로그인 후 */
function completeLogin(ret_obj, response_tags, params, fo_obj) {
    if(fo_obj.remember_user_id && fo_obj.remember_user_id.checked) {
        var expire = new Date();
        expire.setTime(expire.getTime()+ (7000 * 24 * 3600000));
        setCookie('user_id', fo_obj.user_id.value, expire);
    }

    var url =  current_url.setQuery('act','');
    location.href = current_url.setQuery('act','');
}

/* 로그아웃 후 */
function completeLogout(ret_obj) {
    location.href = current_url.setQuery('act','');
}

/* 인증 메일 재발송 후 */
function completeResendAuthMail(ret_obj, response_tags) {
	var error = ret_obj['error'];
    var message =  ret_obj['message'];

    if(message) alert(message);
	if(error != 0) alert(error);
}

/* 프로필 이미지, 이미지 이름, 마크 삭제 */
function doDeleteProfileImage(member_srl) {
	if (!member_srl) return;

	if (!confirm(xe.lang.deleteProfileImage)) return false;

	exec_xml(
		'member',
		'procMemberDeleteProfileImage',
		{member_srl:member_srl},
		function(){jQuery('#profile_imagetag').remove()},
		['error','message']
	);
}

function doDeleteImageName(member_srl) {
	if (!member_srl) return;

	if (!confirm(xe.lang.deleteImageName)) return false;
	exec_xml(
		'member',
		'procMemberDeleteImageName',
		{member_srl:member_srl},
		function(){jQuery('#image_nametag').remove()},
		['error','message']
	);
}

function doDeleteImageMark(member_srl) {
	if (!member_srl) return;

	if (!confirm(xe.lang.deleteImageMark)) return false;
	exec_xml(
		'member',
		'procMemberDeleteImageMark',
		{member_srl:member_srl},
		function(){jQuery('#image_marktag').remove()},
		['error','message']
	);
}

/* 스크랩 삭제 */
function doDeleteScrap(document_srl) {
    var params = new Array();
    params['document_srl'] = document_srl;
    exec_xml('member', 'procMemberDeleteScrap', params, function() { location.reload(); });
}

/* 비밀번호 찾기 후 */
function completeFindMemberAccount(ret_obj, response_tags) {
    alert(ret_obj['message']);
}

/* 임시 비밀번호 생성 */
function completeFindMemberAccountByQuestion(ret_obj, response_tags) {
    if(ret_obj['error'] != 0){
		alert(ret_obj['message']);
	}else{
		location.href = current_url.setQuery('act','dispMemberGetTempPassword').setQuery('user_id',ret_obj['user_id']);
	}
}

/* 저장글 삭제 */
function doDeleteSavedDocument(document_srl, confirm_message) {
    if(!confirm(confirm_message)) return false;

    var params = new Array();
    params['document_srl'] = document_srl;
    exec_xml('member', 'procMemberDeleteSavedDocument', params, function() { location.reload(); });
}

function insertSelectedModule(id, module_srl, mid, browser_title) {
    location.href = current_url.setQuery('selected_module_srl',module_srl);
}

