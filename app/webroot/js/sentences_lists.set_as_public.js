/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang <tranglich@gmail.com>

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

$(document).ready(function() {

	$("#isPublic").change(function(){
		var is_public;
		var list_id = $(".sentencesListId").attr('id');
		
		if($(this).is(':checked')){	
			is_public = 1;
		}else{
			is_public = 0;
		}
		
		$("#inProcess").show();
		
		$.post(
			"http://" + self.location.hostname + ":" + self.location.port + "/sentences_lists/set_as_public/",
			{ "list_id": list_id, "is_public": is_public },
			function(){
				$("#inProcess").hide();
			}
		);
	});

});
