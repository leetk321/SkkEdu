// 로그삭제
function doDeleteLogs() {
    var fo_obj = xGetElementById("log_fo");
    var data_srl = new Array();

    if(typeof(fo_obj.cart.length)=='undefined') {
        if(fo_obj.cart.checked) data_srl[data_srl.length] = fo_obj.cart.value;
    } else {
        var length = fo_obj.cart.length;
        for(var i=0;i<length;i++) {
            if(fo_obj.cart[i].checked) data_srl[data_srl.length] = fo_obj.cart[i].value;
        }
    }

    if(data_srl.length<1) { alert(null_message); return; }
    if(!confirm(delete_message)) return;

    var params = new Array();
    params['data_srls'] = data_srl.join('|');
    exec_xml('lottery','procLotteryAdminLogDelete', params, completeDeletelog);
}

// 기록초기화
function doResetLogs() {
    if(!confirm(reset_message)) return;
    exec_xml('lottery','procLotteryAdminLogReset', {}, completeDeletelog);
}

/* 일괄 삭제 후 */
function completeDeletelog(ret_obj) {
    alert(ret_obj['message']);
    location.reload();
}
