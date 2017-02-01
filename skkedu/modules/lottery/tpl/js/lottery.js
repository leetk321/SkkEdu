// 복권 구입
function lotteryBuy() {
    if(!confirm(confirm_buy)) return;
    exec_xml('lottery','procLotteryBuy', {}, completeLotteryBuy,['error','message','data']);
}
// 당첨금 수령
function lotteryPointReceive() {
    exec_xml('lottery','procLotteryPointReceive', {}, completePointReceive,['error','message','reload']);
}

function completeLotteryBuy(ret_obj) {
    var message = ret_obj['message'];
    var data = ret_obj['data'];
    var down = false;
    var check = 0;
    var check2 = 0;

    (function($){
        $(function(){
            $("#number_bg").show();
            $("#number").text(data['text']);
            $("#number_bg ul li").mousedown(function() {
                down = true;
            });
            $("#number_bg ul li").mouseup(function() {
                down = false;
            });
            $("#number_bg li").mousemove(function() {
                if(down) {
                    var index = $("#number_bg li").index($(this));
                    $(this).fadeTo("normal",0.3);
                    if(check==1 && check2) { alert(data['message']); check = 2; }
                    if(index == 8 && !check) check = 1;
                    if(index == 9 && !check2) check2 = 1;
                }
                return false;
            });
        });
    })(jQuery);
}

function completePointReceive(ret_obj) {
    var message = ret_obj['message'];
    var reload = ret_obj['reload'];
    alert(message);
    if(reload) location.reload();
}