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

function wpendeavouresu_activate() {

}

function wpendeavouresu_deactivate() {

}

function wpendeavouresu_listexplorers($atts = [], $content = null) {
    global $wpdb;

    $ExpID = get_current_user_id();

    $content = "";
    $sql = "SELECT E.ExpID, U.ID, U.display_name FROM " . $wpdb->base_prefix . "users U, exp1_explorers E WHERE U.ID = E.ExpWPID AND (E.ExpWPPID1 = " . $ExpID . " OR E.ExpWPPID2 = " . $ExpID . " OR E.ExpWPPID3 = " . $ExpID . ") ORDER BY U.display_name";
    $dbdata = $wpdb->get_results($sql, ARRAY_N, 0);
    if (! empty($dbdata)) {
        $content = $content . "Badge records:\n"; 
        foreach ($dbdata as $dbrow) {
            $content = $content . "<br><A HREF=\"/explorer-progress-record/?ExpID=" . $dbrow[1] . "\">" . $dbrow[2] . "</a>\n";
        }
    } else {
        $content = $content . "<A HREF=\"/explorer-progress-record/\">My progress report</a>\n";
    }
    // always return
    return $content;
}

function wpendeavouresu_explorer($atts = [], $content = null) {
    global $wpdb;

    $content = "";
    if (! empty($_GET['ExpID'])) {
        $ExpID = null;
        $UserID = get_current_user_id();
        $sql = "SELECT E.ExpWPPID1, E.ExpWPPID2, E.ExpWPPID3 FROM exp1_explorers E WHERE E.ExpWPID = " . $_GET['ExpID'];
        $expdata = $wpdb->get_row($sql, ARRAY_N, 0);
        if (! empty($expdata)) {
            if (($expdata[0] == $UserID) || ($expdata[1] == $UserID) || ($expdata[2] == $UserID)) {
                $ExpID  = $_GET['ExpID'];
                // $content = $content . "<h5 class=\"text-center\">ExpID matches a Parent ID.</h5>\n";
            }
        } 
        if (empty($ExpID)) {
            $content = $content . "<h5 class=\"text-center\">You are not authorised to view this data.</h5>\n";
        }
        $wpdb->flush();
    } else {
        $ExpID = get_current_user_id();
    }
    if (! empty($ExpID)) {
        //echo "ExpID: " . $ExpID;
        $wpdb->flush();
        $sql = "SELECT U.display_name, U.user_login, E.ExpDateStart, E.ExpDateEnd, S.Description, E.TotalNightsAway, E.TotalHikes, E.ExpID ";
        $sql = $sql . "FROM " . $wpdb->base_prefix . "users U, exp1_expstatus S, exp1_explorers E ";
        $sql = $sql . "WHERE U.ID = E.ExpWPID AND S.ExpStatusID = E.ExpStatusID AND U.ID = " . $ExpID;
        $expdata = $wpdb->get_row($sql, ARRAY_N, 0);
        if (count($expdata) > 0) {
             if (is_null($expdata[3])) {
                $dateEnd = "current";
            } else {
                $dateEnd = esc_html($expdata[3]);
            }
            $ExpID = $expdata[7];
        } else {
            $content = $content . "<h5 class=\"text-center\">ExpID not found in database</h5>\n";
        }
        $wpdb->flush();
        $sql = "SELECT ExpHikeID, Description, HikeDays, DateStart, DateEnd FROM exp1_exphikes WHERE ExpID = " .$ExpID . " ORDER BY DateStart DESC";
        $hikedata = $wpdb->get_results($sql, ARRAY_N);
        $HikeNo = count($hikedata);
        $wpdb->flush();

        $sql = "SELECT ExpNightAwayID, DateStart, DateEnd, Description, NALocation, NADays FROM exp1_expna WHERE ExpID = " . $ExpID . " ORDER BY DateStart DESC";
        $nadata = $wpdb->get_results($sql, ARRAY_N);
        $NANo = count($nadata);
        $wpdb->flush();

        $sql = "SELECT E.ExpTypeID, T.Description, E.DateStart FROM exp1_exptypes E, exp1_exptypetypes T ";
        $sql = $sql . "WHERE E.ExpID = " . $ExpID . " AND E.ExpTypeTypeID = T.ExpTypeTypeID AND E.DateEnd IS NULL";
        $typedata = $wpdb->get_row($sql, ARRAY_N);
        if (! empty($typedata)) {
            $ExpType = esc_html($typedata[1]);
        } else {
            $ExpType = esc_html('Type not set');
        }
        $wpdb->flush();
        $sql = "SELECT B.BadgeID, B.IconPath, CONCAT(B.Name, ' (', S.Description, ', ', T.Description, ')'), B.Description, E.ExpBadgeID, E.DateStart, E.DateEnd  ";
        $sql = $sql . "FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T, exp1_expbadges E ";
        $sql = $sql . "WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID AND B.BadgeID = E.BadgeID AND E.ExpID = " . $ExpID . " ";
        $sql = $sql . "ORDER BY B.BadgeTypeID, B.Description";
        $badgedata = $wpdb->get_results($sql, ARRAY_N);
        $BadgesNo = count($badgedata);
        $wpdb->flush();

        $content = $content . "<table class=\"table-sm w-100\">\n";
        $content = $content . "\t<tr><td colspan=2><h5 class=\"text-center\">" . esc_html($expdata[0]) . " (" . esc_html($expdata[1]) . " - " . esc_html($expdata[4]) . " - " . $ExpType . " - " . esc_html($expdata[2]) . " - " . $dateEnd . ")</h5></td></tr>\n";
        $content = $content . "\t<tr><td class=\"align-text-top\">" . esc_html($BadgesNo) . " Awards / Badges:</td><td>\n\t\t<table class=\"table\">\n";
        
        if (count($badgedata) > 0) {
            foreach($badgedata as $badge) {
                $content = $content . "\t\t\t<tr class=\"expbadge\" data-toggle=\"modal\" data-target=\"#modalExpBadgeReqts\" id=\"" . esc_html($badge[0]) . "\"><td><img height=\"25px\" src=\"" . esc_html($badge[1]) . "\"></td><td>" . esc_html($badge[3]) . "</td>";
                $content = $content . "<td>" . esc_html($badge[5]) . " - ";
                if ($badge[6] === "") {
                    $content = $content . "in progress";
                } else {
                    $content = $content . esc_html($badge[6]);
                }
                $content = $content . "</td><tr>\n";
            }
        }
        $content = $content . "\t\t</table></td></tr>\n";
        $content = $content . "\t<tr><td class=\"align-text-top\">" . esc_html($expdata[5]) . " nights away:</td><td>\n\t\t<table class=\"table\">\n";
        if (count($nadata) > 0) {
            foreach($nadata as $expna) {
                $content = $content . "\t\t\t<tr><td>" . esc_html($expna[3]) . " (" . esc_html($expna[4]) . ": " . esc_html($expna[5]) . " night(s) - " . esc_html($expna[1]) . " - " . esc_html($expna[2])  . ")</td><tr>";
            }
        }
        $content = $content . "\t\t</table></td></tr>\n";
        $content = $content . "\t<tr><td class=\"align-text-top\">" . esc_html($expdata[6]) . " Hikes:</td><td>\n\t\t<table class=\"table\">\n";
        if (count($hikedata) > 0) {
            foreach($hikedata as $hike) {
                $content = $content . "\t\t\t<tr><td>" . esc_html($hike[1]) . " (" . esc_html($hike[2]) . " hikes: " . esc_html($hike[3]) . " - " . esc_html($hike[4]) . ")</td><tr>";
            }
        }
        $content = $content . "\t\t</table></td></tr>\n";
        $content = $content . "\t</table>\n";
        $content = $content . "</form>\n";
    }

    // always return
    return $content;

}

function wpendeavouresu_allexplorers($atts = [], $content = null) {
    global  $wpdb;

    // Prepare the content to replace the tag
    // Table definition
    $content = "<div class=\"text-center\"><table class=\"table\">\n";
    // Table head
    $content = $content . "\t<thead>\n\t\t<tr>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Explorer Name</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Type</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Status</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Nights Away</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Hikes</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Start date</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">End date</th>\n";
    $content = $content . "\t\t</tr>\n\t</thead>\n";

    // Table content
    // $sql = "SELECT E.ExpID, U.display_name, exp1_exptypetypes.Description, S.Description, E.TotalNightsAway, E.TotalHikes, E.ExpDateStart, E.ExpDateEnd ";
    // $sql = $sql . "FROM " . $wpdb->base_prefix . "users U, exp1_explorers E, exp1_expstatus S, exp1_exptypetypes, exp1_exptypes ";
    // $sql = $sql . "WHERE U.ID = E.ExpWPID AND E.Deleted = 0 AND E.ExpStatusID = S.ExpStatusID AND exp1_exptypetypes.ExpTypeTypeID = exp1_exptypes.ExpTypeTypeID AND exp1_exptypes.ExpID = E.ExpID AND exp1_exptypes.DateEnd IS NULL ";
    // $sql = $sql . "ORDER BY U.display_name";
    $sql = "CALL GetAllExplorers()";
    // Flush the DB cache and run the query
    $wpdb->flush();
    $dbdata = $wpdb->get_results($sql, ARRAY_N);
    if (count($dbdata) > 0) {
        foreach ($dbdata as $row) {
            $content = $content . "\t\t<tr class=\"explorer\" data-toggle=\"modal\" data-target=\"#modalGetExplorer\" id=\"" . $row[0]. "\"><th scope=\"row\" class=\"text-left\">" . esc_html($row[1]) . "</th>";
            $content = $content . "<td>" . esc_html($row[2]) . "</td>";
            $content = $content . "<td>" . esc_html($row[3]) . "</td>";
            $content = $content . "<td>" . esc_html($row[4]) . "</td>";
            $content = $content . "<td>" . esc_html($row[5]) . "</td>";
            $content = $content . "<td>" . esc_html($row[6]) . "</td>";
            if ($row[7] == "") {
                $content = $content . "<td>Current</td></tr>\n";
            } else {
                $content = $content . "<td>" . esc_html($row[7]) . "</td></tr>\n";
            }
        }
        $wpdb->flush();
    } else {
        $content = $content . "\t\t<tr><td colspan=7 class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
    }
    // Close the table
    $content = $content . "</table></div>\n";
    $content = $content . "<div class=\"text-align-center text-center w-100\">\n";
    $content = $content . "\t<button type=\"button\" class=\"btn btn-primary btn-group-justified\" data-toggle=\"modal\" data-target=\"#modalAddExplorers\" id=\"btnAddExplorers\">Add explorers</button> \n";
    $content = $content . "\t<button type=\"button\" class=\"btn btn-primary btn-group-justified\" data-toggle=\"modal\" data-target=\"#modalAddEvent\" id=\"btnAddEventNA\">Add Night Aways</button> \n";
    $content = $content . "\t<button type=\"button\" class=\"btn btn-primary btn-group-justified\" data-toggle=\"modal\" data-target=\"#modalAddEvent\" id=\"btnAddEventHike\">Add Hikes</button> \n";
    $content = $content . "\t<button type=\"button\" class=\"btn btn-primary btn-group-justified\" data-toggle=\"modal\" data-target=\"#modalAddEvent\" id=\"btnAddEventReqt\">Add Badges</button> \n";
    $content = $content . "</div>\n";
   
    $content = $content . "<div class=\"modal fade\" id=\"modalAddExplorers\" tabindex=\"-1\" aria-labelledby=\"modalAddExplorersLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog  modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalAddExplorersLabel\">Add explorers from user list</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalAddExplorersBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" id=\"btnCloseExplorers\" data-dismiss=\"modal\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnSaveExplorers\">Save changes</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    $content = $content . "<div class=\"modal fade\" id=\"modalAddEvent\" tabindex=\"-1\" aria-labelledby=\"modalAddEventLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog  modal-lg modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalAddEventLabel\">Retrieving data</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalAddEventBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" id=\"btnCloseEvent\" data-dismiss=\"modal\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnSaveEvent\">Save changes</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";
    
    $content = $content . "<div class=\"modal fade\" id=\"modalGetExplorer\" tabindex=\"-1\" aria-labelledby=\"modalGetExplorerLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog modal-lg modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalGetExplorerLabel\">Retrieving explorer name</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalGetExplorerBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\" data-target=\"#modalAddExplorers\" id=\"btnExplorerClose\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnEditStatus\">Edit Status</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnEditLinks\">Edit Links</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnEditType\">Edit Type</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnAddNA\">Nights Away</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnAddHike\">Hikes</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\" id=\"btnAddBadge\">Badges & Awards</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    $content = $content . "\t\t\t\t<div class=\"modal fade\" id=\"modalUpdateExplorer\" tabindex=\"-1\" aria-labelledby=\"modalUpdateExplorerLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t\t\t\t<div class=\"modal-dialog modal-lg modal-dialog-centered\">\n\t\t\t\t<div class=\"modal-content\">\n\t\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalUpdateExplorerLabel\">Retrieving explorer name</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-body\" id=\"modalUpdateExplorerBody\">\n";
    $content = $content . "\t\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnUpdateSave\" name=\"btnUpdateSave\">Save changes</button>\n";
    $content = $content . "\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t</div>\n";

    $content = $content . "<div class=\"modal fade\" id=\"modalUpdateEvent\" tabindex=\"-1\" aria-labelledby=\"modalUpdateEventLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog  modal-lg modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalUpdateEventLabel\">Retrieving data</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalUpdateEventBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" id=\"btnCloseEvent\" data-dismiss=\"modal\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnUpdateEvent\" name=\"btnUpdateEvent\">Save changes</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    // always return
    return $content;
}

function wpendeavouresu_getnonexpusers() {
    global  $wpdb;

    // Handle the ajax request
    check_ajax_referer('wpendeavouresu_getnonexpusers');

    // Flush the DB cache and run the query
    $wpdb->flush();
    $sql = "CALL GetNonExplorerUsers()";
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
    $sql = "SELECT ExpTypeTypeID, Description FROM exp1_exptypetypes WHERE Deleted = 0 ORDER BY Description";
    $dbdata = $wpdb->get_results($sql, ARRAY_N);
    $typesdata = array();
    if (count($dbdata) > 0) {
        foreach($dbdata as $dbrow) {
            $typesdata[] = array('TypeID' => esc_html($dbrow[0]), 'Description' => esc_html($dbrow[1]));
        }
    }
    $wpdb->flush();
    $content['ExpTypesNo'] = count($typesdata);
    $content['ExpTypes'] = $typesdata;

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
        if ($_POST['rdoType'] != null) {
            $rdoType = $_POST['rdoType'];
        } else {
            $rdoType = 0;
        }
        if ($_POST['dateFrom'] != null) {
            $dateFrom = $_POST['dateFrom'];
        } else {
            $dateFrom = "CURRENT_DATE";
        }
        $wpdb->flush();
        foreach ($newexp as $explorer) {
            switch ($explorer['name']) {
                case "rdoType":
                case "dateFrom":
                    // Ignore
                    break;
                default:
                    if (strlen($explorer['name']) < 4) break;
                    $sql = "CALL AddExplorer(" . $explorer['value'] . ", " . $rdoType . ", '" . $dateFrom . "')";
                    $wpdb->query($sql);
                    $wpdb->flush();
                    $dbrecords++;
                    break;
            }
        }
    }
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

        $sql = "SELECT ExpHikeID, Description, HikeDays, DateStart, DateEnd FROM exp1_exphikes WHERE ExpID = " .$expID . " ORDER BY DateStart DESC";
        $hikedata = $wpdb->get_results($sql, ARRAY_N);
        $exphikes = array();
        if (count($hikedata) > 0) {
            foreach($hikedata as $hike) {
                $exphikes[] = array('ExpHikeID' => esc_html($hike[0]), 'Description' => esc_html($hike[1]), 'HikeDays' => esc_html($hike[2]), 'DateStart' => esc_html($hike[3]), 'DateEnd' => esc_html($hike[4]));
            }
        }
        $content['HikeNo'] = count($exphikes);
        $wpdb->flush();

        $sql = "SELECT ExpNightAwayID, DateStart, DateEnd, Description, NALocation, NADays FROM exp1_expna WHERE ExpID = " . $expID . " ORDER BY DateStart DESC";
        $nadata = $wpdb->get_results($sql, ARRAY_N);
        $expnas = array();
        if (count($nadata) > 0) {
            foreach($nadata as $expna) {
                $expnas[] = array('ExpNAID' => esc_html($expna[0]), 'DateStart' => esc_html($expna[1]), 'DateEnd' => esc_html($expna[2]), 'Description' => esc_html($expna[3]), 'NALocation' => esc_html($expna[4]), 'NADays' => esc_html($expna[5]));
            }
        }
        $content['NANo'] = count($expnas);
        $wpdb->flush();

        $sql = "SELECT E.ExpTypeID, T.Description, E.DateStart FROM exp1_exptypes E, exp1_exptypetypes T ";
        $sql = $sql . "WHERE E.ExpID = " . $expID . " AND E.ExpTypeTypeID = T.ExpTypeTypeID AND E.DateEnd IS NULL";
        $typedata = $wpdb->get_row($sql, ARRAY_N);
        if (! empty($typedata)) {
            $content['ExpTypeID'] = esc_html($typedata[0]);
            $content['ExpType'] = esc_html($typedata[1]);
            $content['ExpTypeDateStart'] = esc_html($typedata[2]);
        } else {
            $content['ExpTypeID'] = esc_html(0);
            $content['ExpType'] = esc_html('Type not set');
            $content['ExpTypeDateStart'] = esc_html('Date not set');
        }
        $wpdb->flush();
        $sql = "SELECT B.BadgeID, B.IconPath, CONCAT(B.Name, ' (', S.Description, ', ', T.Description, ')'), B.Description, E.ExpBadgeID, E.DateStart, E.DateEnd  ";
        $sql = $sql . "FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T, exp1_expbadges E ";
        $sql = $sql . "WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID AND B.BadgeID = E.BadgeID AND E.ExpID = " . $expID . " ";
        $sql = $sql . "ORDER BY B.BadgeTypeID, B.Description";
        $badgedata = $wpdb->get_results($sql, ARRAY_N);
        $expbadges = array();
        if (count($badgedata) > 0) {
            foreach($badgedata as $badge) {
                $expbadges[] = array('BadgeID' => esc_html($badge[0]), 'IconPath' => esc_html($badge[1]),
                                    'Description' => esc_html($badge[2]), 'BadgeDescription' => esc_html($badge[3]),
                                    'ExpBadgeID' => esc_html($badge[4]), 'DateStart' => esc_html($badge[5]), 'DateEnd' => esc_html($badge[6]) );
            }
        }
        $content['BadgesNo'] = count($expbadges);
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
                if ($dbdata != null) {
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
            case "EditLinks":
                $sql = "SELECT U.ID, U.display_name FROM " . $wpdb->base_prefix . "users U WHERE U.ID NOT IN (SELECT ExpWPID FROM exp1_explorers) ORDER BY U.display_name";
                $dbdata = $wpdb->get_results($sql, ARRAY_N, 0);
                $wpdb->flush();               
                if ($dbdata != null) {
                    $content['Parents'] = $dbdata[0];
                    $parents = array();
                    foreach ($dbdata as $dbrow) {
                        $parents[] = array('ID' => $dbrow[0], 'Description' => $dbrow[1]);
                    }
                    $content['Parents'] = $parents;
                    $content['ParentsNo'] = count($parents);
                    $wpdb->flush();
                } else {
                    $content['ParentsNo'] = 0;
                }
                $sql = "SELECT ExpWPPID1, ExpWPPID2, ExpWPPID3 FROM exp1_explorers WHERE ExpID = " . $expID;
                $dbdata = $wpdb->get_row($sql, ARRAY_N);
                $wpdb->flush();               
                if (! empty($dbdata)) {
                    $content['Link1ID'] = $dbdata[0];
                    $content['Link2ID'] = $dbdata[1];
                    $content['Link3ID'] = $dbdata[2];
                }
                break;
            case "EditType":
                $sql = "SELECT ExpTypeTypeID FROM exp1_exptypes WHERE ExpID = " . $expID . " AND DateEnd IS NULL";
                $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
                if ($dbdata != null) {
                    $content['ExpTypeID'] = $dbdata[0];
                    $wpdb->flush();
                }                    
                $sql = "SELECT ExpTypeTypeID, Description FROM exp1_exptypetypes WHERE Deleted = 0 ORDER BY Description";
                $dbdata = $wpdb->get_results($sql, ARRAY_N);
                $typesdata = array();
                if (count($dbdata) > 0) {
                    foreach($dbdata as $dbrow) {
                        $typesdata[] = array('TypeID' => esc_html($dbrow[0]), 'Description' => esc_html($dbrow[1]));
                    }
                }
                $content['ExpTypesNo'] = count($typesdata);
                $content['ExpTypes'] = $typesdata;
                break;
            case "AddBadge":
                $sql = "SELECT B.BadgeID, CONCAT(B.Name, ' (', S.Description, ', ', T.Description, ')'), B.Description ";
                $sql = $sql . "FROM exp1_badges B, exp1_badgestatus S, exp1_badgetypes T ";
                $sql = $sql . "WHERE T.BadgeTypeID = B.BadgeTypeID AND S.BadgeStatusID = B.BadgeStatusID ";
                $sql = $sql . "ORDER BY T.Description, B.Description";
                $badges = array();
                $dbdata = $wpdb->get_results($sql, ARRAY_N);
                if ($dbdata != null) {
                    foreach($dbdata as $dbrow) {
                        $badges[] = array( "BadgeID" => $dbrow[0], "Description" => esc_html($dbrow[1]));
                    }
                }                    
                $wpdb->flush();
                $content['BadgesNo'] = count($badges);
                $content['Badges'] = $badges;
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
    
    // check_ajax_referer('wpendeavouresu_updateexplorerdata');
    
    // Declare the return data
    $content = array();
    if ((! empty($_POST['dbdata'])) && (! empty($_POST['actiontype']))) {
        $newdata = $_POST['dbdata'];
        foreach ($newdata as $dbdata) {
            switch ($dbdata['name']) {
                case "ExplorerID":
                    $expID = $dbdata['value'];
                    break;
                case "ExplorerName":
                    $expName = $dbdata['value'];
                    break;
                case "UpdateType":
                    $updatetype = $dbdata['value'];
                    break;
                default:
                    break;
            }
        }
        // echo "Action: " . $_POST['actiontype'] . " for ID: " . $expID . ". ";

        if (empty($expID)) {
            $content['success'] = 0;
            $content['error'] = "ExplorerID is empty";
            wp_send_json($content);
            wp_die();
        }
     
        $wpdb->flush();
        switch ($_POST['actiontype']) {
            case "EditStatus":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "rdoStatus":
                            $rdoStatus = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                $sql = "UPDATE exp1_explorers SET ExpStatusID = " . intval($rdoStatus) . " WHERE ExpID = " . intval($expID);
                // query returns the number of affected rows - ignored.
                $dbresult = $wpdb->query($sql);
                if ($dbresult > 0) {
                    $content['success'] = 1;
                    $content['error'] = "";
                } else {
                    $content['success'] = 0;
                }
                break;
            case "EditType":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "rdoType":
                            $rdoType = $dbdata['value'];
                            break;
                        case "dateFrom":
                            $dateFrom = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                // Close previous type. This may or may not update anything.
                $sql = "UPDATE exp1_exptypes SET DateEnd = '" . $dateFrom . "' WHERE ExpID = " . intval($expID) . " AND DateEnd IS NULL";
                $dbresult = $wpdb->query($sql);
                $wpdb->flush();

                $sql = "INSERT INTO exp1_exptypes (ExpID, ExpTypeTypeID, DateStart) VALUES (" . intval($expID) . ", " . intval($rdoType) . ", '" . $dateFrom . "')";
                // query returns the number of affected rows - ignored.
                $dbresult = $wpdb->query($sql);
                if ($dbresult > 0) {
                    $content['success'] = 1;
                } else {
                    $content['success'] = 0;
                }
                break;
            case "EditLinks":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "selLink1":
                            $selLink1 = $dbdata['value'];
                            break;
                        case "selLink2":
                            $selLink2 = $dbdata['value'];
                            break;
                        case "selLink3":
                            $selLink3 = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                $sql = "UPDATE exp1_explorers SET ExpWPPID1 = ";
                if ($selLink1 > 0) {
                    $sql = $sql . $selLink1;
                } else {
                    $sql = $sql . "NULL";
                }
                $sql = $sql . " WHERE ExpID = " . $expID;
                $dbresult = $wpdb->query($sql);
                $wpdb->flush();
                if ($dbresult > 0) {
                    $content['success1'] = 1;
                } else {
                    $content['success1'] = 0;
                }
                $sql = "UPDATE exp1_explorers SET ExpWPPID2 = ";
                if ($selLink2 > 0) {
                    $sql = $sql . $selLink2;
                } else {
                    $sql = $sql . "NULL";
                }
                $sql = $sql . " WHERE ExpID = " . $expID;
                $dbresult = $wpdb->query($sql);
                $wpdb->flush();
                if ($dbresult > 0) {
                    $content['success2'] = 1;
                } else {
                    $content['success2'] = 0;
                }
                $sql = "UPDATE exp1_explorers SET ExpWPPID3 = ";
                if ($selLink3 > 0) {
                    $sql = $sql . $selLink3;
                } else {
                    $sql = $sql . "NULL";
                }
                $sql = $sql . " WHERE ExpID = " . $expID;
                $dbresult = $wpdb->query($sql);
                $wpdb->flush();
                if ($dbresult > 0) {
                    $content['success3'] = 1;
                } else {
                    $content['success3'] = 0;
                }
                if (($content['success1'] == 0) || ($content['success2'] == 0) || ($content['success3'] == 0)) {
                    $content['success'] = 0;
                } else {
                    $content['success'] = 1;
                }
                $content['success'] = 1;
                break;
            case "AddNA":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "txtDays":
                            $txtDays = $dbdata['value'];
                            break;
                        case "txtDescription":
                            $txtDescription = $dbdata['value'];
                            break;
                        case "txtLocation":
                            $txtLocation = $dbdata['value'];
                            break;
                        case "dateStart":
                            $dateStart = $dbdata['value'];
                            break;
                        case "dateEnd":
                            $dateEnd = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                $sql = "CALL AddNightAway(" . intval($expID) . ", " . intval($txtDays) . ", '" . $txtDescription . "', '" . $txtLocation . "', '" . $dateStart . "', '" . $dateEnd . "')";
                $dbresult = $wpdb->query($sql);
                $wpdb->flush();
                if ($dbresult > 0) {
                    $content['success'] = 1;
                } else {
                    $content['success'] = 0;
                }
                break;
            case "AddHike":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "txtDescription":
                            $txtDescription = $dbdata['value'];
                            break;
                        case "txtHikeDays":
                            $txtHikeDays = $dbdata['value'];
                            break;
                        case "dateStart":
                            $dateStart = $dbdata['value'];
                            break;
                        case "dateEnd":
                            $dateEnd = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                $sql = "CALL AddHike(" . intval($expID) . ", '" . $txtDescription . "', " . $txtHikeDays . ", '" . $dateStart . "', '" . $dateEnd . "')";
                $dbresult = $wpdb->query($sql);
                $wpdb->flush();
                if ($dbresult > 0) {
                    $content['success'] = 1;
                } else {
                    $content['success'] = 0;
                }
                break;
            case "AddBadge":
            case "UpdateBadge":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "selBadge":
                        case "BadgeID":
                            $BadgeID = $dbdata['value'];
                            break;
                        case "ExpBadgeID":
                            $ExpBadgeID = $dbdata['value'];
                            break;
                        case "ExplorerID":
                            $ExpID = $dbdata['value'];
                            break;
                        case "dateStart":
                            $dateStart = $dbdata['value'];
                            break;
                        case "dateEnd":
                            $dateEnd = $dbdata['value'];
                            break;
                        case "UpdateType":
                        default:
                            break;
                    }
                }
                $content['success'] = 0;
                $content['Error'] = "Unknown error.";
                if (empty($dateStart)) {
                    $dateStart = "CURRENT_DATE";
                } else {
                    $dateStart = "'" . $dateStart . "'";
                }
                if (! empty($dateEnd)) {
                    $dateEnd = "'" . $dateEnd . "'";
                }
                if (empty($ExpID)) {
                    $content['Error'] = "Empty ExpBadgeID or ExpID.";
                } else {
                    if (empty($ExpBadgeID)) {
                        if (! empty($dateEnd)) {
                            $sql = "INSERT INTO exp1_expbadges (ExpID, BadgeID, DateStart, DateEnd) VALUES (" . intval($ExpID) . ", " . intval($BadgeID) . ", " . $dateStart . ", " . $dateEnd . ")";
                        } else {
                            $sql = "INSERT INTO exp1_expbadges (ExpID, BadgeID, DateStart, DateEnd) VALUES (" . intval($ExpID) . ", " . intval($BadgeID) . ", " . $dateStart . ", NULL)";
                        }
                        $content["SQL_INSERT"] = $sql;
                        $wpdb->query($sql);
                        $wpdb->flush();
                        $sql = "SELECT ExpBadgeID FROM exp1_expbadges WHERE ExpID = " . intval($ExpID) . " AND BadgeID = " . intval($BadgeID);
                        $content["SQL_SELECT"] = $sql;
                        $dbresult = $wpdb->get_results($sql, ARRAY_N);
                        if (count($dbresult) > 0) {
                            $ExpBadgeID = $dbresult[0][0];
                            $content["ExpBadgeID"] = "ExpBadgeID = " . $ExpBadgeID . ".";
                        } else {
                            $content["ExpBadgeID"] = "ExpBadgeID not found in database.";
                            $content['Error'] = "ExpBadgeID not found in database.";
                        }
                    }
                    if (! empty($ExpBadgeID)) {
                        if ($dateStart == null) $dateStart = "CURRENT_DATE";
                        foreach ($newdata as $dbdata) {
                            switch ($dbdata['name']) {
                                case "AddEventType":
                                case "UpdateType":
                                case "selBadge":
                                case "AddEventType":
                                case "BadgeID":
                                case "ExpBadgeID":
                                case "ExplorerID":
                                case "dateStart":
                                case "dateEnd":
                                    // Ignore
                                    break;
                                default:
                                    if (strlen($dbdata['name']) < 4) break;
                                    if (intval($dbdata['value']) == 0) break;
                                    if (! empty($dateEnd)) {
                                        $sql = "CALL AddBadgeReqt(" . $ExpBadgeID . ", "  . $ExpID . ", ". intval($dbdata['value']) . ", " . $dateStart . ", " . $dateEnd . ")";
                                    } else {
                                        $sql = "CALL AddBadgeReqt(" . $ExpBadgeID . ", "  . $ExpID . ", ". intval($dbdata['value']) . ", " . $dateStart . ", NULL)";
                                    }
                                    $content["SQL_" . $dbdata['name']] = $sql;
                                    // Query returns either 0 (already exists) or the new row ID.
                                    $dbresult = $wpdb->query($sql);
                                    $wpdb->flush();
                                    break;
                            }
                        }
                        $content['success'] = 1;
                        $content['Error'] = "";
                    }
                    if (! empty($ExpBadgeID)) {
                        if (! empty($dateEnd)) {
                            $sql = "UPDATE exp1_expbadges SET DateStart = " . $dateStart . ", DateEnd = " . $dateEnd . " WHERE ExpBadgeID = " . $ExpBadgeID;
                        } else {
                            $sql = "UPDATE exp1_expbadges SET DateStart = " . $dateStart . ", DateEnd = NULL WHERE ExpBadgeID = " . $ExpBadgeID;
                        }
                        $content["SQL_UPDATE"] = $sql;
                        $wpdb->query($sql);
                        $wpdb->flush();        
                    }
                }
                break;
           default:
                break;
        }
    }
    $wpdb->flush();
    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_geteventdata() {
    global  $wpdb;
    
    $newdata = array();
    $wpdb->flush();
    $sql = "CALL GetAllExplorers()";
    $dbdata = $wpdb->get_results($sql, ARRAY_N);
    if (count($dbdata) > 0) {
        foreach ($dbdata as $dbrow) {
            $explorers[] = array("ExpID" => $dbrow[0], "ExpName" => esc_html($dbrow[1]), "ExpType" => esc_html($dbrow[2]), "ExpStatus" => esc_html($dbrow[3]));
        }
        $newdata["ExpNo"] = count($dbdata);
        $newdata["Explorers"] = $explorers;
    } else {
        $newdata["ExpNo"] = 0;
    }
    $wpdb->flush();
    $newdata['actiontype'] = $_GET['actiontype'];
    switch ($_GET['actiontype']) {
        case "AddEventNA":
        case "AddEventHike":
            break;
        case "AddEventBadge":
            $sql = "CALL GetAllBadges()";
            $badges = array();
            $dbdata = $wpdb->get_results($sql, ARRAY_N);
            if ($dbdata != null) {
                foreach($dbdata as $dbrow) {
                    $badges[] = array( "BadgeID" => $dbrow[0], "Label" => esc_html($dbrow[1]), "Description" => esc_html($dbrow[2]));
                }
            }                    
            $wpdb->flush();
            $newdata['BadgesNo'] = count($badges);
            $newdata['Badges'] = $badges;
            break;
        case "AddEventReqt":
            $sql = "CALL GetAllBadges()";
            $badges = array();
            $dbdata = $wpdb->get_results($sql, ARRAY_N);
            if ($dbdata != null) {
                foreach($dbdata as $dbrow) {
                    $badges[] = array( "BadgeID" => $dbrow[0], "Label" => esc_html($dbrow[1]), "Description" => esc_html($dbrow[2]));
                }
            }                    
            $wpdb->flush();
            $newdata['BadgesNo'] = count($badges);
            $newdata['Badges'] = $badges;
            break;  
        default:
            break;
    }
    wp_send_json($newdata);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_getexpeventdata() {
    global  $wpdb;

    check_ajax_referer('wpendeavouresu_getexpeventdata');

    $content['success'] = 0;
    $content['error'] = "actiontype is empty";
    if (! empty($_GET['actiontype'])) {
        switch ($_GET['actiontype']) {
            case "getbadgedata":
                if (empty($_GET['ExpBadgeID'])) {
                    $content['error'] = "ExpBadgeID is empty";
                    break;
                }
                $ExpBadgeID = $_GET['ExpBadgeID'];
                $content["ExpBadgeID"] = $ExpBadgeID;
                $wpdb->flush();               
                $sql = "SELECT X.ExpID, U.display_name, B.Description, X.BadgeID, X.DateStart, X.DateEnd FROM exp1_explorers E, edvr1_users U, exp1_expbadges X, exp1_badges B WHERE E.ExpWPID = U.ID AND E.ExpID = X.ExpID AND X.BadgeID = B.BadgeID AND X.ExpBadgeID = " . $ExpBadgeID;
                $content["SQL1"] = $sql;
                $dbdata = $wpdb->get_results($sql, ARRAY_N);
                $expbadgreqts = array();
                $badgereqts = array();
                $content['error'] = "Could not retrieve badge and explorer summary.";
                if (count($dbdata) > 0) {
                    foreach ($dbdata as $dbrow) {
                        $content["ExpID"] = $dbrow[0];
                        $content["ExpName"] = esc_html($dbrow[1]);
                        $content["BadgeName"] = esc_html($dbrow[2]);
                        $content["BadgeID"] = $dbrow[3];
                        $BadgeID = $dbrow[3];
                        $content["ExpBadgeStart"] = esc_html($dbrow[4]);
                        $content["ExpBadgeEnd"] = esc_html($dbrow[5]);
                    }
                    $wpdb->flush();               
                    $sql = "SELECT ExpBadgeReqtID, BadgeReqtID, DateStart, DateEnd FROM exp1_expbadgereqts WHERE ExpBadgeID = " . $ExpBadgeID;
                    $content["SQL2"] = $sql;
                    $dbdata = $wpdb->get_results($sql, ARRAY_N);
                    if (count($dbdata) > 0) {
                        foreach ($dbdata as $dbrow) {
                            $expbadgreqts[] = array("ExpBadgeReqtID" => $dbrow[0], "BadgeReqtID" => $dbrow[1], "DateStart" => $dbrow[2], "DateEnd" => $dbrow[3]);
                        }
                    }
                    $wpdb->flush();               
                    $sql = "SELECT BadgeReqtID, Description FROM exp1_badgereqts WHERE BadgeID = " . $BadgeID;
                    $content["SQL3"] = $sql;
                    $dbdata = $wpdb->get_results($sql, ARRAY_N);
                    if (count($dbdata) > 0) {
                        foreach ($dbdata as $dbrow) {
                            $badgereqts[] = array("BadgeReqtID" => $dbrow[0], "ReqtDesc" => $dbrow[1]);
                        }
                    }
                    $wpdb->flush();               
                    $content["ExpBadgeReqtsNo"] = count($expbadgreqts);
                    $content["ExpBadgeReqts"] = $expbadgreqts;
                    $content["BadgeReqtsNo"] = count($badgereqts);
                    $content["BadgeReqts"] = $badgereqts;
                    $content['success'] = 1;
                    $content['error'] = "No error";
                }
                break;
            default:
                break;
        }
    }

    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_addeventdata() {
    global  $wpdb;
    
    // check_ajax_referer('wpendeavouresu_updateexplorerdata');
    
    // Declare the return data
    $content = array();
    if ((! empty($_POST['dbdata'])) && (! empty($_POST['actiontype']))) {
        $newdata = $_POST['dbdata'];
        $wpdb->flush();
        $dbrecords = 0;
        switch ($_POST['actiontype']) {
            case "AddEventNA":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "txtDays":
                            $txtDays = $dbdata['value'];
                            break;
                        case "txtDescription":
                            $txtDescription = $dbdata['value'];
                            break;
                        case "txtLocation":
                            $txtLocation = $dbdata['value'];
                            break;
                        case "dateStart":
                            $dateStart = $dbdata['value'];
                            break;
                        case "dateEnd":
                            $dateEnd = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                if ($dateStart == null) $dateStart = "CURRENT_DATE";
                if ($dateEnd == null) $dateEnd = "CURRENT_DATE";

                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "AddEventType":
                        case "txtDays":
                        case "txtDescription":
                        case "txtLocation":
                        case "dateStart":
                        case "dateEnd":
                            // Ignore
                            break;
                        default:
                            if (strlen($dbdata['name']) < 4) break;
                            if (intval($dbdata['value']) == 0) break;
                            $sql = "CALL AddNightAway(" . intval($dbdata['value']) . ", " . intval($txtDays) . ", '" . $txtDescription . "', '" . $txtLocation . "', '" . $dateStart . "', '" . $dateEnd . "')";
                            $wpdb->query($sql);
                            $wpdb->flush();
                            $dbrecords++;
                            break;
                    }
                }
                if ($dbrecords > 0) {
                    $content['success'] = 1;
                } else {
                    $content['success'] = 0;
                }
                break;
            case "AddEventHike":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "txtDescription":
                            $txtDescription = $dbdata['value'];
                            break;
                        case "txtHikeDays":
                            $txtHikeDays = $dbdata['value'];
                            break;
                        case "dateStart":
                            if (! empty($dbdata['value'])) {
                                $dateStart = "'" . $dbdata['value'] . "'";
                            }
                            break;
                        case "dateEnd":
                            if (! empty($dbdata['value'])) {
                                $dateEnd = "'" . $dbdata['value'] . "'";
                            }
                            break;
                        default:
                            break;
                    }
                }
                if ($dateStart == null) $dateStart = "CURRENT_DATE";
                if (($dateEnd == null) || empty($dateEnd)) $dateEnd = "CURRENT_DATE";
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "AddEventType":
                        case "txtHikeDays":
                        case "txtDescription":
                        case "dateStart":
                        case "dateEnd":
                            // Ignore
                            break;
                        default:
                            if (strlen($dbdata['name']) < 4) break;
                            if (intval($dbdata['value']) == 0) break;
                            $sql = "CALL AddHike(" . intval($dbdata['value']) . ", '" . $txtDescription . "', " . intval($txtHikeDays) . ", " . $dateStart . ", " . $dateEnd . ")";
                            $wpdb->query($sql);
                            $wpdb->flush();
                            $dbrecords++;
                            break;
                    }
                }
                if ($dbrecords > 0) {
                    $content['success'] = 1;
                } else {
                    $content['success'] = 0;
                }
                break;
            case "AddEventBadge":
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "selBadge":
                            $selBadge = $dbdata['value'];
                            break;
                        case "dateStart":
                            $dateStart = $dbdata['value'];
                            break;
                        case "dateEnd":
                            $dateEnd = $dbdata['value'];
                            break;
                        default:
                            break;
                    }
                }
                if ($dateStart == null) $dateStart = "CURRENT_DATE";
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "AddEventType":
                        case "selBadge":
                        case "dateStart":
                        case "dateEnd":
                            // Ignore
                            break;
                        default:
                            if (strlen($dbdata['name']) < 4) break;
                            if (intval($dbdata['value']) == 0) break;

                            if (! empty($dateEnd)) {
                                $sql = "INSERT INTO exp1_expbadges (ExpID, BadgeID, DateStart, DateEnd) VALUES (" . intval($dbdata['value']) . ", " . intval($selBadge) . ", '" . $dateStart . "', '" . $dateEnd . "')";
                            } else {
                                $sql = "INSERT INTO exp1_expbadges (ExpID, BadgeID, DateStart) VALUES (" . intval($dbdata['value']) . ", " . intval($selBadge) . ", '" . $dateStart . "')";
                            }
                            $wpdb->query($sql);
                            $wpdb->flush();
                            $dbrecords++;
                            break;
                    }
                }
                if ($dbrecords > 0) {
                    $content['success'] = 1;
                } else {
                    $content['success'] = 0;
                }
                break;
            case "AddEventReqt":
                $expid = array();
                $reqtid = array();
                foreach ($newdata as $dbdata) {
                    switch ($dbdata['name']) {
                        case "selBadgeReqt":
                            $selBadge = $dbdata['value'];
                            break;
                        case "dateStart":
                            $dateStart = $dbdata['value'];
                            break;
                        case "dateEnd":
                            $dateEnd = $dbdata['value'];
                            break;
                        case "AddEventType":
                            // ignore this
                            break;
                        default:
                            if (strncmp($dbdata['name'], "chk", 3) == 0) {
                                $expid[] = array('name' => $dbdata['name'], 'value' => $dbdata['value']);
                            }
                            if (strncmp($dbdata['name'], "reqt", 4) == 0) {
                                $reqtids[] = array('name' => $dbdata['name'], 'value' => $dbdata['value']);
                            }
                            break;
                    }
                }
                if ((count($expid) == 0) || (count($reqtids) == 0)) break;
                if ($dateStart == null) $dateStart = "CURRENT_DATE";
                $dbrecords = 0;
                foreach ($expid as $explorer) {
                    if (intval($explorer['value']) == 0) break;
                    // Check that there is an expbadges record for this explorer and this badge. If there isn't one, create one
                    $sql = "CALL GetExpBadgeID (" . intval($explorer['value']) . ", " . intval($selBadge) . ", '" . $dateStart . "')";
                    $content["SQLReqtID"] = $sql;
                    $dbresult = $wpdb->get_results($sql, ARRAY_N);
                    $expbadgeID = $dbresult[0][0];
                    foreach ($reqtids as $reqtid) {
                        if (! empty($dateEnd)) {
                            $sql = "INSERT INTO exp1_expbadgereqts (ExpID, ExpBadgeID, BadgeReqtID, DateStart, DateEnd) VALUES (" . intval($explorer['value']) . ", " . $expbadgeID . ", " . $reqtid['value'] . ", '" . $dateStart . "', '" . $dateEnd . "')";
                        } else {
                            $sql = "INSERT INTO exp1_expbadgereqts (ExpID, ExpBadgeID, BadgeReqtID, DateStart) VALUES (" . intval($explorer['value']) . ", " . $expbadgeID . ", " . $reqtid['value'] . ", '" . $dateStart . "')";
                        }
                        $content['SQL' . $reqtid['value']] = $sql;
                        $wpdb->query($sql);
                        $wpdb->flush();
                        $dbrecords++;
                    }
                }
                if ($dbrecords > 0) {
                    $content['success'] = $dbrecords;
                } else {
                    $content['success'] = 0;
                }
                break;
            default:
                break;
        }
    }
    $wpdb->flush();
    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_getbadgereqts() {
    global  $wpdb;

    $wpdb->flush();
    $sql = "SELECT BadgeReqtID, Description FROM exp1_badgereqts WHERE Deleted = 0 AND BadgeReqtStatusID = 1 AND BadgeID = " . $_GET['actiontype'];
    $dbdata = $wpdb->get_results($sql, ARRAY_N);
    $reqts = array();
    if (count($dbdata) > 0) {
        foreach($dbdata as $dbrow) {
            $reqts[] = array('reqtid' => esc_html($dbrow[0]), 'reqtdesc' => esc_html($dbrow[1]));
        }
    }
    $wpdb->flush();
    $content['reqtsno'] = count($reqts);
    $content['reqts'] = $reqts;

    // Send the data back
    wp_send_json($content);
   
    wp_die(); // All ajax handlers die when finished
}

function wpendeavouresu_enqueuescript( $hook ) {
    // If this is not a page, return
    // Otherwise, enqueue the script
    // wp_enqueue_script( 'ajax-getnonexpusers', plugins_url('js/wp-endeavouresu-getnonexpusers.js', __FILE__ ), array('jquery') );
    // wp_enqueue_script( 'ajax-savenewexplorers', plugins_url('js/wp-endeavouresu-savenewexplorers.js', __FILE__ ), array('jquery') );
    // wp_enqueue_script( 'ajax-getexplorer', plugins_url('js/wp-endeavouresu-getexplorer.js', __FILE__ ), array('jquery') );
    // wp_enqueue_script( 'ajax-getexplorerdata', plugins_url('js/wp-endeavouresu-getexplorerdata.js', __FILE__ ), array('jquery') );
    // wp_enqueue_script( 'ajax-updateexplorerdata', plugins_url('js/wp-endeavouresu-updateexplorerdata.js', __FILE__ ), array('jquery') );
    wp_enqueue_script( 'ajax-endeavouresu', plugins_url('js/wp-endeavouresu-endeavouresu.js', __FILE__ ), array('jquery') );
    $nonce_getnonexpusers = wp_create_nonce( 'wpendeavouresu_getnonexpusers' );
    $nonce_savenewexplorers = wp_create_nonce( 'wpendeavouresu_savenewexplorers' );
    $nonce_getexplorer = wp_create_nonce( 'wpendeavouresu_getexplorer' );
    $nonce_getexplorerdata = wp_create_nonce( 'wpendeavouresu_getexplorerdata' );
    $nonce_updateexplorerdata = wp_create_nonce( 'wpendeavouresu_updateexplorerdata' );
    $nonce_geteventdata = wp_create_nonce ('wpendeavouresu_geteventdata');
    $nonce_addeventdata = wp_create_nonce ('wpendeavouresu_addeventdata');
    $nonce_getbadgereqts = wp_create_nonce ('wpendeavouresu_getbadgereqts');
    $nonce_getexpeventdata = wp_create_nonce ('wpendeavouresu_getexpeventdata');
    $nonce_updateexpeventdata = wp_create_nonce ('wpendeavouresu_updateexpeventdata');
    wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_newexplorers', array(
       'ajax_url' => admin_url( 'admin-ajax.php' ),
       'nonce'    => $nonce_savenewexplorers,
    ) );
    wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_nonexpusers', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getnonexpusers,
     ) );
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_getexplorer', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getexplorer,
     ) );
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_getexplorerdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getexplorerdata,
     ) );
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_updateexplorerdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_updateexplorerdata,
     ) );
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_geteventdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_geteventdata,
     ) );
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_addeventdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_addeventdata,
     ) );
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_getbadgereqts', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getbadgereqts,
     ) ); 
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_getexpeventdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getexpeventdata,
     ) ); 
     wp_localize_script( 'ajax-endeavouresu', 'ajaxdata_updateexpeventdata', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_updateexpeventdata,
     ) ); 
}


register_activation_hook( __FILE__, 'wpendeavouresu_activate' );
register_deactivation_hook( __FILE__, 'wpendeavouresu_deactivate' );


// Shortcode to trigger the leader plugin from the page 
add_shortcode('EndeavourESU_AllExplorers', 'wpendeavouresu_allexplorers');

// Shortcode to trigger the explorer plugin from the page
add_shortcode('EndeavourESU_Explorer', 'wpendeavouresu_explorer');

// Shortcode to trigger the list explorers plugin from the page
add_shortcode('EndeavourESU_ListExplorers', 'wpendeavouresu_listexplorers');

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

// Add the handler for AJAX request to get event data 
add_action('wp_ajax_get_eventdata', 'wpendeavouresu_geteventdata');

// Add the handler for AJAX request to add event data 
add_action('wp_ajax_add_eventdata', 'wpendeavouresu_addeventdata');

// Add the handler for AJAX request to update event data 
add_action('wp_ajax_get_expeventdata', 'wpendeavouresu_getexpeventdata');

// Add the handler for AJAX request to update event data 
add_action('wp_ajax_update_expeventdata', 'wpendeavouresu_updateexpeventdata');

// Add the handler for AJAX request to get badge requirements 
add_action('wp_ajax_get_badgereqts', 'wpendeavouresu_getbadgereqts');
?>
