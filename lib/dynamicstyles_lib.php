<?php
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
 * Dynamic styles.
 *
 * @package    theme_klassplace
 * @author     valery.fremaux <valery.fremaux@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Loads dynamic style sheets that will be never cached.
 */
function theme_klassplace_get_dynamic_css($theme) {
    global $PAGE, $USER;

    // Load the fonts urls.
    $defaultfamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol"';
    $titledefaultfamily = '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol"';
    $fonturl = '';
    $altfonturl = '';
    $titlefonturl = '';
    $altdefaultfamily = $defaultfamily;

    if (@$theme->settings->usecustomfonts) {
        $fonturl = $theme->setting_file_url('generalbodyfont', 'generalbodyfont');
        if ($fonturl) {
            $defaultfamily = 'mainfont';
        }

        $titledefaultfamily = $defaultfamily;
        $titlefonturl = $theme->setting_file_url('titlefont', 'generalbodyfont');
        if ($titlefonturl) {
            $titledefaultfamily = 'titlefont';
        }

        $altdefaultfamily = $defaultfamily;
        $altfonturl = $theme->setting_file_url('generalaltfont', 'generalbodyfont');
        if ($altfonturl) {
            $altdefaultfamily = 'altfont';
        }
    }

    if (!empty($theme->settings->usedyslexicfont)) {
        if (get_user_preferences('dyslexic_helper', false, $USER->id)) {
            $fonturl = $theme->setting_file_url('altdyslexicfont', 'dyslexicfont');
            if (empty($fonturl)) {
                $fonturl = new moodle_url('/theme/klassplace/fonts/opendyslexic3-regular-webfont.woff');
            }
            $defaultfamily = 'mainfont';
            $titledefaultfamily = 'mainfont';
        }
    }

    if (get_user_preferences('highcontrast_helper', false, $USER->id)) {
        $fonturl = $theme->setting_file_url('althighcontrastfont', 'highcontrastfont');
        if (empty($fonturl)) {
            $fonturl = new moodle_url('/theme/klassplace/fonts/Kanit-Medium.ttf');
        }
        $defaultfamily = 'mainfont';
        $titledefaultfamily = 'mainfont';
    }

    $csscontent = theme_klassplace_load_uncached_css();

    $csscontent = str_replace('[[settings:bodyfont]]', 'url('.$fonturl.')', $csscontent);
    $csscontent = str_replace('[[settings:altfont]]', 'url('.$altfonturl.')', $csscontent);
    $csscontent = str_replace('[[settings:fontfamily]]', $defaultfamily, $csscontent);
    $csscontent = str_replace('[[settings:titlefont]]', 'url('.$titlefonturl.')', $csscontent);
    $csscontent = str_replace('[[settings:titlefontfamily]]', $titledefaultfamily, $csscontent);

    // Add general alt font based on class selector
    $altfontcss = '';
    if (!empty($theme->settings->generalaltccsselector)) {
        $altfontcss = '
            '.$theme->settings->generalaltccsselector.' {
                font-family: '.$altdefaultfamily.' !important;
            }
        ';
    }
    $csscontent = str_replace('[[settings:specialaltfontcss]]', $altfontcss, $csscontent);

    return $csscontent;
}

function theme_klassplace_load_uncached_css($stylesheetsoptions = []) {
    global $PAGE, $CFG;

    $css = '';
    $stylespath = $CFG->dirroot.'/theme/klassplace/uncached_style/*.css';
    $styles = glob($stylespath);
    foreach ($styles as $stylefile) {
        if (preg_match('/fonts\.css/', $stylefile) && array_key_exists('nofonts', $stylesheetsoptions)) {
            continue;
        }
        $css .= implode('', file($stylefile));
    }

    return $css;
}