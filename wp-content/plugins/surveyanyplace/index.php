<?php
/* Bij installatie plugin krijg je deze informatie te zien!
Plugin Name: SurveyAnyplace Plugin
Plugin URI:  https://surveyanyplace.com/
Description: Entertaining Surveys,Serious Results
Version:     1.0.0
Author:      Turgay Aydemir - SurveyAnyplace
Author URI:  https://surveyanyplace.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
if (!defined('ABSPATH')) {
    die('Access denied.');
}

define('SURVEYANYPLACE_BASE', plugin_dir_url(__FILE__));

$files_to_includes = array(
    'resources/library/surveyanyplace-widgets',
    'resources/library/surveyanyplace-shortcodes',
    'resources/library/surveyanyplace-functions'
);

foreach ($files_to_includes as $file) {
    include_once($file . '.php');
}



