/***************************/
//@Author: Adrian "yEnS" Mato Gondelle & Ivan Guardado Castro
//@website: www.yensdesign.com
//@email: yensamg@gmail.com
//@license: Feel free to use it, but keep this credits please!					
/***************************/

$(document).ready(function(){
	//global vars
	var form = $("#login");
	var name = $("#courriel");
	var pass = $("#password");
	
	//On blur
	name.blur(validateEmail);
	pass.blur(validatePass);
	//On key press
	name.keyup(validateEmail);
	pass.keyup(validatePass);
	//On Submitting
	form.submit(function(){
		if(validateEmail() & validatePass())
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
	function validatePass(){
		var a = $("#password");

		//it's NOT valid
		if(pass.val().length <5){
			pass.addClass("error");
			return false;
		}
		//it's valid
		else{			
			pass.removeClass("error");
			return true;
		}
	}
});