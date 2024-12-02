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
 * Javascript controller for controlling the sections.
 *
 * @module     theme_klassplace/flex_section_control
 * @package    theme_klassplace
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/config', 'core/log'], function($, config, log) {

    /**
     * SectionControl class.
     *
     * @param {String} selector The selector for the page region containing the actions panel.
     */
    var flexsection_control = {

        courseid: 0,

        init: function(attribs) {

            this.courseid = attribs;

            // Attach togglestate handler to all flexsections in page.
            $('.flexcontrol').on('click', this.togglestate);
            $('.section-caption').on('keydown', this.togglekeyreceiver);

            // Attach global processings.
            $('.flexsection-global-control').on('click', this.processglobal);

            $('.flexsections > .section > .content > .sectionname').on('click', this.proxysectionnameevent);
            log.debug('AMD Flex sections control initialized');
        },

        // Init_editing will NOT take control of section name.
        init_editing: function(attribs) {

            this.courseid = attribs;

            // Expand everything.
            $('.section.sub > .content > .section-content').css('display', 'block');
            $('.section.sub > .content > .section-content').css('visibility', 'visible');
            $('.section.sub > .content > .summary').css('display', 'block');
            $('.section.sub > .content > .summary').css('visibility', 'visible');
            $('.section.sub >.content > .flexsections').css('display', 'block');
            $('.section.sub >.content > .flexsections').css('visibility', 'visible');
            // $('.flexcontrol > img').attr('src', $('.flexcontrol > img').attr('src').replace('collapsed', 'expanded'));

            // If collapse mode enabled in editing
            // Attach togglestate handler to all flexsections in page.
            $('.flexcontrol').on('click', this.togglestate);

            // Attach global processings.
            $('.flexsection-global-control').on('click', this.processglobal);

            // Dim toggle buttons. (if collapse mode disabled in edting.

            log.debug('AMD Flex sections control initialized v1.3');
        },

        proxysectionnameevent: function(e) {

            e.stopPropagation();
            e.preventDefault();
            var handle = $(this).find('img');
            handle.trigger('click');

        },

        togglestate: function(e, hide) {

            e.stopPropagation();
            e.preventDefault();
            var that = $(this);

            if (that.hasClass('sectioname')) {
                var regex = /sectioname-([0-9]+)-section-([0-9]+)/;
            } else {
                var regex = /control-([0-9]+)-section-([0-9]+)/;
            }
            var matchs = regex.exec(that.attr('id'));
            var sectionid = parseInt(matchs[1]);
            var sectionsection = parseInt(matchs[2]);
            regex = /level-([0-9]+)/;
            matchs = regex.exec(that.attr('class'));
            var level = parseInt(matchs[1]);

            log.debug('Working for flex section ' + sectionsection + ' of id ' + sectionid);

            var url = config.wwwroot + '/theme/klassplace/sections/ajax/register.php?';
            url += 'sectionid=' + sectionid;
            // var handlesrc = $('#control-' + sectionid + '-section-' + sectionsection + ' > img').attr('src');

            if (!hide) {
                var parentid = that.closest('li').parent().closest('li').attr('id');
                // Trigger hide event on all siblings.
                $.each($('#' + parentid + ' .flexcontrol.level-' + level), function(index, value) {
                    if ($(value).attr('id') != that.attr('id')) {
                        $(value).trigger('click', true);
                    }
                });
            }

            if (($('#section-' + sectionsection).hasClass('expanded')) ||
                        (hide === true)) {
                $('#section-' + sectionsection).addClass('collapsed');
                $('#section-' + sectionsection).removeClass('expanded');
                log.debug('Changing section #section-title-' + sectionsection + ' to false');
                $('#section-title-' + sectionsection).attr('aria-expanded', 'false');
                $('#section-' + sectionsection + ' > .content > .section-content').addClass('collpased');
                $('#section-' + sectionsection + ' > .content > .section-content').removeClass('expanded');
                // handlesrc = handlesrc.replace('expanded', 'collapsed');
                // $('#control-' + sectionid + '-section-' + sectionsection + ' > img ').attr('src', handlesrc);
                hide = 1;
            } else {
                // Show section.
                $('#section-' + sectionsection).addClass('expanded');
                $('#section-' + sectionsection).removeClass('collapsed');
                log.debug('Changing section #section-title-' + sectionsection + ' to true');
                $('#section-title-' + sectionsection).attr('aria-expanded', 'true');
                $('#section-' + sectionsection + ' > .content > .section-content').addClass('expanded');
                $('#section-' + sectionsection + ' > .content > .section-content').removeClass('collapsed');
                // handlesrc = handlesrc.replace('collapsed', 'expanded');
                // $('#control-' + sectionid + '-section-' + sectionsection + ' > img ').attr('src', handlesrc);
                hide = 0;

                // Scroll to this section.
                var offset = that.offset();
                offset.top -= 70;
                $('html, body').animate({
                    scrollTop: offset.top,
                    scrollLeft: 0
                });
            }

            url += '&hide=' + hide;

            $.get(url, function() {
            });

            return false;
        },

        processglobal: function(e) {
            e.stopPropagation();
            e.preventDefault();
            var that = $(this);

            var regex = /flexsections-control-([a-z]+)/;
            var matchs = regex.exec(that.attr('id'));
            var what = matchs[1];

            var url = config.wwwroot + '/theme/klassplace/sections/ajax/register.php?';
            url += 'id=' + flexsection_control.courseid;
            url += '&what=' + what;

            switch (what) {
                case 'collapseall':
                    $('.section.sub').removeClass('expanded');
                    $('.section.sub').addClass('collapsed');
                    $('.section-content').addClass('collpased');
                    $('.section-content').removeClass('expanded');
                    $('.section-title').attr('aria-expanded', 'false');
                    // $('.flexcontrol > img').attr('src', $('.flexcontrol > img').attr('src').replace('expanded', 'collapsed'));
                    break;

                case 'expandall':
                    $('.section').removeClass('collapsed');
                    $('.section').addClass('expanded');
                    $('.section-content').addClass('expanded');
                    $('.section-content').removeClass('collapsed');
                    $('.section-title').attr('aria-expanded', 'true');
                    // $('.flexcontrol > img').attr('src',$('.flexcontrol > img').attr('src').replace('collapsed', 'expanded'));
                    break;

                case 'map':
                    $('.section').removeClass('collapsed');
                    $('.section').addClass('expanded');
                    $('.section-content').removeClass('expanded');
                    $('.section-content').addClass('collapsed');
                    $('.section-title').attr('aria-expanded', 'false');
                    // Close leaves.
                    $('.section.isleaf').addClass('expanded');
                    $('.section.isleaf').removeClass('collapsed');
                    $('.section.isleaf .section-title').attr('aria-expanded', 'true');
            }

            // Update positions server side.
            $.get(url);
        },

        togglekeyreceiver: function(e) {
            var that = $(this);
            // Catch [enter] and [space]
            if (e.keyCode == 13 || e.keyCode == 32) {
                var sectionli = that.closest('li');
                var sectionid = sectionli.attr('id').replace('section-', '');
                var flexcontrol = that.find('#section-title-' + sectionid + ' > div.flexcontrol');
                var toggleproxy = $.proxy(flexsection_control.togglestate, flexcontrol);
                toggleproxy(e);
            }
        }
    };

    return flexsection_control;

});
