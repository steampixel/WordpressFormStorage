<?php
/*
Plugin Name: SteamPixel Form Storage
Plugin URI: http://steampixel.de
Description: Store submitted post data
Version: 1.0.0
Author: SteamPixel Media Solutions
Author URI: http://steampixel.de
License: Copyright (C) SteamPixel Media Solutions - All Rights Reserved
Text Domain: steampixel
*/

$spfs_plugin_dir = dirname(__FILE__);

require_once('setup.php');
require_once('plugin_options.php');
require_once('download.php');
require_once('status.php');
require_once('custom_post_types.php');
require_once('storage.php');
