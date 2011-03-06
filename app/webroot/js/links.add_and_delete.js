/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2011  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

    $(".link").click(function(){
        var sentenceId = $(this).data("sentenceId");
        var translationId = $(this).data("translationId");
        var rootUrl = get_tatoeba_root_url();
        var image = $(this);
        var action = null;
        
        if ($(this).hasClass("add")){
            var action = 'add';
            var newAction = 'delete';
            var removeClass = "indirectTranslation";
            var addClass = "directTranslation";
            var newType = "direct";
        } else if ($(this).hasClass("delete")){
            var action = 'delete';
            var newAction = 'add';
            var removeClass = "directTranslation";
            var addClass = "indirectTranslation";
            var newType = "indirect";
        }
        
        if (action != null) {
            // Show the loading gif...
            $(this).html(
                "<img src='/img/loading-small.gif' alt='loading'>"
            );
            
            // Send request...
            $.get(
                rootUrl + "/links/"+action+"/"+sentenceId+"/"+translationId,
                function(data) {
                    var elementId = "#translation_"+translationId+"_"+sentenceId;
                    
                    // Update the link or unlink image
                    image.html(data);
                    image.removeClass(action);
                    image.addClass(newAction);
                    
                    // update the class of the sentence and the arrow
                    $(elementId).removeClass(removeClass);
                    $(elementId).addClass(addClass);
                    $(elementId+" .show img").attr(
                        'src', '/img/'+newType+'_translation.png'
                    );
                }
            );
        }
        
    });
    
});