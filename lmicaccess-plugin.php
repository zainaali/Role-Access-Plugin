<?php

/**
 * @package AcessPlugin
 */

 /*
Plugin Name: Role Access Plugin
Plugin URI: https://findzain.netlify.app/
Description: Manage Role Access for Website.
Version: 1.0.0
Author: Zain
Author URI: https://findzain.netlify.app/
License: Copyrights for LMIC.
Text Domain: lmicaccess-plugin
*/

/*
This software is under copyrights of Zain and devvelop to be use
by LMIC on their website.

*/


namespace WP_RolesEM;

define('REM2_PLUGIN_URL'       , plugins_url(basename(plugin_dir_path(__FILE__ )), basename(__FILE__)));
define('REM2_ASSETS_URL'       , REM2_PLUGIN_URL . '/assets/');
define('REM2_PLUGIN_FILE'      , __FILE__);

require_once __DIR__ . '/autoloader.php';

new Includes\RolesEm();