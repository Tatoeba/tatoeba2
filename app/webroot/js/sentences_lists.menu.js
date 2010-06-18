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


$(document).ready(function() {
    
    var sentenceId = -1;
    
    /*
     * Display the "check" green icon for a short time
     * after sentence has been added to list.
     */
    function feedbackValid(){
        $("#sentence"+sentenceId+"_saved_in_list").show(); // TODO Set up a better system for this thing. 
        // Because you can't have the "in process" AND the "valid" at the same time.
        // It will be useful for favoriting as well.
        setTimeout(removeFeedback, 500);
    }
    
    /*
     * Remove the "check" green icon.
     */
    function removeFeedback(){
        $("#sentence"+sentenceId+"_saved_in_list").hide();
    }

    
    // Clicking on "Add to list" displays the list selection.
    // Reclicking on it hides the list selection.
    $(".addToList").click(function(){
        sentenceId = $(this).data("sentenceId");
        $("#addToList"+sentenceId).toggle();
    });
    
    
    // The sentence is added to the list after user has clicked
    // the button. I was hesitating between this solution and
    // having the sentence added to the list onChange, that is,
    // directly after the selection in the <select>.
    $(".validateButton").click(function(){
        
        var listId = $("#listSelection"+sentenceId).val();
        var rootUrl = get_tatoeba_root_url();
        
        // Add sentence to selected list
        if(listId > 0){
        
            $("#_"+sentenceId+"_in_process").show();
            
            $.post(
                rootUrl 
                + "/sentences_lists/add_sentence_to_list/"
                + sentenceId + "/" + listId
                , {}
                ,function(data){
                    if(!data.match('error')){
                        $('#listSelection'+sentenceId).val(-1);
                        $('#listSelection'+sentenceId+' option[value="'+data+'"]').remove();
                        feedbackValid(sentenceId);
                    }
                    $("#_"+sentenceId+"_in_process").hide();
                },
                'html'
            );
            
        }
        
        // Create a new list and add sentence to this new list
        else if(listId == -1){
            
            var txt = 'Name of the list : <br />'
            + '<input type="text" id="listName" name="listName" />';
            
            // callback for the popup
            function mycallbackform(value, message, form){
                
                if(value != undefined){ // need to check this, otherwise it loops indefinitely when canceling...
                
                    $("#_"+sentenceId+"_in_process").show();
                    
                    $.post(
                        rootUrl 
                        + "/sentences_lists/add_sentence_to_new_list/"
                        + sentenceId + "/"+ form.listName
                        , {}
                        ,function(data){
                            if(data != 'error'){
                                $('#listSelection'+sentenceId).append(
                                    '<option value="'+ data +'">'
                                    + form.listName 
                                    +'</option>'
                                );
                                $('#listSelection'+sentenceId).val(data);
                                feedbackValid(sentenceId);
                                
                            }else{
                                $.prompt("Sorry, an error occured.");
                            }
                            $("#_"+sentenceId+"_in_process").hide();
                        },
                        'html'
                    );
                    
                }
                
            }
            
            // popup to enter name of new list
            $.prompt(txt,{
                callback: mycallbackform,
                buttons: { Ok: 'OK'}
            });
            
        }
        
        // redirect to lists
        else if(listId == -2){
            
            $(location).attr(
                'href', 
                rootUrl 
                + "/sentences_lists/"
            );
            
        }
        
    });
});
