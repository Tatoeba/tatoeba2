$(document).ready(function(){

// there's only one  "click" function for favorite/unfavorite ( and a test inside to make the difference
// instead of two "click" functions because of the way JQuery works
// .ready  function before loading pages on the browser make a preprocessing stuff
//   it will check and relace in the html all the div matching one of the xpath expression, and add to it "onclick" 
// so as it is done only one time at the beginning and as we change the class of the block after the first loading
//   jquery can't add to it "onclick" 
// 
// so to get trough this we need to make a single function and a test for each class

	$(".favorite").click(function(){
		var favorite_id = $(this).parent().attr("id");
		var favorite_option = $(this);
		
		/*******************************
		/ the sentence can be favorite 
		********************************/

		if (favorite_option.hasClass("add")){
			
			$("#favorite_"+favorite_id+"_in_process").show();

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
		
					$("#favorite_"+favorite_id+"_in_process").hide();
					
				}
			);
			

		}
	
		/*******************************
		/ the sentence can be unfavorite 
		********************************/

		else if (favorite_option.hasClass("remove")){
			
			$("#favorite_"+favorite_id+"_in_process").show();
			
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
		
					$("#favorite_"+favorite_id+"_in_process").hide();
				}
			);
			

		}



	});



});
