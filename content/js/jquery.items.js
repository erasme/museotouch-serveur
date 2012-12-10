/***************************/
//@Author: Adrian "yEnS" Mato Gondelle & Ivan Guardado Castro
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

$(document).ready(function(){
	//global vars
	var form = $("#items");
	var iname = $("#item_nom");
	var idacq = $("#item_date_acqui");
	var idcre = $("#item_data_crea");
	var idate = $("#item_date");
	var icont = $("#item_conti");
	var icoun = $("#item_count");
	var itail = $("#item_taille");
	var icart = $("#item_cartel");
	
	//On blur
	iname.blur(ValidateText);
	idacq.blur(ValidateDate);
	idcre.blur(ValidateDate);
	idate.blur(ValidateText);
	icont.blur(ValidateNum);
	icoun.blur(ValidateNum);
	itail.blur(ValidateText);
	icart.blur(ValidateText);
	//On key press
	lname.keyup(validateLastName);
	fname.keyup(validateFirstName);
	pass1.keyup(validatePass1);
	pass2.keyup(validatePass2);
	//On Submitting
	form.submit(function(){
		if(validateLastName() & validateFirstName() & validateEmail() & validatePass1() & validatePass2())
			return true
		else
			return false;
	});
	
	//validation functions
	function validateEmail(){
		//testing regular expression
		var a = $("#courriel").val();
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,6}$/;
		//if it's valid email
		if(filter.test(a)){
			email.removeClass("error");
			return true;
		}
		//if it's NOT valid
		else{
			email.addClass("error");
			return false;
		}
	}
	function validateLastName(){
		//if it's NOT valid
		if(lname.val().length < 1){
			lname.addClass("error");
			return false;
		}
		//if it's valid
		else{
			lname.removeClass("error");
			return true;
		}
	}
	
});