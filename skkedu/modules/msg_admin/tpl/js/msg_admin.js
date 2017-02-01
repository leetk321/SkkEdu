/* 쪽지 보내기 */
function completeInsert(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var message_type = ret_obj['message_type'];
	var page = ret_obj['page'];

    alert(message);

    var url = current_url.setQuery('act','dispMsg_adminAdminList').setQuery('message_type',message_type).setQuery('message_srl','');
	if(page) url = url.setQuery('page', page);

    location.href = url;
}

/* 쪽지 수정 */
function completeModify(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var message_srl = ret_obj['message_srl'];
    var message_type = ret_obj['message_type'];
	var page = ret_obj['page'];

    alert(message);

    var url = current_url.setQuery('act','dispMsg_adminAdminList').setQuery('message_srl',message_srl);
    if(message_type) url = url.setQuery('message_type',message_type);
	if(page) url = url.setQuery('page', page);

    location.href = url;
}

/* 쪽지 삭제 */
function completeDelete(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var message_type = ret_obj['message_type'];
	var page = ret_obj['page'];

    alert(message);

    var url = current_url.setQuery('act','dispMsg_adminAdminList').setQuery('message_type',message_type);
    if(page) url = url.setQuery('page', page);

    location.href = url;
}

function dozone_receiver(i) {
    jQuery('.zone_receiver').css('display','none');
    if(i == -1) {
		jQuery('#zone_receiver1').css('display','block');
	} else if(i == -2) {
		jQuery('#zone_receiver2').css('display','block');
	}
}

function doEditReceiver(obj, cmd) {
    var list_obj = xGetElementById('receiver_value_list');
    var item_obj = xGetElementById('receiver_value_item');
    var idx = list_obj.selectedIndex;
    var lng = list_obj.options.length;
    var val = item_obj.value;
    switch(cmd) {
        case 'insert' :
                if(!val) return;
                var opt = new Option(val, val, false, true);
                list_obj.options[list_obj.length] = opt;
                item_obj.value = '';
                item_obj.focus();
            break;
        case 'up' :
                if(lng < 2 || idx<1) return;

                var value1 = list_obj.options[idx].value;
                var value2 = list_obj.options[idx-1].value;
                list_obj.options[idx] = new Option(value2,value2,false,false);
                list_obj.options[idx-1] = new Option(value1,value1,false,true);
            break;
        case 'down' :
                if(lng < 2 || idx == lng-1) return;

                var value1 = list_obj.options[idx].value;
                var value2 = list_obj.options[idx+1].value;
                list_obj.options[idx] = new Option(value2,value2,false,false);
                list_obj.options[idx+1] = new Option(value1,value1,false,true);
            break;
        case 'delete' :
                list_obj.remove(idx);
                if(idx==0) list_obj.selectedIndex = 0;
                else list_obj.selectedIndex = idx-1;
            break;
    }

    var value_list = new Array();
    for(var i=0;i<list_obj.options.length;i++) {
        value_list[value_list.length] = list_obj.options[i].value;
    }

    xGetElementById('fo_insert_form').receiver_value.value = value_list.join('|@|');
}