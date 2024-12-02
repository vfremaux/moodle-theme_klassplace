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
* Social networking settings page file.
*
* @package    theme_klassplace
* @copyright  2020 Nicolas Maligue
* 
* @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

defined('MOODLE_INTERNAL') || die();

$page = new admin_settingpage($themename.'_homepage_social', get_string('socialsection', 'theme_klassplace'));

/*
 * reason : only panel.
 *
$name = $themename.'/showsocialinheader';
$title = get_string('showsocialinheader','theme_klassplace');
$description = get_string('showsocialinheader_desc', 'theme_klassplace');
$default = 0;
$setting = new admin_setting_configcheckbox($name, $title, $description, $default);
$page->add($setting);
*/

/*
 *
 * Reason : let the social panel as a thin row without comments.
 *
$name = $themename.'/socialpanelsettings_hdr';
$title = get_string('socialpanelsettings', 'theme_klassplace');
$description = format_text(get_string('socialpanelsettings_desc' , 'theme_klassplace'), FORMAT_MARKDOWN);
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

$name = $themename.'/socialheading';
$title = get_string('socialheading','theme_klassplace');
$description = get_string('socialheading_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

$name = $themename.'/socialtagline';
$title = get_string('socialtagline','theme_klassplace');
$description = get_string('socialtagline_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtextarea($name, $title, $description, $default);
$page->add($setting);
*/

$name = $themename.'/sociallinkssettings_hdr';
$title = get_string('sociallinkssettings', 'theme_klassplace');
$description = format_text(get_string('sociallinkssettings_desc' , 'theme_klassplace'), FORMAT_MARKDOWN);
$setting = new admin_setting_heading($name, $title, $description);
$page->add($setting);

// displayfacebook setting.
$name = $themename.'/displayfacebook';
$title = get_string('displayfacebook','theme_klassplace');
$description = get_string('displayfacebook_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// facebook url setting.
$name = $themename.'/facebook';
$title = get_string('facebook', 'theme_klassplace');
$description = get_string('facebook_desc', 'theme_klassplace');
$default = 'http://www.facebook.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displaytwitter setting.
$name = $themename.'/displaytwitter';
$title = get_string('displaytwitter','theme_klassplace');
$description = get_string('displaytwitter_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// twitter url setting.
$name = $themename.'/twitter';
$title = get_string('twitter', 'theme_klassplace');
$description = get_string('twitter_desc', 'theme_klassplace');
$default = 'http://www.twitter.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displaygoogleplus setting.
$name = $themename.'/displaygoogleplus';
$title = get_string('displaygoogleplus','theme_klassplace');
$description = get_string('displaygoogleplus_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// googleplus url setting.
$name = $themename.'/googleplus';
$title = get_string('googleplus', 'theme_klassplace');
$description = get_string('googleplus_desc', 'theme_klassplace');
$default = 'http://www.googleplus.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displaypinterest setting.
$name = $themename.'/displaypinterest';
$title = get_string('displaypinterest','theme_klassplace');
$description = get_string('displaypinterest_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// pinterest url setting.
$name = $themename.'/pinterest';
$title = get_string('pinterest', 'theme_klassplace');
$description = get_string('pinterest_desc', 'theme_klassplace');
$default = 'http://www.pinterest.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displayinstagram setting.
$name = $themename.'/displayinstagram';
$title = get_string('displayinstagram','theme_klassplace');
$description = get_string('displayinstagram_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// instagram url setting.
$name = $themename.'/instagram';
$title = get_string('instagram', 'theme_klassplace');
$description = get_string('instagram_desc', 'theme_klassplace');
$default = 'http://www.instagram.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displayyoutube setting.
$name = $themename.'/displayyoutube';
$title = get_string('displayyoutube','theme_klassplace');
$description = get_string('displayyoutube_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// youtube url setting.
$name = $themename.'/youtube';
$title = get_string('youtube', 'theme_klassplace');
$description = get_string('youtube_desc', 'theme_klassplace');
$default = 'http://www.youtube.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displayflickr setting.
$name = $themename.'/displayflickr';
$title = get_string('displayflickr','theme_klassplace');
$description = get_string('displayflickr_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// flickr url setting.
$name = $themename.'/flickr';
$title = get_string('flickr', 'theme_klassplace');
$description = get_string('flickr_desc', 'theme_klassplace');
$default = 'http://www.flickr.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displaywhatsapp setting.
$name = $themename.'/displaywhatsapp';
$title = get_string('displaywhatsapp','theme_klassplace');
$description = get_string('displaywhatsapp_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// whatsapp url setting.
$name = $themename.'/whatsapp';
$title = get_string('whatsapp', 'theme_klassplace');
$description = get_string('whatsapp_desc', 'theme_klassplace');
$default = 'http://www.whatsapp.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

//displayskype
$name = $themename.'/displayskype';
$title = get_string('displayskype','theme_klassplace');
$description = get_string('displayskype_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// skype url setting.
$name = $themename.'/skype';
$title = get_string('skype', 'theme_klassplace');
$description = get_string('skype_desc', 'theme_klassplace');
$default = 'http://www.skype.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displaygithub setting.
$name = $themename.'/displaylinkedin';
$title = get_string('displaylinkedin','theme_klassplace');
$description = get_string('displaylinkedin_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// github url setting.
$name = $themename.'/linkedin';
$title = get_string('linkedin', 'theme_klassplace');
$description = get_string('linkedin_desc', 'theme_klassplace');
$default = 'http://www.linkedin.com/mycollege';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Display Vimeo setting.
$name = $themename.'/displayvimeo';
$title = get_string('displayvimeo', 'theme_klassplace');
$description = get_string('displayvimeo_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Vimeo url setting.
$name = $themename.'/vimeo';
$title = get_string('vimeo', 'theme_klassplace');
$description = get_string('vimeo_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// displaycontactno setting.
$name = $themename.'/displaycontactno';
$title = get_string('displaycontactno','theme_klassplace');
$description = get_string('displaycontactno_desc', 'theme_klassplace');
$default = 1;
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// contactno .
$name = $themename.'/contactno';
$title = get_string('contactno', 'theme_klassplace');
$description = get_string('contactno_desc', 'theme_klassplace');
$default = '0 900 555 22 33';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Display General social url setting 1.
$name = $themename.'/displaysocial1';
$title = get_string('displaysociallink', 'theme_klassplace').' 1';
$description = get_string('displaysociallink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

$name = $themename.'/social1';
$title = get_string('sociallink', 'theme_klassplace').' 1';
$description = get_string('sociallink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Social icon setting 1.
$name = $themename.'/iconsocial1';
$title = get_string('sociallinkicon', 'theme_klassplace');
$description = get_string('sociallinkicon_desc', 'theme_klassplace');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Display General social url setting 2.
$name = $themename.'/displaysocial2';
$title = get_string('displaysociallink', 'theme_klassplace').' 2';
$description = get_string('displaysociallink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// General social url setting 2.
$name = $themename.'/social2';
$title = get_string('sociallink', 'theme_klassplace').' 2';
$description = get_string('sociallink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Social icon setting 2.
$name = $themename.'/iconsocial2';
$title = get_string('sociallinkicon', 'theme_klassplace');
$description = get_string('sociallinkicon_desc', 'theme_klassplace');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);

// Display General social url setting 3.
$name = $themename.'/displaysocial3';
$title = get_string('displaysociallink', 'theme_klassplace').' 3';
$description = get_string('displaysociallink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configselect($name, $title, $description, $default, $yesnochoices);
$page->add($setting);

// General social url setting 3.
$name = $themename.'/social3';
$title = get_string('sociallink', 'theme_klassplace').' 3';
$description = get_string('sociallink_desc', 'theme_klassplace');
$default = '';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$page->add($setting);

// Social icon setting 3.
$name = $themename.'/iconsocial3';
$title = get_string('sociallinkicon', 'theme_klassplace');
$description = get_string('sociallinkicon_desc', 'theme_klassplace');
$default = 'home';
$setting = new admin_setting_configtext($name, $title, $description, $default);
$setting->set_updatedcallback('theme_reset_all_caches');
$page->add($setting);


// Must add the page after definiting all the settings!
$ADMIN->add($themename.'_homepage', $page);