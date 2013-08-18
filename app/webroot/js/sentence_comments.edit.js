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


/* Javascript for managing AJAX editing of comments
 * TODO: How do you sanatize text input in jQuery?
 * TODO: Save function
 */
$(document).ready(function() {
    
    $(".editLink").attr("class", "editLinkShow");
    
    var rootUrl = get_tatoeba_root_url();
    
    function saveEdit(commentId){
        alert("commentId = "+commentId);
    }
    
    function manageEditForm(editLink) {
        var commentId =  editLink.attr("id");
        //textContainer is either a div or a textarea
        var textContainer = $("#commentText_"+commentId);
        
        //use html attribute "tatoeba:open" to track open status
        //use html attribute "tatoeba:textValue" to save text
        var isOpen = null;
        if (textContainer.attr("tatoeba:open") == undefined) {
            //*TODO* Sanatize
            textContainer.attr("tatoeba:textValue", textContainer.text().trim());
            textContainer.attr("tatoeba:open", 0);
            isOpen = 0;
        } else {
            isOpen = textContainer.attr("tatoeba:open");
        }
        
        if (isOpen == 1) {
            changeEditIcon(editLink, 0);
            destroyEditForm(textContainer, commentId);
        } else {
            //*TODO* Sanatize
            var editForm = createEditForm(editLink, textContainer, commentId);
            changeEditIcon(editLink, 1);
            textContainer.replaceWith(editForm);
            editForm.slideDown(400);
        }
        
    }
    
    function changeEditIcon(editLink, openStatus) {
        if (openStatus == 1) { 
            editLink.attr("src", "../../img/delete.png");
        } else {
            editLink.attr("src", "../../img/edit.png");
        }
    }
    
    function createEditForm(editLink, textContainer, commentId) {
        //textContainer is a div with text
        var textValue = textContainer.attr("tatoeba:textValue");
        var editForm = document.createElement("textarea");
        var saveButton = document.createElement("button");
        var cancelButton = document.createElement("a");
        cancelButton.className = "editCommentCancelLink";
        $(cancelButton).click(function() {
           manageEditForm(editLink);
        });
        $(saveButton).click(function() {
           saveEdit(commentId);
        });
        saveButton.className = "editCommentSaveButton";
        editForm.className = "editCommentTextArea"
        editForm.id = "commentText_"+commentId;
        editForm.rows = 6;
        editForm.cols = 62;
        editForm = $(editForm);
        editForm.text(textValue);
        editForm.attr("tatoeba:open", 1);
        editForm.attr("tatoeba:textValue", textValue)
        textContainer.after($(cancelButton).text("cancel"));
        textContainer.after($(saveButton).text("save"));
        return editForm;
    }
    
    function destroyEditForm(textContainer,commentId) {
        //textContainer is a textarea with two buttons for siblings
        //so destroy buttons
        textContainer.nextAll().remove();
        //create div with text to replace textarea
        var textDiv = document.createElement("div");
        textDiv.id = "commentText_"+commentId;
        textDiv.className = "commentText";
        textDiv = $(textDiv);
        var textValue = textContainer.attr("tatoeba:textValue").trim();
        textDiv.attr("tatoeba:open", 0);
        textDiv.attr("tatoeba:textValue", textValue);
        textDiv.text(textValue);
        textContainer.replaceWith(textDiv);
    }
    
    $(".editLinkShow").click(function() {
        manageEditForm( $(this) ) ;
    });
    
} );