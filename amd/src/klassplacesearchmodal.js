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
 * Search box.
 *
 * @module     core/search-input
 * @class      search-input
 * @package    core
 * @copyright  2016 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.1
 */
define(['jquery', 'core/log'], function($, log) {

    var klassplacesearchmodal = {

        init: function() {
            var btn = $("#searchbutton");
            var span = $(".search-close");

            btn.on('click', this.openmodal);
            span.on('click', this.closemodal);

            window.onclick = function(event) {
                if (event.target == $('#searchmodal')) {
                    $('#searchmodal').css("display", "none");
                }
            }

            log.debug("AMD klassplacesearchmodal init");
        },

        openmodal: function() {
            $('#searchmodal').css("display", "block");
        },

        closemodal: function() {
            $('#searchmodal').css("display", "none");
        }
    }

     return klassplacesearchmodal;
});