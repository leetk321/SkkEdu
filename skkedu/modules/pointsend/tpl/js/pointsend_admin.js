/* 설정 저장 후 */
function completeInsertConfig(ret_obj, response_tags) {
	var error = ret_obj['error'];
	var message = ret_obj['message'];

	alert(message);

	var url = current_url.setQuery('act','dispPointsendAdminIndex');
	location.href = url;
}

/* 선물 되돌리기 후 */
function completeRevert() {
	var url = current_url.setQuery('act','dispPointsendAdminLogList');
	location.href = url;
}

function completeSend(ret_obj) {
	var message = ret_obj['message'];
	alert(message);
	location.reload();
}

function completeDeleteLog(ret_obj) {
	var message = ret_obj['message'];

	alert(message);

	var url = current_url.setQuery('act','dispPointsendAdminLogList', 'log_srl', '');
	location.href = url;
}