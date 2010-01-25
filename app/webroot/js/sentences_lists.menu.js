/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
    Copyright (C) 2009  HO Ngoc Phuong Trang (tranglich@gmail.com)

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

$(document).ready(function() {
	
	var sentence_id = -1;
	
	/*
	 * Display the "check" green icon for a short time
	 * after sentence has been added to list.
	 */
	function feedbackValid(){
		$("#_"+sentence_id+"_valid").show(); // TODO Set up a better system for this thing. 
		// Because you can't have the "in process" AND the "valid" at the same time.
		// It will be useful for favoriting as well.
		setTimeout(removeFeedback, 500);
	}
	
	/*
	 * Remove the "check" green icon.
	 */
	function removeFeedback(){
		$("#_"+sentence_id+"_valid").hide();
	}

	
	// Clicking on "Add to list" displays the list selection.
	// Reclicking on it hides the list selection.
	$(".addToList").click(function(){
		sentence_id = $(this).parent().attr("id").slice(1);
		$(".addToList"+sentence_id).toggle();
	});
	
	
	// The sentence is added to the list after user has clicked
	// the button. I was hesitating between this solution and
	// having the sentence added to the list onChange, that is,
	// directly after the selection in the <select>.
	$(".addToListButton").click(function(){
		
		sentence_id = $(this).parent().parent().attr("id").slice(1);
		var list_id = $("#listSelection"+sentence_id).val();
		
		// Add sentence to selected list
		if(list_id > 0){
		
			$("#"+sentence_id+"_in_process").show();
			
			$.post("http://" + self.location.hostname + ":" + self.location.port + "/sentences_lists/add_sentence_to_list/"+ sentence_id + "/" + list_id
				, {}
				,function(data){
					if(data != 'error'){
						$('#listSelection'+sentence_id).val(-1);
						$('#listSelection'+sentence_id+' option[value="'+data+'"]').remove();
						feedbackValid(sentence_id);
					}
					$("#_"+sentence_id+"_in_process").hide();
				},
				'html'
			);
			
		}
		
		// Create a new list and add sentence to this new list
		else if(list_id == -1){
			
			var txt = 'Name of the list : <br />'
			+ '<input type="text" id="listName" name="listName" />';
			
			// callback for the popup
			function mycallbackform(value, message, form){
				
				if(value != undefined){ // need to check this, otherwise it loops indefinitely when canceling...
				
					$("#_"+sentence_id+"_in_process").show();
					
					$.post("http://" + self.location.hostname + ":" + self.location.port + "/sentences_lists/add_sentence_to_new_list/"+ sentence_id + "/"+ form.listName
						, {}
						,function(data){
							if(data != 'error'){
								$('#listSelection'+sentence_id).append('<option value="'+ data +'">'+ form.listName +'</option>');
								$('#listSelection'+sentence_id).val(data);
								feedbackValid(sentence_id);
								
							}else{
								$.prompt("Sorry, an error occured.");
							}
							$("#_"+sentence_id+"_in_process").hide();
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
		else if(list_id == -2){
			
			$(location).attr('href', "http://" + self.location.hostname + ":" + self.location.port + "/sentences_lists/");
			
		}
		
	});
});
