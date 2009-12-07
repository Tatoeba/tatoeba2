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

$(document).ready(function(){

// there's only one  "click" function for favorite/unfavorite ( and a test inside to make the difference
// instead of two "click" functions because of the way JQuery works
// so as it is done only one time at the beginning and as we change the class of the block after the first loading
//   jquery can't add to it "onclick" 
// 
// so to get trough this we need to make a single function and a test for each class

	$(".favorite").click(function(){
		var favorite_id = $(this).parent().attr("id").slice(1);
		var favorite_option = $(this);
		
		/*******************************
		/ the sentence can be favorite 
		********************************/

		if (favorite_option.hasClass("add")){
			
			$("#_"+favorite_id+"_in_process").show();

			$.post("http://" + self.location.hostname + "/favorites/add_favorite/"+ favorite_id
				, {}	
				,function(data){
					// if add retrieve no data , then for a reason or an other, the sentence couldn't have been added 
					// so we change nothing
					if ( null != data && "" != data ){
 						// this second test is here because with debug enable, the function always retrieve data
						// so we test if the retrieving data is an <a> </a> 
						if ( data[1] == "a" ){
							
							favorite_option.html(data);
							favorite_option.removeClass("add").addClass("remove");
							
						}
					}
		
					$("#_"+favorite_id+"_in_process").hide();
					
				}
			);
			

		}
	
		/*******************************
		/ the sentence can be unfavorite 
		********************************/

		else if (favorite_option.hasClass("remove")){
			
			$("#_"+favorite_id+"_in_process").show();
			
			$.post("http://" + self.location.hostname + "/favorites/remove_favorite/"+ favorite_id
				, {}
				,function(data){
					// if add retrieve no data , then for a reason or an other, the sentence couldn't have been added					
					// so we change nothing
					if ( null != data && "" != data ){
 						// this second test is here because with debug enable, the function always retrieve data
						// so we test if the retrieving data is an <a> </a> 
						if ( data[1] == "a" ){
							
							favorite_option.html(data);
							favorite_option.removeClass("remove").addClass("add");
							
						}

					}
		
					$("#_"+favorite_id+"_in_process").hide();
				}
			);
			

		}



	});



});
