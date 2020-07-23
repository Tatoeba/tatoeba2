/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2010  HO Ngoc Phuong Trang <tranglich@gmail.com>
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

/* Polyfill for IE */
if (window.NodeList && !NodeList.prototype.forEach) {
   NodeList.prototype.forEach = Array.prototype.forEach;
}

var addAudio = function () {
    document.querySelectorAll('.audioAvailable').forEach(function(elem) {
       elem.addEventListener('click', function(event) {
           var audioURL = event.target.getAttribute('href');
           var audio = new Audio(audioURL);
           audio.play();
       });
    });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', addAudio);
} else {
  addAudio();
}
