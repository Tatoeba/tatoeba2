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
	
    var host = self.location.hostname;
    var port = self.location.port;
	
    $(".languageFlag").live('click' ,function(){
		var flagImage = $(this);
        // to avoid duplicate and xhtml error the id is  _XXXX_original
        // TODO find a better way to store the id ...
		var sentenceId = $(this).parent().attr('id').split("_")[1];
        // TODO for the moment we retrive the previous language by
        // parsing the src attribute of the flag image ... I can be
        // hacky too someday ... need to find a generic way to store
        // information in a xhtml doc
		var prevLang = flagImage.attr('src').split('/')[3].split(".")[0];

		$("#selectLang_" + sentenceId).toggle();
		
		$("#selectLang_" + sentenceId).change(function(){
		
			var newLang = $(this).val();

			
			flagImage.attr('src', '/img/loading-small.gif');
			$("#selectLang_" + sentenceId).hide();
			
			$.post(
				"http://" + host + ":" + port + "/sentences/change_language/",
                { "id": sentenceId, "newLang": newLang, "prevLang": prevLang },
				function(){
					$("#_" + sentenceId + "_in_process").hide();
					flagImage.attr('src', '/img/flags/' + newLang + '.png');
				}
			);
		});
		
	});
	
});
