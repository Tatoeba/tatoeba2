$(document).ready(function(){
	$(".followingOption").click(function(){
		var user_id = $(".user").attr("id");
		var action = $(this).attr("id"); // "start" or "stop"
		var url = "http://" + self.location.hostname + "/users/" + action + "_following";
		
		$(".followers").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
		
		$.post(
			url,
			{ "user_id": user_id },
			function(data){
				load_followers(user_id);
				$(".followingOption").toggle();
			}
		);
	});
});

function load_followers(user_id){
	$(".followers").load("http://" + self.location.hostname + "/users/followers/" + user_id);
}