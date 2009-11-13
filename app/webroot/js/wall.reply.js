/*
    Tatoeba Project, free collaborativ creation of languages corpuses project
    Copyright (C) 2009 Allan SIMON <allan.simon@supinfo.com>

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

$(document).ready(function(){
    var replyFormInMessageID = -1;


    function saveMessage(){
        var messageContent = $("#replyFormDiv_" + replyFormInMessageID).find("textarea").val();
        
        $("#replyFormDiv_" + replyFormInMessageID ).remove()  ;
        
        $.post("http://" + self.location.hostname + "/wall/save_inside"
            , { "content" : messageContent , "replyTo" : replyFormInMessageID } 
            , function(data){
                $("#messageBody_" + replyFormInMessageID).append(data);
                replyFormInMessageID = -1;
            }
            , "html"
        );
        
       // alert (messageContent);
    }
    // without this all the element with ajaxSubmit class created after
    // the page initial loading wouldn't have been bound with the saveMessage
    // function, due to jquery mechanism
    $('.ajaxSubmit').live('click',
        function(){
            saveMessage() ;
            // this line is not in save message due to ajax's asynchronousity
            // that way we're sure replyFormInMessageId will not be set to -1
            // before the end of saveMessage
        }); 

    $(".replyLink").click(function(){
        var messageId = $(this).attr("class").split(' ')[1];
        //alert (messageId);
        var currentMessageBody = $('#messageBody_' + messageId );
        var messageText = currentMessageBody.html(); 
    

        // we remove the previous inside reply form 
        $("#replyFormDiv_" + replyFormInMessageID ).remove()  ;

        if ( messageId != replyFormInMessageID ){
            replyFormInMessageID = messageId ;
            // i know that's a bit "hacky" to retrieve the send message form
            // but that way we're sure to always have a coherent form, and 
            // we only need to change the helper
            var sendMessageForm = $('#WallSaveForm').clone();
            
            // change the form in order to make it unique 
            sendMessageForm.attr("id" , "replyForm_" + messageId ); 
            sendMessageForm.attr("method","");
            sendMessageForm.attr("action","");
            sendMessageForm.find('.submit').attr("class" , "ajaxSubmit"); 
            
            
            currentMessageBody.html(messageText 
                + "<div id=\"replyFormDiv_"+messageId+"\" class=\"replyFormDiv\" >"
                + sendMessageForm.html()
                +"</div>" ); 
        }

    });





});
