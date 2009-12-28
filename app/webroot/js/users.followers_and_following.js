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

var followingAction = 'start'; // "start" or "stop"

$(document).ready(function(){
	$("#followingOption").click(function(){
		var user_id = $(".user").attr("id");
		var url = "http://" + self.location.hostname + "/users/" + followingAction + "_following";

		var label = $("#followingOption").html();


		$("#followingOption").html("<img src='/img/loading.gif' alt='loading'>");

		$.post(
			url,
			{ "user_id": user_id },
			function(data){
				//load_followers(user_id);
				if(followingAction == 'start'){
					followingAction = 'stop';
					$("#followingOption").html(label.replace(/start/i, 'Stop'));
				}else{
					followingAction = 'start';
					$("#followingOption").html(label.replace(/stop/i, 'Start'));
				}
			}
		);
	});
});

function load_followers(user_id){
	$("#followingOption").load("http://" + self.location.hostname + "/users/followers/" + user_id);
}
