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
define(['jquery', 'core/log'], function($, log) {

    /**
     */
    var sectionstylechanger = {

        init: function() {
            $('.apply-section-style').bind('click', this.applystyle);
            log.debug("AMD Fordson Fel Section Styles initialized");
        },

        applystyle: function(e) {

            e.stopPropagation();
            e.preventDefault();

            var that = $(this);
            this.form.overridestyle.value = that.attr('data-value');
            this.form.submit();
        }
    };

    return sectionstylechanger;
});
