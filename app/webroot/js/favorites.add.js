/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>
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


$(document).ready(function(){

    var rootUrl = get_tatoeba_root_url();
    
    // there's only one  "click" function for favorite/unfavorite
    // ( and a test inside to make the difference instead of two 
    // "click" functions because of the way JQuery works so as it
    // is done only one time at the beginning and as we change the
    // class of the block after the first loading jquery can't add
    // to it  "onclick"  so to get trough this we need to make a
    // single function and a test for each class

    $(".favorite").click(function(){
        var favoriteId = $(this).data("sentenceId");
        var favoriteOption = $(this);
        
        /*******************************
        / the sentence can be favorite 
        ********************************/

        if (favoriteOption.hasClass("add")){
            
            $("#_"+favoriteId+"_in_process").show();

            $.post(rootUrl + "/favorites/add_favorite/"+ favoriteId
                , {}
                ,function(data){
                    // if add retrieve no data , then for a reason or
                    // an other, the sentence couldn't have been added 
                    // so we change nothing
                    if ( null != data && "" != data ){
                        // this second test is here because with debug
                        // enable, the function always retrieve data
                        // so we test if the retrieving data is an <a> </a> 
                        if ( data[1] == "a" ){
                            
                            favoriteOption.html(data);
                            favoriteOption.removeClass("add").addClass("remove");
                            
                        }
                    }
        
                    $("#_"+favoriteId+"_in_process").hide();
                    
                }
            );
            

        }
    
        /*******************************
        / the sentence can be unfavorite 
        ********************************/

        else if (favoriteOption.hasClass("remove")){
            
            $("#_"+favoriteId+"_in_process").show();
            
            $.post(rootUrl + "/favorites/remove_favorite/"+ favoriteId
                , {}
                ,function(data){
                    // if add retrieve no data , then for a reason or an
                    // other, the sentence couldn't have been added                    
                    // so we change nothing
                    if ( null != data && "" != data ){
                        // this second test is here because with debug
                        // enable, the function always retrieve data
                        // so we test if the retrieving data is an <a> </a> 
                        if ( data[1] == "a" ){

                            favoriteOption.html(data);
                            favoriteOption.removeClass("remove").addClass("add");

                        }
                    }
                    $("#_"+favoriteId+"_in_process").hide();
                }
            );
            

        }
    });
});
