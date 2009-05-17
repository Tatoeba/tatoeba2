$(document).ready(function(){
	//load_followers();
	//load_following();
});

function load_followers(){
	var user_id = $(".user").attr("id");
	$(".followers").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".followers").load("http://" + self.location.hostname + "/users/followers/" + user_id);
}

function load_following(){
	var user_id = $(".user").attr("id");
	$(".following").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");
	$(".following").load("http://" + self.location.hostname + "/users/following/" + user_id);
}