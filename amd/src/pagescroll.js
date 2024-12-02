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
 * @package    theme_klassplace
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/log'], function($, log) {

    var pagescroll = {

        scrollTrigger: 100,

        init: function() {

            if ($('#go-to-bottom').length) {

                $(window).bind('scroll', this.switchbuttons);

                $('#go-to-bottom').bind('click', this.gotobottom);
                $('#back-to-top').bind('click', this.backtotop);
                log.debug("AMD Theme Klassplace page scroll initialized");
            }
        },

        switchbuttons: function() {
            var scrollTop = $(window).scrollTop();
            if (scrollTop > pagescroll.scrollTrigger) {
                $('#go-to-bottom').removeClass('show');
                $('.hide-on-scroll').removeClass('show');
                $('#back-to-top').addClass('show');
                $('.show-on-scroll').addClass('show');
            } else {
                $('#go-to-bottom').addClass('show');
                $('.hide-on-scroll').addClass('show');
                $('#back-to-top').removeClass('show');
                $('.show-on-scroll').removeClass('show');
            }
        },

        gotobottom: function(e) {
            e.preventDefault();
            var fullheight = $('#page-wrapper').height();
            $('html,body').animate({
                scrollTop: fullheight
            }, 700);
        },

        backtotop: function (e) {
            e.preventDefault();
            $('html,body').animate({
                scrollTop: 0
            }, 700);
        }
    };

    return pagescroll;
});
