// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Contain the logic for a drawer.
 *
 * @copyright  2021 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/config', 'core/log'],function($, cfg, log) {

    var theme_usertours = {

        init: function() {
            $('[data-action="tool_usertours/resetpagetour"]').bind('click', this.resetcurrenttours);
        },

        resetcurrenttours: function(e) {

            var that = $(this);

            e.preventDefault(true);

            var url = cfg.wwwroot + '/theme/klassplace/services/usertours.php';
            url += '?url=' + encodeURIComponent(that.attr('data-url'));

            $.get(url);
            window.scrollTo(0, 0);
            location.reload(true);

            log.debug("Reset page usertours ");
        }
    };

    return theme_usertours;

});
