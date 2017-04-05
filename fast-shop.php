<?php
/*
Plugin Name: Fast Shop
Plugin URI: https://fast-shop.profglobal.ru
Description:  The plugin will help in the creation of any online store without changing your template.
Version: 1.0.1
Author: Vitaliy Karakushan
Author URI: https://fast-shop.profglobal.ru
License: GPL2
Domain: fast-shop
*/
/*
Copyright 2016 Vitaliy Karakushan  (email : karakushan@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

require_once __DIR__ . '/vendor/autoload.php';

define( 'FS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'FS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( '\FS\FS_Init', false ) ) {

	$fs_init = new \FS\FS_Init;

	if ( $fs_option['debug'] ) {
		ini_set( 'error_reporting', E_ALL );
		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );
	}

	// Installation and uninstallation hooks
	register_activation_hook( __FILE__, array( $fs_init, 'fs_activate' ) );
	register_deactivation_hook( __FILE__, array( $fs_init, 'fs_deactivate' ) );
}



