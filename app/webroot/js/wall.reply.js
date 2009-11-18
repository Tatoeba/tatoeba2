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
    var previousReplyFormInMessageID = -1;

    /*
    ** saveMessage 
    ** take the message written in the reply Form save it
    ** and then replace the form by a normal reply div
    */

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
    }

    /*
    ** manageReplyForm 
    ** handle both creation and suppression of div
    ** both imagine to create 2 functions but due to how
    ** jquery manage automatic binding, one function is less puzzling
    */
    function manageReplyForm(messageToReplyTo){
        var currentMessageId =  messageToReplyTo.attr("class").split(' ')[1];
        var aReplyFormAlreadyExist = false ;
        var hasClickOnReply = true;
        //alert (previousReplyFormInMessageID+" "+ currentMessageId );

        if ( previousReplyFormInMessageID != -1 ){
            aReplyFormAlreadyExist = true ;
        }
        if ( previousReplyFormInMessageID == currentMessageId){
            hasClickOnReply = false ;
        }


        if (aReplyFormAlreadyExist ){
             // replace "reply"  by "close"  
            $("#reply_"+previousReplyFormInMessageID).attr("class" ,"replyLink " + previousReplyFormInMessageID );
            $("#reply_"+previousReplyFormInMessageID).html("reply"); 
        // alert ("already exist") ; 
            // we remove the previous inside reply form 
            $("#replyFormDiv_" + previousReplyFormInMessageID ).remove();
            previousReplyFormInMessageID = -1 ;           
        }

        if ( hasClickOnReply) {
          //  alert ("has click on reply");
            var currentMessageBody = $('#messageBody_' + currentMessageId );


            // replace "reply"  by "close"  
            messageToReplyTo.attr("class" ,"closeLink " + currentMessageId );
            messageToReplyTo.html("close"); 
            // i know that's a bit "hacky" to retrieve the send message form
            // but that way we're sure to always have a coherent form, and 
            // we only need to change the helper
            var sendMessageForm = $('#WallSaveForm').clone();
            
            // change the form in order to make it unique 
            sendMessageForm.attr("id" , "replyForm_" + currentMessageId  ); 
            sendMessageForm.attr("method","");
            sendMessageForm.attr("action","");
            sendMessageForm.find('.submit').attr("class" , "ajaxSubmit"); 
            
            // note to myself
            // use append instead of  .html(previous + "") because the previous code will be unbound by jquery
            // as it will be considered as new code 
            currentMessageBody.append( 
                 "<div id=\"replyFormDiv_"+currentMessageId+"\" class=\"replyFormDiv\" >"
                + sendMessageForm.html()
                +"</div>" );
            
            previousReplyFormInMessageID = currentMessageId ; 
        }

    }


//    function manageReplyForm(messageToReplyTo){
//
//        var messageId = messageToReplyTo.attr("class").split(' ')[1];
//        /*  reply link case  */
//        if ( messageToReplyTo.hasClass("replyLink")){
//            
//            var currentMessageBody = $('#messageBody_' + messageId );
//            var messageText = currentMessageBody.html();
//           
//            // replace "reply"  by "close"  
//            messageToReplyTo.attr("class" ,"closeLink " + messageId );
//           messageToReplyTo.html("close"); 

//           // we remove the previous inside reply form 

//           $("#replyFormDiv_" + replyFormInMessageID ).remove()  ;
//           if ( messageId != replyFormInMessageID ){

//               replyFormInMessageID = messageId ;
//               // i know that's a bit "hacky" to retrieve the send message form
//               // but that way we're sure to always have a coherent form, and 
//               // we only need to change the helper
//               var sendMessageForm = $('#WallSaveForm').clone();
                
//               // change the form in order to make it unique 
//               sendMessageForm.attr("id" , "replyForm_" + messageId ); 
//               sendMessageForm.attr("method","");
//               sendMessageForm.attr("action","");
//               sendMessageForm.find('.submit').attr("class" , "ajaxSubmit"); 
                
                
//               currentMessageBody.html(messageText 
//                   + "<div id=\"replyFormDiv_"+messageId+"\" class=\"replyFormDiv\" >"
//                   + sendMessageForm.html()
//                   +"</div>" ); 
//           }
//       }
//       /****** 
//        a reply form is already opened for the same message
//        so a "close" link is displayed 
//        *******/
//        else if ( messageToReplyTo.hasClass("closeLink") ){
//           messageToReplyTo.attr("class" ,"replyLink " + messageId ) ;
//           messageToReplyTo.html("reply"); 
//           $("#replyFormDiv_" + replyFormInMessageID ).remove()  ;
//           replyFormInMessageId = -1 ;

//        }
//   }

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
        manageReplyForm( $(this) ) ;
    });





});
