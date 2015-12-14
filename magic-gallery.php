<?php
/**
 * Plugin Name: Magic Gallery
 * Plugin URI: github.com/PDKK
 * Description: A mofiied verion of the multiple galleries on page plugin
 * Version: 0.1
 * Author: Paul Knox-Kennedy
 * Author URI: www.knox-kennedy.me.uk
 * License: GPL2
 */

/*  Modifications Copyright 2015  Paul Knox-Kennedy
    Original Copyright 2013  I Wayan Wirka  (email : wirka.wayan@gmail.com)

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


include_once(plugin_dir_path( __FILE__ ) . "admin/options.php");

include_once(plugin_dir_path( __FILE__ ) . "admin/metabox.php");

if ( is_admin() ) {

	new MGOP_Dynamic_Meta_Boxes( $mgop_mbs );
	
}

include_once(plugin_dir_path( __FILE__ ) . "functions.php");


?>
