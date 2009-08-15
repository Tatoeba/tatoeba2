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
		var favorite_id = $(this).attr("id");

		/*******************************
		/ the sentence can be favorite 
		********************************/

		if ( $("#"+favorite_id).hasClass("favorite")){
			
			var previousHtml = $(".sentence").html();
			 $(".sentence").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>" );


			$.post("http://" + self.location.hostname + "/favorites/add_favorite/ "+ $(".translateLink").attr("id") 
				, {}	
				,function(data){
					// if add retrieve no data , then for a reason or an other, the sentence couldn't have been added 
					// so we change nothing
					if ( null != data && "" != data ){
 						// this second test is here because with debug enable, the function always retrieve data
						// so we test if the retrieving data is an <a> </a> 
						if ( data[1] == "a" ){

							$(".favorite").html(data);

							$("#"+favorite_id).removeClass("favorite").addClass("unfavorite");
						}

					}
		
					$(".sentence").html(previousHtml);
				}
			);
			

		}

		/*******************************
		/ the sentence can be unfavorite 
		********************************/

		if ( $("#"+favorite_id).hasClass("unfavorite")){
			
			var previousHtml = $(".sentence").html();
			 $(".sentence").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>" );


			$.post("http://" + self.location.hostname + "/favorites/remove_favorite/ "+ $(".translateLink").attr("id") 
				, {}	
				,function(data){
					// if add retrieve no data , then for a reason or an other, the sentence couldn't have been added					
					// so we change nothing
					if ( null != data && "" != data ){
 						// this second test is here because with debug enable, the function always retrieve data
						// so we test if the retrieving data is an <a> </a> 
						if ( data[1] == "a" ){

							$("#"+favorite_id).html(data);
							$("#"+favorite_id).removeClass("unfavorite").addClass("favorite");
						}

					}
		
					$(".sentence").html(previousHtml);
				}
			);
			

		}



	});



});
