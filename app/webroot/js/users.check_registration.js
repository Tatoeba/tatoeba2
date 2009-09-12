function check(fieldName, string){
	var inputId = fieldName.charAt(0).toUpperCase() + fieldName.substr(1);
	
	$("#User"+inputId).removeClass("error").removeClass("valid").addClass("checking");
	$.post(
		"http://" + self.location.hostname + "/users/check_" + fieldName + "/" + string
		, {}
		, function(data){
			if(data == 'valid'){
				$("#User"+inputId).removeClass("checking").removeClass("error").addClass("valid");
			}else{
				$("#User"+inputId).removeClass("checking").removeClass("valid").addClass("error");
			}
		}
	);
}

function triggerChecking(fieldName, inputText){
	// we need to set some delay before checking in the database
	clearTimeout($.data(this, "timer"));
	var ms = 500;
	var wait = setTimeout(function() {
	  check(fieldName, inputText);
	}, ms);
	$.data(this, "timer", wait);
}


$(document).ready(function()
{
	
	/************************
	 *	Username validation
	 ************************/
	$("#UserUsername").keyup(function(e){
		var correctUsername = /[A-Za-z_]{2,20}/;
		
		if(e.keyCode > 40 || e.keyCode == 8 || e.keyCode == 13){ 	// so we don't take account keys like shift, home, end, etc... 
																	// But we accept backspace.
													
			var username = $(this).val();
			
			if(username.match(correctUsername)){

				triggerChecking('username', username);
				
			}else{
			
				$(this).removeClass("valid").addClass("error");
				
			}
			
		}
		
	});
	
	/************************
	 *	Password validation
	 ************************/
	$("#UserPassword").keyup(function(e){
		var correctPassword  = /(.){4,}/;
		
		if(e.keyCode > 40 || e.keyCode == 8 || e.keyCode == 13){ 	// so we don't take account keys like shift, home, end, etc... 
																	// But we accept backspace.
			var password = $(this).val();
			
			if(password.match(correctPassword)){
				
				$(this).removeClass("error").addClass("valid");
				
			}else{
				
				$(this).removeClass("valid").addClass("error");
				
			}
		}
		
	});
	
	
	/************************
	 *	Email validation
	 ************************/
	$("#UserEmail").keyup(function(e){
		var correctEmail  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
		
		if(e.keyCode > 40 || e.keyCode == 8 || e.keyCode == 13){ 	// so we don't take account keys like shift, home, end, etc... 
																	// But we accept backspace and enter
			
			var email = $(this).val();
			
			if(email.match(correctEmail)){
				
				triggerChecking('email', email);
				
			}else{
				
				$(this).removeClass("valid").addClass("error");
				
			}
			
		}
		
	});
  
});