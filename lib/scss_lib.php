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
 * SCSS Lib file.
 *
 * @package    theme_klassplace
 * @copyright  2016 Chris Kenniburg
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_klassplace_css_tree_post_processor($tree, $theme) {
    $prefixer = new theme_boost\autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_klassplace_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    $iterator = new DirectoryIterator($CFG->dirroot . '/theme/klassplace/scss/preset/');
    $presetisset = '';
    foreach ($iterator as $pfile) {
        if (!$pfile->isDot()) {
            $presetname = substr($pfile, 0, strlen($pfile) - 5); // Name - '.scss'.
            if ($filename == $presetname) {
                $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/preset/' . $pfile);
                $presetisset = true;
            }
        }
    }
    if (!$presetisset) {
        $filename .= '.scss';
        if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_'.$theme->name, 'preset', 0, '/', $filename))) {
            $scss .= $presetfile->get_content();
        }
        else {
            // Safety fallback - maybe new installs etc.
            // Nothing presetted.
            $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/preset/Default.scss');
        }
    }

    $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/klassplace_variables.scss');

    // Page Layout
    if (@$theme->settings->pagelayout == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/pagelayout/layout1.scss');
    }
    if (@$theme->settings->pagelayout == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/pagelayout/layout2.scss');
    }
    if (@$theme->settings->pagelayout == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/pagelayout/layout3.scss');
    }
    if (@$theme->settings->pagelayout == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/pagelayout/layout4.scss');
    }
    if (@$theme->settings->pagelayout == 5) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/pagelayout/layout5.scss');
    }

    // Section Style
    if (@$theme->settings->sectionlayout == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle1.scss');
    }
    if (@$theme->settings->sectionlayout == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle2.scss');
    }
    if (@$theme->settings->sectionlayout == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle3.scss');
    }
    if (@$theme->settings->sectionlayout == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle4.scss');
    }
    if (@$theme->settings->sectionlayout == 5) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle5.scss');
    }
    if (@$theme->settings->sectionlayout == 6) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle6.scss');
    }
    if (@$theme->settings->sectionlayout == 7) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle7.scss');
    }
    if (@$theme->settings->sectionlayout == 8) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/sectionlayout/sectionstyle8.scss');
    }

    if (@$theme->settings->marketingstyle == 1) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/marketingstyle/marketingstyle1.scss');
    }
    if (@$theme->settings->marketingstyle == 2) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/marketingstyle/marketingstyle2.scss');
    }
    if (@$theme->settings->marketingstyle == 3) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/marketingstyle/marketingstyle3.scss');
    }
    if (@$theme->settings->marketingstyle == 4) {
        $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/marketingstyle/marketingstyle4.scss');
    }

    include($CFG->dirroot.'/theme/klassplace/config_scss.php');

    if (!empty($SCSS->sheets)) {
        foreach ($SCSS->sheets as $scsssheet) {
            if (is_readable($CFG->dirroot . '/theme/klassplace/scss/'.$scsssheet.'.scss')) {
                $scss .= file_get_contents($CFG->dirroot . '/theme/klassplace/scss/'.$scsssheet.'.scss');
            }
        }
    }

    // Add variant local sheet.
    if (preg_match('/\d{2}$/', $theme->name)) {
        // We are in a numbered variant.
        $scss .= file_get_contents($CFG->dirroot . '/theme/'.$theme->name.'/scss/variant.scss');
    }

    // Post add scss.
    if (!empty($theme->settings->scss)) {
        $scss .= $theme->settings->scss;
    }

    /*
    $SCSSDUMP = fopen($CFG->dataroot.'/scss.log', 'w');
    if ($SCSSDUMP) {
        fputs($SCSSDUMP, $scss);
        fclose($SCSSDUMP);
    }
    */

    return $scss;
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_klassplace_get_pre_scss($theme) {
    global $CFG, $PAGE;

    $prescss = '';

    $configurable = [
    // Config key => variableName,

    // Master colors.
    'brandprimary' => ['brandprimary'],
    'brandprimaryalt' => ['brandprimaryalt'],
    'brandsecondary' => ['brandsecondary'],
    'brandsecondaryalt' => ['brandsecondaryalt'],
    'brandsuccess' => ['success'],
    'brandinfo' => ['info'],
    'brandwarning' => ['warning'],
    'branddanger' => ['danger'],

    'topnavbarbkg' => ['topnavbar-bg'],
    'topnavbarfg' => ['topnavbar-fg'],
    'topnavbarbkghov' => ['topnavbar-bg-hover'],
    'topnavbarteacherbkg' => ['topnavbar-teacher-bg'],

    'bodybackground' => ['body-bg'],

    'breadcrumbbkg' => ['breadcrumb-bg'],
    'breadcrumbfg' => ['breadcrumb-fg'],

    'cardbkg' => ['card-bg'],

    'drawerbkg' => ['drawer-bg'],
    'footerbkg' => ['footer-bg'],
    'footerfg' => ['footer-fg'],
    'footerdataprivacy' => ['footer-dataprivacy'],
    'footerlogininfo' => ['footer-logininfo'],
    'socialnavbkg' => ['socialnav-bg'],
    'announcementbkg' => ['announcement-bg'],
    'academicsbkg' => ['academics-bg'],
    'customboxbkg' => ['custombox-bg'],
    'customboxhoverbkg' => ['customboxhover-bg'],
    'circlesbkg' => ['circles-bg'],
    'indicatorsbkg' => ['indicators-bg'],
    'clientlogobkg' => ['clientlogo-bg'],
    /* 'socialbkg' => ['social-bg'], */
    'mydashboardbkg' => ['mydashboard-bg'],

    'fploginformbkg' => ['fploginform-bg'],
    'fploginformfg' => ['fploginform-fg'],

    'headerimagepadding' => ['headerimagepadding'],
    'iconwidth' => ['fpicon-width'],
    'learningcontentvoffset' => ['learningcontentvoffset'],
    'learningcontenthpadding' => ['learningcontenthpadding'],
    'blockwidth' => ['blockwidth'],
    'slideshowheight' => ['slideshowheight'],
    'activityiconsize' => ['activityiconsize'],
    'activitycustomiconwidth' => ['activitycustomiconwidth'],
    'pagecontentmaxwidth' => ['page-content-maxwidth'],
    'borderradius' => ['border-radius'],
    'borderbigradius' => ['border-big-radius'],

    'monochrome' => ['monochrome'],
    'usecustomfonts' => ['usecustomfonts'],
    'generalaltccsselector' => ['altfontselector'],
    ];

    // Add settings variables.
    foreach ($configurable as $configkey => $targets) {
        if (!isset($theme->settings->{$configkey})) {
            continue;
        }
        $value = $theme->settings->{$configkey};
        if (in_array($configkey, ['footermobileapp', 'footerdataprivacy', 'footerlogininfo'])) {
            // Process specially those settings that are checkboxes to control a display:none property.
            if (empty($value)) {
                $value = "none";
            } else {
                $value = "block";
            }
        } else if ($configkey == 'topnavbarbkg') {
            // Allow some settings "defaulting" other settings.
            if (empty($value)) {
                $value = $theme->settings->brandprimary;
            }
        } else if ($configkey == 'topnavbarfg') {
            // Allow some settings "defaulting" other settings.
            if (empty($value)) {
                $value = $theme->settings->brandprimaryalt;
            }
        } else if (empty($value)) {
            // $value = 'undefined';
            continue;
        }

        array_map(function ($target) use (&$prescss, $value) {
            $prescss .= '$' . $target . ': ' . $value . ";\n";
        }
        , (array)$targets);
    }

    // Set the image for customboxes
    $customsectionsimage = $theme->setting_file_url('customsectionsbkg', 'customsectionsbkg');
    if (isset($customsectionsimage)) {
        $prescss .= '$custombox-image: url("'.$customsectionsimage."\");\n";
    }

    // Load the fonts urls
    $generalbodyfonturl = $theme->setting_file_url('generalbodyfont', 'generalbodyfont');
    if (!empty($generalbodyfonturl)) {
        $prescss .= '$generalbodyfont: url("'.$generalbodyfonturl."\");\n";
    }

    $generalaltfonturl = $theme->setting_file_url('generalaltfont', 'generalaltfont');
    if (!empty($generalaltfonturl)) {
        $prescss .= '$generalaltfont: url("'.$generalaltfonturl."\");\n";
    }

    $titlefonturl = $theme->setting_file_url('titlefont', 'titlefont');
    if (!empty($titlefonturl)) {
        $prescss .= '$titlefont: url("'.$titlefonturl."\");\n";
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $prescss .= $theme->settings->scsspre;
    }

    // Set the default image for the header.
    $slide1image = $theme->setting_file_url('slide1image', 'slide1image');
    if (isset($slide1image)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $prescss .= '.slide1image {background-image: url("' . $slide1image . '"); background-size:cover; background-repeat: no-repeat; background-position:center;}';
    }

    // Set the default image for the header.
    $slide2image = $theme->setting_file_url('slide2image', 'slide2image');
    if (isset($slide2image)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $prescss .= '.slide2image {background-image: url("' . $slide2image . '"); background-size:cover; background-repeat: no-repeat; background-position:center;}';
    }

    // Set the default image for the header.
    $slide3image = $theme->setting_file_url('slide3image', 'slide3image');
    if (isset($slide3image)) {
        // Add a fade in transition to avoid the flicker on course headers ***.
        $prescss .= '.slide3image {background-image: url("' . $slide3image . '"); background-size:cover; background-repeat: no-repeat; background-position:center;}';
    }

    // Set the default image for the header.
    $headerbg = $theme->setting_file_url('headerdefaultimage', 'headerdefaultimage');

    // Set the background image for the page.
    $pagebg = $theme->setting_file_url('backgroundimage', 'backgroundimage');
    if (isset($pagebg)) {
        $prescss .= 'body {background-image: url("' . $pagebg . '"); background-size:cover; background-position:center;}';
    }

    // Set the background image for the event panel.
    $eventsbkgurl = $theme->setting_file_url('eventbkgimage', 'eventbkgimage');
    if (isset($eventsbkgurl)) {
        $prescss .= '$eventsbkgurl: url('.$eventsbkgurl.");\n";
    } else {
        $prescss .= '$eventsbkgurl: url([[pix:theme|events]]);'."\n";
    }

    return $prescss;
}

