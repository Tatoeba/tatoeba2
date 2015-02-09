/*
    Tatoeba Project, free collaborative creation of multilingual corpuses project
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
function toggle_visibility(id) {
   if (document.getElementById('translation-show-'+id).style.display != 'none') {
     document.getElementById('translation-show-'+id).style.display = 'none';
     document.getElementById('translation-'+id).style.display = 'block';
  }
  else {
     document.getElementById('translation-show-'+id).style.display = 'inline';
     document.getElementById('translation-'+id).style.display = 'none';
  }
}
  
