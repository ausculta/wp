<?php
/*
 * @link        http://endeavouresu.uk
 * @since       1.0.0
 * @package     wp-endeavouresu
 *
 * @wordpress-plugin
 * Plugin Name: wp-endeavouresu
 * Plugin URI:  
 * Requires at least: 5.5
 * Requires PHP: 7.4
 * Description: Plugin to simplify the management of Endeavour Explorer Scout Unit
 * Version:     1.0.0
 * Author:      Ausculta Ltd
 * Author URI:  http://ausculta.net
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-endeavouresu
 * Domain Path: /languages
 */

/* Copyright 2020 Ausculta Ltd (email: info@ausculta.net)
wp-endeavouresu is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
wp-endeavouresu is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with wp-endeavouresu. If not, see http://.
*/


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( !defined( 'WPENDEAVOURESU_VER' ) ) {
	define( 'WPENDEAVOURESU_VER', '1.0.0' );
}


function wpendeavouresu_init() {
    // back end
    // add_action ( 'plugins_loaded', array( $this, 'wp-endeavouresu') );
    // register_setting( 'wporg_settings', 'wporg_option_foo' );
		    
    // front end
    add_shortcode( "tag", $func);
    
}

/**
 * load textdomain
 *
 * @return void
 */
function wpendeavouresu_textdomain() {
    load_plugin_textdomain( 'wpendeavouresu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
       

function wpendeavouresu_allexplorers($atts = [], $content = null) {
    global  $wpdb;

    // Prepare the content to replace the tag
    // Table definition
    $content = "<div><table class=\"table\">\n";
    // Table head
    $content = $content . "\t<thead>\n\t\t<tr>";
    $content = $content . "\t\t\t<td>Explorer Name<\td>\n";
    $content = $content . "\t\t\t<td>Status</td>\n";
    $content = $content . "\t\t\t<td>Type</td>\n";
    $content = $content . "\t\t\t<td>Start date</td>\n";
    $content = $content . "\t\t\t<td>End date</td>\n";
    $content = $content . "\t\t</tr>\n\t</thead>\n";

    // Table content
    $sql = "SELECT FROM U.displayname, S.Description, T.Description, E.ExpDateStart, E.ExpDateEnd ";
    $sql = $sql . "FROM " . $wpdb->base_prefix . "users U, exp1_explorers E, exp1_expstatus S, exp1_exptypes T";
    $sql = $sql . "WHERE U.ID = E.ExpWPID AND E.ExpStatusID = S.ExpStatusID AND T.ExpTypeID = E.ExpTypeID";
    // Flush the DB cache and run the query
    $wpdb->flush();
    $result = $wpdb->get_results($wpdb->prepare($sql), ARRAY_N);
    if (($i = count($result)) > 0) {
        // Query returned rows
        foreach ($result as $row) {
            $content = $content . "\t\t<tr><td>" . $row[0] . "</td>";
            $content = $content . "<td>" . $row[1] . "</td>";
            $content = $content . "<td>" . $row[2] . "</td>";
            $content = $content . "<td>" . $row[3] . "</td>";
            $content = $content . "<td>" . $row[4] . "</td></tr>";
        }
        $wpdb->flush();
    } else {
        $content = $content . "\t\t<tr><td colspan=5>There are no explorer records in the database</td></tr>\n"
    }
    // Close the table
    $content = $content . "</table></div>\n";

    $content = $content . "<div class=\"text-center\"><a class="btn btn-primary" href="#" role="button">Add explorers</a>";
    
    // always return
    return $content;
}
?>