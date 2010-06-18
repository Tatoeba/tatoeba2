/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// TODO This still needs some refactoring...
 
$(document).ready(function()
{
    /*
     * Check in database if the value already exists.
     */
    function check(fieldName, string){
        var inputId = fieldName.charAt(0).toUpperCase() + fieldName.substr(1);
        
        var rootUrl = get_tatoeba_root_url();
        $("#registration"+inputId).attr("class", "checking");
        
        $.post(
            rootUrl + "/users/check_" + fieldName + "/" + string
            , {}
            , function(data){
                if(data.match('valid')){
                    $("#registration"+inputId).attr("class", "valid");
                }else{
                    $("#registration"+inputId).attr("class", "error");
                }
            }
        );
    }
    
    /*
     * Timer, to add a delay before checking in database.
     */
    function triggerChecking(fieldName, inputText){
        clearTimeout($.data(this, "timer"));
        var ms = 500;
        var wait = setTimeout(
            function() {
                check(fieldName, inputText);
            }, 
            ms
        );
        $.data(this, "timer", wait);
    }

    /*
     * Username validation
     */
    $("#registrationUsername").keyup(function(e){
        var correctUsername = /[A-Za-z_]{2,20}/;
        // so we don't take account keys like shift, home, end, etc.
        // But we accept backspace... 
        if(e.keyCode > 40 || e.keyCode == 8 || e.keyCode == 13){     
                                                                    
                                                    
            var username = $(this).val();
            
            if(username.match(correctUsername)){

                triggerChecking('username', username);
                
            }else{
            
                $(this).attr("class", "error");
                
            }
            
        }
        
    });
    
    /*
     * Password validation
     */
    $("#registrationPassword").keyup(function(e){
        var correctPassword  = /(.){6,}/;
        // so we don't take account keys like shift, home, end, etc... 
        // But we accept backspace.
        if(e.keyCode > 40 || e.keyCode == 8 || e.keyCode == 13){     
                                                                    
            var password = $(this).val();
            
            if(password.match(correctPassword)){
                
                $(this).attr("class", "valid");
                
            }else{
                
                $(this).attr("class", "error");
                
            }
        }
        
    });
    
    /*
     * Email validation
     */
    $("#registrationEmail").keyup(function(e){
        var correctEmail  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        // so we don't take account keys like shift, home, end, etc... 
        // But we accept backspace and enter
        if(e.keyCode > 40 || e.keyCode == 8 || e.keyCode == 13){     
                                                                    
            
            var email = $(this).val();
            
            if(email.match(correctEmail)){
                
                triggerChecking('email', email);
                
            }else{
                
                $(this).attr("class", "error");
                
            }
            
        }
        
    });
    
    /*
     * Mask or unmask password.
     */
    $("#UserMaskPassword").change(function(){
        if($(this).is(':checked')){
            var currentPassword = $("#registrationPassword").val();
            $("#registrationPassword").hide();
            $("#unmaskedPasswordContainer").html(
                '<input type="text" value="'+currentPassword+'">'
            );
        }else{
            $("#unmaskedPasswordContainer").html("");
            $("#registrationPassword").show();
        }
    });
  
});
