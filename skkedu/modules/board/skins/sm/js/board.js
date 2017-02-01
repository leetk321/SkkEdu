
function toggle_object(post_id){   
    var obj = xGetElementById(post_id);   
    if(!obj) return;   
  
    if(obj.style.display=="none"){   
        obj.style.display='block';
        
    } else {   
        obj.style.display="none";  			
    }
	
}

function toggle(post_id){   
    var obj = xGetElementById(post_id);   
    if(!obj) return;   
  
    if(obj.style.display=="block"){   
        obj.style.display='none';
        
    } else {   
        obj.style.display="none";  			
    }
	
}

function completeDocumentInserted(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
	var category_srl = ret_obj['category_srl'];
    var page = ret_obj['page'];

    //alert(message);
	
	var url;
    if(!document_srl)
    {
        url = current_url.setQuery('mid',mid).setQuery('act','');
    }
    else
    {
        url = ("").setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    }
    if(category_srl) url = url.setQuery('category',category_srl);
    location.href = url;

}


function completeInsertComment(ret_obj) {
    var error = ret_obj['error'];
    var message = ret_obj['message'];
    var mid = ret_obj['mid'];
    var document_srl = ret_obj['document_srl'];
    var comment_srl = ret_obj['comment_srl'];

    var url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    if(comment_srl) url = url.setQuery('rnd',comment_srl)+"#comment_"+comment_srl;
	
	var modify = url.setQuery('rnd',comment_srl)+"#comment_"+comment_srl

    //alert(message);
	if(location.href == modify) url = current_url.setQuery('mid',mid).setQuery('document_srl',document_srl).setQuery('act','');
    location.href = url;
	
}