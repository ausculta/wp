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
    $content = "<div><table class=\"table-sm\">\n";
    // Table head
    $content = $content . "\t<thead>\n\t\t<tr>\n";
    $content = $content . "\t\t\t<td>Explorer Name</td>\n";
    $content = $content . "\t\t\t<td>Status</td>\n";
    $content = $content . "\t\t\t<td>Start date</td>\n";
    $content = $content . "\t\t\t<td>End date</td>\n";
    $content = $content . "\t\t</tr>\n\t</thead>\n";

    // Table content
    $sql = "SELECT U.display_name, S.Description, E.ExpDateStart, E.ExpDateEnd, E.ExpID ";
    $sql = $sql . "FROM " . $wpdb->base_prefix . "users U, exp1_explorers E, exp1_expstatus S ";
    $sql = $sql . "WHERE U.ID = E.ExpWPID AND E.ExpStatusID = S.ExpStatusID AND E.Deleted = 0 ";
    $sql = $sql . "ORDER BY U.display_name";
    // Flush the DB cache and run the query
    $wpdb->flush();
    $result = $wpdb->get_results($sql, ARRAY_N);
    if (($i = count($result)) > 0) {
        // Query returned rows
        foreach ($result as $row) {
            $content = $content . "\t\t<tr><td class=\"explorer\" data-toggle=\"modal\" data-target=\"#modalGetExplorer\" id=\"" . esc_html($row[4]). "\">" . esc_html($row[0]) . "</td>";
            $content = $content . "<td>" . esc_html($row[1]) . "</td>";
            $content = $content . "<td>" . esc_html($row[2]) . "</td>";
            $content = $content . "<td>" . esc_html($row[3]) . "</td></tr>\n";
        }
        $wpdb->flush();
    } else {
        $content = $content . "\t\t<tr><td colspan=4 class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
    }
    // Close the table
    $content = $content . "</table></div>\n";
    $content = $content . "<div class=\"text-align-center\"><button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalAddExplorers\" id=\"btnAddExplorers\">Add explorers</button></div>\n";

    // $content = $content . "SQL: " . $sql . ";\n";
    
    $nonce_allexplorers = wp_create_nonce( 'wp-endeavouresu_allexplorers' );

    $content = $content . "<div class=\"modal fade\" id=\"modalAddExplorers\" tabindex=\"-1\" aria-labelledby=\"modalAddExplorersLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog  modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalAddExplorersLabel\">Add explorers from user list</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalAddExplorersBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnSaveExplorers\">Save changes</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    $nonce_allexplorers = wp_create_nonce( 'wp-endeavouresu_getexplorer' );

    $content = $content . "<div class=\"modal fade\" id=\"modalGetExplorer\" tabindex=\"-1\" aria-labelledby=\"modalGetExplorerLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog modal-xl modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalGetExplorerLabel\">Retrieving explorer name</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalGetExplorerBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\" data-target=\"#modalAddExplorers\" id=\"btnExplorerClose\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnEditStatus\">Edit Status</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnEditType\">Edit Type</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnAddNA\">Nights Away</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnAddHike\">Hikes</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnAddAward\">Badges & Awards</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    // always return
    return $content;
}

function wpendeavouresu_enqueuescript( $hook ) {
    // If this is not a page, return
    // Otherwise, enqueue the script
    wp_enqueue_script( 'ajax-getnonexpusers', plugins_url('js/wp-endeavouresu-getnonexpusers.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajax-savenewexplorers', plugins_url('js/wp-endeavouresu-savenewexplorers.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajax-getexplorer', plugins_url('js/wp-endeavouresu-getexplorer.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajax-getexplorerdata', plugins_url('js/wp-endeavouresu-getexplorerdata.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajax-updateexplorerdata', plugins_url('js/wp-endeavouresu-updateexplorerdata.js', __FILE__ ), array('jquery') );
    $nonce_getnonexpusers = wp_create_nonce( 'wpendeavouresu_getnonexpusers' );
    $nonce_savenewexplorers = wp_create_nonce( 'wpendeavouresu_savenewexplorers' );
    $nonce_getexplorer = wp_create_nonce( 'wpendeavouresu_getexplorer' );
    $nonce_getexplorerdata = wp_create_nonce( 'wpendeavouresu_getexplorerdata' );
    $nonce_updateexplorerdata = wp_create_nonce( 'wpendeavouresu_updateexplorerdata' );
    wp_localize_script( 'ajax-savenewexplorers', 'ajaxdata_newexplorers', array(
       'ajax_url' => admin_url( 'admin-ajax.php' ),
       'nonce'    => $nonce_savenewexplorers,
    ) );
    wp_localize_script( 'ajax-getnonexpusers', 'ajaxdata_nonexpusers', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getnonexpusers,
     ) );
     wp_localize_script( 'ajax-getexplorer', 'ajaxdata_getexplorer', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getexplorer,
     ) );
     wp_localize_script( 'ajax-getexplorerdata', 'ajaxdata_getexplorerdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getexplorerdata,
     ) );
     wp_localize_script( 'ajax-updateexplorerdata', 'ajaxdata_updateexplorerdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_updateexplorerdata,
     ) );
}

function wpendeavouresu_getnonexpusers() {
    global  $wpdb;

    // Handle the ajax request
    check_ajax_referer('wpendeavouresu_getnonexpusers');

    // Flush the DB cache and run the query
    $wpdb->flush();
    $sql = "SELECT ID, display_name FROM " . $wpdb->base_prefix . "users ";
    $sql = $sql . "WHERE " . $wpdb->base_prefix . "users.ID NOT IN (SELECT DISTINCT ExpWPID FROM exp1_explorers) ORDER BY display_name ASC";
    $result = $wpdb->get_results($sql, ARRAY_N);
    $content = array();
    $explist = array();
    if (count($result) > 0) {
        // Query returned rows
        foreach ($result as $row) {
            $explist[] = array( 'id' => esc_html($row[0]), 'display_name' => esc_html($row[1]));
        }
    }
    $content['expno'] = count($result);
    $content['explist'] = $explist;
    $wpdb->flush();

    // Send the data back
    wp_send_json($content);
   
    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_savenewexplorers() {
    global  $wpdb;

    check_ajax_referer('wpendeavouresu_savenewexplorers');
    $dbrecords = 0;
    
    if (! empty($_POST['dbdata'])) {
        // $newexp = json_decode($_POST['dbdata'], true);
        $newexp = $_POST['dbdata'];
       
        foreach ($newexp as $explorer) {
            if (strlen($explorer.['name']) < 4) continue;
            $wpdb->flush();
            $sql = "INSERT INTO exp1_explorers (ExpWPID, ExpDateStart, ExpStatusID) VALUES (" . $explorer['value'] . ", CURRENT_DATE, 1)";
            // query returns the number of affected rows - ignored.
            $wpdb->query($sql);       
            $dbrecords++;
        }
    }
    $wpdb->flush();
    $content['dbrecords'] = $dbrecords;
    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_getexplorer() {
    global  $wpdb;

    // Handle the ajax request
    check_ajax_referer('wpendeavouresu_getexplorer');

    if (! empty($_GET['ExpID'])) {
        // Flush the DB cache and run the query
        $expID = $_GET['ExpID'];
        // echo "ExpID: " + $expID;
        $wpdb->flush();
        $sql = "SELECT U.display_name, U.user_login, E.ExpDateStart, E.ExpDateEnd, S.Description, E.TotalNightsAway, E.TotalHikes ";
        $sql = $sql . "FROM " . $wpdb->base_prefix . "users U, exp1_expstatus S, exp1_explorers E ";
        $sql = $sql . "WHERE U.ID = E.ExpWPID AND S.ExpStatusID = E.ExpStatusID AND E.ExpID = " . $expID;
        // echo $sql;
        $expdata = $wpdb->get_row($sql, ARRAY_N, 0);
        if (count($expdata) > 0) {
            $content = array(
                'Name'=> esc_html($expdata[0]),
                'Login' => esc_html($expdata[1]),
                'DateStart' => esc_html($expdata[2]),               
                'DateEnd' => esc_html($expdata[3]),
                'Status' => esc_html($expdata[4]),
                'NightsAway' => esc_html($expdata[5]),
                'Hikes' => esc_html($expdata[6]),
                'ExpID' => esc_html($expID),
                'ExpHikes' => "0",
                'ExNAs'=> "0",
                'ExpBadges' => "0");
            if (is_null($expdata[3])) $content['DateEnd'] = "current";
        } else {
            $content = array ( 'Name' => 'ExpID not found in database');
        }
        $wpdb->flush();

        $sql = "SELECT ExpHikeID, Description, DateStart, DateEnd FROM exp1_exphikes WHERE ExpID = " .$expID . " ORDER BY DateStart DESC";
        // echo $sql;
        $hikedata = $wpdb->get_results($sql, ARRAY_N);
        $exphikes = array();
        if (count($hikedata) > 0) {
            foreach($hikedata as $hike) {
                $exphikes[] = array('ExpHikeID' => esc_html($hike[0]), 'Description' => esc_html($hike[1]), 'DateStart' => esc_html($hike[2]), 'DateEnd' => esc_html($hike[3]));
            }
        }
        $content['HikeNo'] = count($exphikes);
        $wpdb->flush();

        $sql = "SELECT ExpNightAwayID, DateStart, DateEnd, NALocation, NADays FROM exp1_expna WHERE ExpID = " . $expID . " ORDER BY DateStart DESC";
        // echo $sql;
        $nadata = $wpdb->get_results($sql, ARRAY_N);
        $expnas = array();
        if (count($nadata) > 0) {
            foreach($nadata as $expna) {
                $expnas[] = array('ExpNAID' => esc_html($expna[0]), 'DateStart' => esc_html($expna[1]), 'DateEnd' => esc_html($expna[2]), 'NALocation' => esc_html($expna[3]), 'NADays' => esc_html($expna[4]));
            }
        }
        $content['NANo'] = count($expnas);
        $wpdb->flush();

        $sql = "SELECT E.ExpTypeID, T.Description, E.DateStart FROM exp1_exptypes E, exp1_exptypetypes T ";
        $sql = $sql . "WHERE E.ExpID = " . $expID . " AND E.ExpTypeTypeID = T.ExpTypeTypeID AND E.DateEnd IS NULL";
        // echo $sql;
        $typedata = $wpdb->get_results($sql, ARRAY_N);
        if (count($typedata) > 0) {
            $content['ExpTypeID'] = esc_html($typedata[0]);
            $content['ExpType'] = esc_html($typedata[1]);
            $content['ExpTypeDateStart'] = esc_html($typedata[2]);
        } else {
            $content['ExpTypeID'] = esc_html(0);
            $content['ExpType'] = esc_html('Type not set');
            $content['ExpTypeDateStart'] = esc_html('Date not set');
        }
        $wpdb->flush();
        $sql = "SELECT B.BadgeID , B.Name, B.Description, B.IconPath, S.Description, T.Description, E.ExpBadgeID, E.DateStart, E.DateEnd ";
        $sql = $sql . "FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T, exp1_expbadges E ";
        $sql = $sql . "WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID AND B.BadgeID = E.BadgeID AND E.ExpID = " . $expID . " ";
        $sql = $sql . "GROUP BY B.BadgeTypeID ORDER BY B.Description";
        // echo $sql;
        $badgedata = $wpdb->get_results($sql, ARRAY_N);
        $expbadges = array();
        if (count($badgedata) > 0) {
            foreach($badgedata as $badge) {
                $expbadges[] = array('BadgeID' => esc_html($badge[0]), 'BadgeName' => esc_html($badge[1]),
                                    'BadgeDescription' => esc_html($badge[2]), 'IconPath' => esc_html($badge[3]),
                                    'BadgeStatus' => esc_html($badge[4]), 'BadgeType' => esc_html($badge[5]),
                                    'ExpBadgeID' => esc_html($badge[6]), 'DateStart' => esc_html($badge[7]), 'DateEnd' => esc_html($badge[8]) );
            }
        }
        $content['BadgeNo'] = count($expbadges);
        $wpdb->flush();

        if (count($exphikes) > 0) $content['ExpHikes'] = $exphikes;
        if (count($expnas) > 0) $content['ExpNAs'] = $expnas;
        if (count($expbadges) > 0) $content['ExpBadges'] = $expbadges;
    }
    // Send the data back
    wp_send_json($content);
   
    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_getexplorerdata() {
    global  $wpdb;

    // Handle the ajax request
    //check_ajax_referer('nonce_getexplorerdata');

    $content = array();
    if (! empty($_GET['ExpID'])) {
        $expID = $_GET['ExpID'];
        $content['ExpID'] = $expID;
        // echo "ExpID: " . $expID;
        
        // Flush the database cache, just in case
        $wpdb->flush();
        
        switch ($_GET['actiontype']) {
            case "EditStatus":
                $sql = "SELECT ExpStatusID FROM exp1_explorers WHERE ExpID = " . $expID;
                $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
                if (count($dbdata) > 0) {
                    $content['ExpStatusID'] = $dbdata[0];
                    $wpdb->flush();               
                    $sql = "SELECT ExpStatusID, Description FROM exp1_expstatus WHERE Deleted = 0 ORDER BY Description";
                    $dbdata = $wpdb->get_results($sql, ARRAY_N);
                    $statusdata = array();
                    if (count($dbdata) > 0) {
                        foreach($dbdata as $dbrow) {
                            $statusdata[] = array('StatusID' => esc_html($dbrow[0]), 'Description' => esc_html($dbrow[1]));
                        }
                    }
                    $content['ExpStatusNo'] = count($statusdata);
                    $content['ExpStatus'] = $statusdata;
                }
                break;
            default:
                break;
        }
    } else {
        $content = "Please provide an explorer ID";
    }
    // Send the data back
    wp_send_json($content);
   
    wp_die(); // All ajax handlers die when finished
}


function wpendeavouresu_updateexplorerdata() {
    global  $wpdb;
    
    check_ajax_referer('wpendeavouresu_updateexplorerdata');
    
    // Declare the return data
    $content = array();
    
    if ((! empty($_POST['dbdata'])) && (! empty($_POST['updatetype']))) {
        // $newexp = json_decode($_POST['dbdata'], true);
        $newdata = $_POST['dbdata'];

        $wpdb->flush();
        switch ($_POST['updatetype']) {
            case "EditStatus":
                $sql = "UPDATE exp1_explorers SET ExpStatusID = " . $_POST['rdoActive'] . " WHERE ExpID = " . $_POST['ExplorerID'];
                // query returns the number of affected rows - ignored.
                $dbresult = $wpdb->query($sql);
                if ($dbresult > 0) $content['success'] = 1;
                break;
            default:
                break;
        }
    }
    $wpdb->flush();
    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

// Shortcode to trigger the plugin from the page
add_shortcode('EndeavourESU_AllExplorers', 'wpendeavouresu_allexplorers');

// Add the JQuery scripts to the page
add_action('wp_enqueue_scripts' , 'wpendeavouresu_enqueuescript');

// Add the handler for AJAX request to get the list of non explorer users 
add_action('wp_ajax_get_nonexpusers', 'wpendeavouresu_getnonexpusers');

// Add the handler for AJAX request to save new explorers
add_action('wp_ajax_save_newexplorers', 'wpendeavouresu_savenewexplorers');

// Add the handler for AJAX request to view explorer details 
add_action('wp_ajax_get_explorer', 'wpendeavouresu_getexplorer');

// Add the handler for AJAX request to get explorer details 
add_action('wp_ajax_get_explorerdata', 'wpendeavouresu_getexplorerdata');

// Add the handler for AJAX request to update explorer details 
add_action('wp_ajax_update_explorerdata', 'wpendeavouresu_updateexplorerdata');

// 5
// SELECT U.display_name, U.user_login, E.ExpDateStart, E.ExpDateEnd, S.Description, E.TotalNightsAway, E.TotalHikes FROM edvr1_users U, exp1_expstatus S, exp1_explorers E WHERE U.ID = E.ExpWPID AND S.ExpStatusID = E.ExpStatusID AND E.ExpID = 5
// SELECT ExpHikeID, Description, DateStart, DateEnd FROM exp1_exphikes WHERE ExpID = 5 ORDER BY DateStart DESC
// SELECT ExpNightAwayID, DateStart, DateEnd, NALocation, NADays FROM exp1_expna WHERE ExpID = 5 ORDER BY DateStart DESC
// SELECT E.ExpTypeID, T.Description, E.DateStart FROM exp1_exptypes E, exp1_exptypetypes T WHERE E.ExpID = 5 AND E.ExpTypeTypeID = T.ExpTypeTypeID AND E.DateEnd IS NULL
// {"Name":["Alaric Childerhouse","alc@endeavouresu.uk","2020-10-02",null,"Active","0","0"],"Login":null,"DateStart":null,"DateEnd":null,"Status":null,"NightsAway":null,"Hikes":null,"ExpID":"5","ExpTypeID":null,"ExpType":null,"ExpTypeDateStart":null,"HikeNo":0,"NANo":0,"BadgeNo":0}
// https://endeavouresu.uk/wp-admin/admin-ajax.php?_ajax_nonce=c1b3af127f&action=get_explorer&ExpID=4
// {"Name":["Alex Privett","ap@endeavouresu.uk","2020-10-02",null,"Active","0","0"],"Login":null,"DateStart":null,"DateEnd":null,"Status":null,"NightsAway":null,"Hikes":null,"ExpID":"4","ExpTypeID":null,"ExpType":null,"ExpTypeDateStart":null,"HikeNo":0,"NANo":0,"BadgeNo":0,"ExpHikes":"0","ExNAs":"0","ExpBadges":"0"}

?>
