function addAuthPhoneNumber(phone_number_clue, phone_field)
{
	phone_number = new Array();
	phone_number[0] = phone_number_clue.substr(0,3);
	if(phone_number_clue.length == 11){
		phone_number[1] = phone_number_clue.substr(3,4);
		phone_number[2] = phone_number_clue.substr(7,11);
	}
	else if(phone_number_clue.length == 10){
		phone_number[1] = phone_number_clue.substr(3,6);
		phone_number[2] = phone_number_clue.substr(6,10);
	}
	var i=0;
	jQuery('input[name='+phone_field+'\\[\\]]').each(function(){
		jQuery(this).val(phone_number[i]);
		jQuery(this).attr('readonly', 'readonly');
		i++;
	});
}