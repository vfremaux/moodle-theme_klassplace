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
 * Contain the logic for a menu shaped lang menu.
 *
 * @package    theme_fordson_fel
 * @copyright  2016 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log'], function($, log) {

    /**
     */
    var langmenu = {

        init: function() {
            $('#lang-menu-handle').bind('click', this.toggle);
            log.debug("AMD langmenu init");
        },

        toggle: function() {
            var isshowing = $('#cm_submenu_lang').css('display');
            log.debug("Lang menu is showing " + isshowing);
            if (isshowing === 'none') {
                log.debug("hide menu ");
                $('#cm_submenu_lang').css('display', 'flex');
                $('#lang-menu-handle').attr('aria-expanded', 'true');
            } else {
                log.debug("show menu ");
                $('#cm_submenu_lang').css('display', 'none');
                $('#lang-menu-handle').attr('aria-expanded', 'false');
            }
        }

    };

    return langmenu;
});
