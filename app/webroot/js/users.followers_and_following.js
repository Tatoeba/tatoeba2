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


function load_followers(){
    var user_id = $(".user").attr("id").slice(1);
    var rootUrl = get_tatoeba_root_url();
    
    $(".followers").load(rootUrl + "/followers/followers/" + user_id + "/true");
    $(".following").load(rootUrl + "/followers/following/" + user_id + "/true");
}

$(document).ready(function(){
    $(".followingOption").click(function(){
        var user_id = $(".user").attr("id").slice(1); // because id starts with an underscore to be compliant to standards
        var action = $(this).attr("id"); // "start" or "stop"
        var rootUrl = get_tatoeba_root_url();
        var url = rootUrl + "/followers/" + action + "_following";
        
        $(".in_process").html("<div class='loading'><img src='/img/loading.gif' alt='loading'></div>");

        $.post(
            url,
            { "user_id": user_id },
            function(data){
                $(".followingOption").toggle();
                $(".in_process").html("");
                load_followers();
            }
        );
    });

    load_followers();
});
