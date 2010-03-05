/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  Allan SIMON <allan.simon@supinfo.com>
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
 *
 */

$(document).ready(function(){

    var host = self.location.hostname;
    var port = self.location.port;

    $(".adopt").click(function(){
        var adopt_id = $(this).parent().attr("id").slice(1);
        var adopt_option = $(this);
        var lang = $("#_"+adopt_id).attr("lang");
        
        /*******************************
        / the sentence can be adopted
        ********************************/

        if (adopt_option.hasClass("add")){
            
            $("#_"+adopt_id+"_in_process").show();

            $.post(
                "http://" + host + ":" + port + "/sentences/adopt/"+  adopt_id
                , {}    
                ,function(data){
                    // if add retrieve no data , then for a reason or an other,
                    // the sentence couldn't have been added so we change nothing
                    if ( null != data && "" != data ){
                        // this second test is here because with debug enable,
                        // the function always retrieve data
                        // so we test if the retrieving data is an <a> </a> 
                        if ( data[1] == "a" ){
                            
                            adopt_option.html(data);
                            adopt_option.removeClass("add").addClass("remove");
                            
                            $(".toggleOriginalSentence").toggle();
                            $("#"+lang+"_"+adopt_id).addClass("editable");
                            $("#"+lang+"_"+adopt_id).addClass("editableSentence");
                            
                            $('.editableSentence').editable(
                                'http://' + self.location.hostname + ":" 
                                + self.location.port + '/sentences/save_sentence', { 
                                type      : 'text',
                                cancel    : 'Cancel',
                                submit    : 'OK',
                                indicator : '<img src="/img/loading.gif">',
                                tooltip   : 'Click to edit...',
                                cssclass  : 'editInPlaceForm'
                            });
                            
                        }
                    }
        
                    $("#_"+adopt_id+"_in_process").hide();
                    
                }
            );
            

        }
    
        /*******************************
        / the sentence can be unadopted 
        ********************************/

        else if (adopt_option.hasClass("remove")){
            $("#belongsTo_"+adopt_id).remove(); 
            $("#_"+adopt_id+"_in_process").show();
            
            $.post(
                "http://" + host + ":" + port + "/sentences/let_go/"+ adopt_id,
                {},
                function(data){
                    // if add retrieve no data , then for a reason or an other,
                    // the sentence couldn't have been added so we change nothing
                    if ( null != data && "" != data ){
                        // this second test is here because with debug enable,
                        // the function always retrieve data so we test if the
                        // retrieving data is an <a></a> 
                        if ( data[1] == "a" ){
                            adopt_option.html(data);
                            adopt_option.removeClass("remove").addClass("add");
                            
                            $(".toggleOriginalSentence").toggle();
                            $("#"+lang+"_"+adopt_id).removeClass("editable");
                            $("#"+lang+"_"+adopt_id).removeClass("editableSentence");
                            $("#"+lang+"_"+adopt_id).editable('disable');
                        }

                    }
                    $("#_"+adopt_id+"_in_process").hide();
                }
            );
        }

    });
});

