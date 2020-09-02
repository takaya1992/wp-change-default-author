<?php
/*
Plugin Name: WP Change Default Author
Description: Change default author
Version: 1.0.0
Author: takaya1992
Author URI: http://takaya1992.com/
License: GPLv2 or later
 */

/*
WP Change Default Author is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

WP Change Default Author is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WP Change Default Author. If not, see https://www.gnu.org/licenses/gpl-3.0.txt .
*/

define( 'WP_CHANGE_DEFAULT_AUTHOR_VERSION', '1.0.0' );

define( 'WP_CHANGE_DEFAULT_AUTHOR__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( WP_CHANGE_DEFAULT_AUTHOR__PLUGIN_DIR . 'classes/WPChangeDefaultAuthor.php' );

add_action( 'init', array( 'WPChangeDefaultAuthor', 'init' ) );

function WPChangeDefaultAuthor__uninstall() {
	delete_option( WPChangeDefaultAuthor::AUTHOR_OPTION_NAME );
}
register_uninstall_hook( __FILE__, 'WPChangeDefaultAuthor__uninstall' );
