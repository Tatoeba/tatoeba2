/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009-2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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


$(document).ready(function() {

    $("#isPublicCheckbox").change(function(){
        var isPublic;
        var listId = $(this).data('listId');
        var rootUrl = get_tatoeba_root_url();
        
        if($(this).is(':checked')){
            isPublic = 1;
        }else{
            isPublic = 0;
        }
        
        $("#inProcess").show();
        
        $.post(
            rootUrl + "/sentences_lists/set_as_public/",
            { "listId": listId, "isPublic": isPublic },
            function(data){
                $("#inProcess").hide();
            }
        );
    });

});
