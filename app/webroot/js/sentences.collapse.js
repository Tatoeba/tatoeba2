/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project

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
    $(document).watch("addrule", function() {
        $(document).on("click", "div.showLink", function(){
            $(".more").hide();
            $("div.showLink").show();
            $(this).parents(".translations").find(".more").show();
            $(this).hide();
            $("div.hideLink").show();
        });
        $(document).on("click", "div.hideLink", function(){
            $(this).parents(".more").hide();
            $(this).hide();
            $("div.showLink").show();
        });
    });
});