/***************************/
//@Author: Adrian "yEnS" Mato Gondelle & Ivan Guardado Castro
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

$(document).ready(function(){
	//global vars
	var form = $("#register");
	var email = $("#courriel");
	var lname = $("#lastname");
	var fname = $("#firstname");
	var pass1 = $("#password1");
	var pass2 = $("#password2");
	
	//On blur
	lname.blur(validateLastName);
	fname.blur(validateFirstName);
	email.blur(validateEmail);
	pass1.blur(validatePass1);
	pass2.blur(validatePass2);
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
	function validateFirstName(){
		//if it's NOT valid
		if(fname.val().length < 1){
			fname.addClass("error");
			return false;
		}
		//if it's valid
		else{
			fname.removeClass("error");
			return true;
		}
	}
	function validatePass1(){
		var a = $("#password1");
		var b = $("#password2");

		//it's NOT valid
		if(pass1.val().length <5){
			pass1.addClass("error");
			return false;
		}
		//it's valid
		else{			
			pass1.removeClass("error");
			validatePass2();
			return true;
		}
	}
	function validatePass2(){
		var a = $("#password1");
		var b = $("#password2");
		//are NOT valid
		if( pass1.val() != pass2.val() ){
			pass2.addClass("error");
			return false;
		}
		//are valid
		else{
			pass2.removeClass("error");
			return true;
		}
	}
});