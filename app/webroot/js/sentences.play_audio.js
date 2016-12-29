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


$(document).ready(function() {
    $(document).watch("addrule", function() {
        $('.audioAvailable').off();
        $('.audioAvailable').click(function() {
           var audioURL = $(this).attr('href');

           $('#audioPlayer').html(
                '<object data="'+ audioURL +'" type="audio/mpeg" data="'+ audioURL +'" width="0" height="0">'+
                    '<param name="src" value="'+ audioURL +'" />' +
                    '<object '+
                        'type="application/x-shockwave-flash" '+
                        'data="https://static.tatoeba.org/dewplayer-mini.swf?autostart=1&amp;mp3='+audioURL +'" '+
                        'width="0" '+
                        'height="0" '+
                    '>'+
                        '<param name="movie" value="https://static.tatoeba.org/dewplayer-mini.swf?autostart=1&amp;mp3='+audioURL +'" />'+
                    '</object>'+
                '</object>'
           );
        });
    });
});
