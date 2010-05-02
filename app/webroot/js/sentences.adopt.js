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
        var sentenceId = $(this).data("sentenceId");
        var adoptOption = $(this);
        var lang = $("#_"+sentenceId).attr("lang");
        
        /*******************************
        / the sentence can be adopted
        ********************************/

        if (adoptOption.hasClass("add")){
            
            $("#_"+sentenceId+"_in_process").show();

            $.post(
                "http://" + host + ":" + port + "/sentences/adopt/"+  sentenceId
                , {}    
                ,function(data){
                    // if add retrieve no data , then for a reason or an other,
                    // the sentence couldn't have been added so we change nothing
                    if ( null != data && "" != data ){
                        // this second test is here because with debug enable,
                        // the function always retrieve data
                        // so we test if the retrieving data is an <a> </a> 
                        if ( data[1] == "a" ){
                            
                            adoptOption.html(data);
                            adoptOption.removeClass("add").addClass("remove");
                            
                            $(".toggleOriginalSentence").toggle();
                            $("#"+lang+"_"+sentenceId).addClass("editable");
                            $("#"+lang+"_"+sentenceId).addClass("editableSentence");
                            
                            $('.editableSentence').editable(
                                'http://' + self.location.hostname + ":" 
                                + self.location.port + '/sentences/edit_sentence', { 
                                type      : 'text',
                                cancel    : 'Cancel',
                                submit    : 'OK',
                                indicator : '<img src="/img/loading.gif">',
                                tooltip   : 'Click to edit...',
                                cssclass  : 'editInPlaceForm'
                            });
                            
                        }
                    }
        
                    $("#_"+sentenceId+"_in_process").hide();
                    
                }
            );
            

        }
    
        /*******************************
        / the sentence can be unadopted 
        ********************************/

        else if (adoptOption.hasClass("remove")){
            $("#belongsTo_"+sentenceId).remove(); 
            $("#_"+sentenceId+"_in_process").show();
            
            $.post(
                "http://" + host + ":" + port + "/sentences/let_go/"+ sentenceId,
                {},
                function(data){
                    // if add retrieve no data , then for a reason or an other,
                    // the sentence couldn't have been added so we change nothing
                    if ( null != data && "" != data ){
                        // this second test is here because with debug enable,
                        // the function always retrieve data so we test if the
                        // retrieving data is an <a></a> 
                        if ( data[1] == "a" ){
                            adoptOption.html(data);
                            adoptOption.removeClass("remove").addClass("add");
                            
                            $(".toggleOriginalSentence").toggle();
                            $("#"+lang+"_"+sentenceId).removeClass("editable");
                            $("#"+lang+"_"+sentenceId).removeClass("editableSentence");
                            $("#"+lang+"_"+sentenceId).editable('disable');
                        }

                    }
                    $("#_"+sentenceId+"_in_process").hide();
                }
            );
        }

    });
});

