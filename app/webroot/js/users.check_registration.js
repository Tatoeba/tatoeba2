/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009  TATOEBA Project(should be changed)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.


*/

function check(fieldName, string){
	var inputId = fieldName.charAt(0).toUpperCase() + fieldName.substr(1);

	$("#registration"+inputId).removeClass("error").removeClass("valid").addClass("checking");
    
	$.post(
		"http://" + self.location.hostname + ":" + self.location.port + "/users/check_" + fieldName + "/" + string
		, {}
		, function(data){
			if(data.match('valid')){
				$("#registration"+inputId).removeClass("checking").removeClass("error").addClass("valid");
			}else{
				$("#registration"+inputId).removeClass("checking").removeClass("valid").addClass("error");
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
	$("#registrationUsername").keyup(function(e){
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
	$("#registrationPassword").keyup(function(e){
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
	$("#registrationEmail").keyup(function(e){
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
