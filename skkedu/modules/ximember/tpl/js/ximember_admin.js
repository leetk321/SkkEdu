
jQuery(document).ready(function($){
	$("#use_signup_limit_Y").click(function(){
		$("#use_signup_limit_setting").show();
	});
	$("#use_signup_limit_N").click(function(){
		$("#use_signup_limit_setting").hide();
	});
});

function changeTarget(obj){
	var item_name = jQuery(obj).attr('item_name');
	var target = jQuery(obj).val();
	if(target == 'sex' || target == 'foreign' || target == 'auth_type'){
		var vaA = xi_variables_typeA[target];
		var vaB = xi_variables_typeB[target];
		jQuery('#typeA_'+item_name).html(vaA);
		jQuery('#typeB_'+item_name).html(vaB);
		jQuery('#val_type_wrap_'+item_name).show();
	}else{
		jQuery('#typeA_'+item_name).html("");
		jQuery('#typeB_'+item_name).html("");
		jQuery('#val_type_wrap_'+item_name).hide();
	}
}