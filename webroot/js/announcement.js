/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
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

    $('.announcement').each(function() {
        var announcementId = $(this).attr('data-announcement-id');
        if ($.cookie(announcementId) === undefined) {
            $(this).show();
        }
    });

    $('.announcement .close').click(function() {
        var announcementId = $(this).parent().attr('data-announcement-id');
        $(this).parent().hide();
        $.cookie(announcementId, 'done', {'expires' : 7, 'path' : '/'});
    });

});