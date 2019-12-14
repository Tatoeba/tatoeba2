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
    $(document).watch("addrule", function() {
        // Clicking on "Add to list" displays the list selection.
        // Reclicking on it hides the list selection.
        $(".addToList").off();
        $(".addToList").click(function(){
            let sentenceId = $(this).attr("data-sentence-id");
            $("#addToList"+sentenceId).toggle();
        });

        // The sentence is added to the list after user has clicked
        // the button. I was hesitating between this solution and
        // having the sentence added to the list onChange, that is,
        // directly after the selection in the <select>.
        $(".validateButton").off();
        $(".validateButton").click(function(){
            let sentenceId = this.dataset.sentenceId;
            let listId = $("#listSelection"+sentenceId).val();
            let rootUrl = get_tatoeba_root_url();
            let inProcess = $("#_" + sentenceId + "_in_process");
            let listSelection = $("#listSelection" + sentenceId);
            let feedback = $("#sentence" + sentenceId + "_saved_in_list"); 

            // Add sentence to selected list
            if(listId > 0){
                inProcess.show();

                $.get(
                    rootUrl
                    + "/sentences_lists/add_sentence_to_list/"
                    + sentenceId + "/" + listId
                    , {}
                    ,function(data){
                        inProcess.hide();
                        if(!data.match('error')){
                            listSelection.find('option[value="' + data + '"]').remove();
                            feedback.show(0).delay(500).hide(0);
                        }
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
});
