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

$(document).ready(function(){
    
    var lang = $("#randomLangChoice").val();
    if (lang == null) {
        lang = '';
    }
    
    $("#showRandom").click(function(){
        lang = $("#randomLangChoice").val();
        loadRandom(lang);
    })
});

function loadRandom(lang){
    $("#random-progress").show();
    $("#random_sentence_display").hide();
    
    $.ajax({
      type: "GET",
      url: "/sentences/random/" + lang,
      success: function (data){ 
          $("#random_sentence_display").watch("html", data);
          $("#random-progress").hide();
          $("#random_sentence_display").show();
          }
    });
}
