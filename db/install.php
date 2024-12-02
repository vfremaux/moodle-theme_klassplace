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
 * @package local_vmoodle
 * @category local
 * @author Bruce Bujon (bruce.bujon@gmail.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL
 */
defined('MOODLE_INTERNAL') || die();

/**
 * We must capture the old block_vmoodle table records and remove the old table
 *
 */
function xmldb_theme_klassplace_install() {
    global $CFG;

    // Ensure we have some defaults page widget structure configured.
    $samples['homepage_structure_uncon'] = [
        'slider',
        'announcement',
        'academics',
        'events',
        'customsection',
        'circles',
        'legacy',
        'clientlogos',
        '',
        '',
    ];

    $samples['homepage_structure_con'] = [
        'slider',
        'events',
        'customsection',
        'legacy',
        'clientlogos',
        '',
        '',
        '',
        '',
        '',
    ];

    $samples['homepage_structure_my'] = [
        'legacy',
        'clientlogos',
        'events',
        '',
        '',
    ];

    $samples['homepage_structure_incourse'] = [
        'legacy',
        'events',
        '',
        '',
        ''
    ];

    theme_klassplace_load_assets('theme_klassplace', $samples, []);

    unset($samples);

    // Register default slider slides.
    $samples['slidercaption'] = [
        'Campus Is An Online Platform',
        'Get all your courses available ever',
        'Learn, interact, become stronger',
        '',
        '',
        '',
    ];

    $samples['sliderheading'] = [
        'Designed to Make Learning',
        'All time, from everywhere',
        'Take power in the digital world',
        '',
        '',
        '',
    ];

    $samples['sliderimage'] = [
        'slider1.png',
        'slider2.png',
        'slider3.png',
        '',
        '',
        ''
    ];

    theme_klassplace_load_assets('theme_klassplace', $samples, ['sliderimage']);

    unset($samples);

    // Event samples
    $samples['eventtitle'] = [
        'Conférence',
        'Colloque',
        'Master Class',
        'Ateliers'
    ];

    $samples['eventdescription'] = [
        'Praesent tristique nulla sem, a varius dolor porta eu.',
        'Praesent rhoncus sapien neque, et dapibus magna sagittis in.',
        'Ut felis nisi, volutpat quis nulla at, tristique dignissim lacus.',
        'Nam egestas orci porta purus pulvinar, sed pharetra nibh convallis.'
    ];

    $samples['eventday'] = [
        '05',
        '30',
        '29',
        '25'
    ];

    $samples['eventmonthyear'] = [
        'Décembre 2020',
        'Septembre 2021',
        'Août 2021',
        'Janvier 2022'
    ];

    $samples['eventlocation'] = [
        'Montpellier, France',
        'Paris, France',
        'Toronto, USA',
        'Swindon, UK'
    ];

    $samples['eventimage'] = [
        'event1.jpg',
        'event2.jpg',
        'event3.jpg',
        'event4.jpg'
    ];

    theme_klassplace_load_assets('theme_klassplace', $samples, ['eventimage']);

    unset($samples);

    // Installing some sample logos.
    $samples['clientlogo'] = [
        'logo1.png',
        'logo2.png',
        'logo3.png',
        'logo4.png',
    ];

    theme_klassplace_load_assets('theme_klassplace', $samples, ['clientlogo']);

    // Changes the max overviewfile limit.
    if ($CFG->courseoverviewfileslimit == 1) {
        set_config('courseoverviewfileslimit', 2);
    }
}

/**
 * Loads some configuration defaults and some default file assets into file store.
 * @param array $samples an array of config attribute sets, by config master key.
 * @param array $imageattributes an array ok attribute names for which the attribute designates an assets to add
 * to the moodle filestore.
 */
function theme_klassplace_load_assets($themename, $samples, $imageattributes) {
    global $CFG;

    $fs = get_file_storage(); // Singleton call.

    foreach ($samples as $attributedef => $attributevalues) {
        $i = 1;
        foreach ($attributevalues as $attrvalue) {
            $configkey = $attributedef.$i;
            $oldsetting = get_config($themename, $configkey);
            if (is_null($oldsetting)) {
                set_config($slotkey, $attrvalue, $themename);

                if (in_array($attributedef, $imageattributes)) {
                    if (!empty(!$attrvalue)) {
                        // $attrvalue should be an image.
                        // Transfer asset file to moodle filestore.
                        $filedesc = new StdClass;
                        $filedesc->contextid = context_system::instance()->id;
                        $filedesc->component = $themename;
                        $filedesc->filearea = $attributedef.$i;
                        $filedesc->itemid = 0;
                        $filedesc->filepath = '/';
                        $filedesc->filename = $attrvalue;

                        $themedir = str_replace('theme_', '', $themename);
                        $fileloc = $CFG->dirroot.'/theme/'.$themedir.'/__assets/'.$attributedef.'/'.$attrvalue;
                        $fs->create_file_from_path($filedesc, $fileloc);
                    }
                }
            }
            $i++;
        }
    }
}