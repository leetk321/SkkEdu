
// 선택로그삭제
function jsDeleteLog() {
    var game_log = xGetElementById("game_log");
    var game_srl = new Array();

    if(typeof(game_log.cart.length)=='undefined') {
        if(game_log.cart.checked) game_srl[game_srl.length] = game_log.cart.value;
    } else {
        var length = game_log.cart.length;
        for(var i=0; i<length; i++) {
            if(game_log.cart[i].checked) game_srl[game_srl.length] = game_log.cart[i].value;
        }
    }
	
	//로그 선택하지 않았을때 오류메세지 출력
    if(game_srl.length < 1) { alert('선택 대상이 없습니다'); return; }
	//삭제 취소시 리턴
    if(!confirm('선택한 게임로그를 삭제합니다.')) return;

	//procRockgameAdminLogDelete에 넘겨줄 배열생성
    var params = new Array();
    params['game_srls'] = game_srl.join('@'); //값을 하나로 합침
    exec_xml('rockgame','procRockgameAdminLogDelete', params, completeDeletelog); //모듈이름//액션이름//보내줄값//콜백함수//콜백함수에서 받을변수(미입력시 message 기본내장)
}

// 전체로그삭제
function jsDeleteLogAll() {
    if(!confirm('전체로그를 삭제합니다.\n삭제후 데이터 복구는 불가능합니다.')) return;
    exec_xml('rockgame','procRockgameAdminLogDeleteAll', {}, completeDeletelog); //모듈이름//액션이름//보내줄값//콜백함수//콜백함수에서 받을변수(미입력시 message 기본내장)
}

/* 일괄 삭제 후 */
function completeDeletelog(ret_obj) {
    alert(ret_obj['message']);
    location.reload();
}