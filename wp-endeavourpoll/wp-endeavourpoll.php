<?php
/*
 * @link        http://endeavouresu.uk
 * @since       1.0.0
 * @package     wp-endeavourpoll
 *
 * @wordpress-plugin
 * Plugin Name: wp-endeavourpoll
 * Plugin URI:  
 * Requires at least: 5.5
 * Requires PHP: 7.4
 * Description: Plugin to create polls for Endeavour Explorer Scout Unit
 * Version:     1.0.0
 * Author:      Ausculta Ltd
 * Author URI:  http://ausculta.net
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-endeavourpoll
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

if ( !defined( 'WPENDEAVOURPOLL_VER' ) ) {
	define( 'WPENDEAVOURPOLL_VER', '1.0.0' );
}

/**
 * load textdomain
 *
 * @return void
 */
function wpendeavourpoll_textdomain() {
    load_plugin_textdomain( 'wpendeavourpoll', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function wpendeavourpoll_activate() {

}

function wpendeavourpoll_deactivate() {

}

function wpendeavourpoll_poll($atts = [], $content = null) {
    global  $wpdb;
    
    $content = "";
    if (count($atts) > 0) {
       // $content = var_dump($atts) . "<br>\n";
        if (! empty($atts['pollid'])) {
            $PollID = $atts['pollid'];
        } else {
            $content = $content . "<h5>Unknown poll reference.</h5>\n";
            return $content;
        }
    } else {
        $content = "<h5>Unknown poll reference (atts is empty).</h5>\n";
        return $content;
    }
    $sql = "SELECT P.PollID, P.PollTitle, P.PollDescription, P.DeadlineDate, P.DeadlineTime, P.NoOptions, P.PollTypeID, P.PollStatusID, S.Description FROM exp1_polls P, exp1_pollstatus S WHERE P.PollStatusID = S.PollStatusID AND P.Deleted = 0 AND PollID = " . $PollID;
    $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
    if (count($dbdata) > 0) {
        if ($dbdata[7] == 3) {
            $content = "<h5 class=\"text-center\">This poll has been deleted and is no longer valid.</h5>";
            return $content;
        }
        if ($dbdata[5] < 1) {
            $content = "<h5 class=\"text-center\">There are no valid reply options for this poll.</h5>";
            return $content;
        }
        
        $sql = "SELECT PollReplyID, ReplyValue, ReplyComment FROM exp1_pollreplies WHERE WPID = " . get_current_user_id() . " AND PollID = " . $PollID;
        $dbreply = $wpdb->get_row($sql, ARRAY_N, 0);
        if (count($dbreply) > 0) {
            $PollReplyID = $dbreply[0];
            $tokens = explode(";", $dbreply[1]);
        }

        $content = $content . "\t\t\t<form name=\"frmEditReply\" id=\"frmEditReply\" method=\"POST\" action=\"\">\n";
        $content = $content . "<input type=\"hidden\" id=\"PollID\" name=\"PollID\" value=\"" . $PollID . "\">";
        $content = $content . "<div class=\"text-center\"><h5>" . $dbdata[1] . " (" . $dbdata[8] . ")</h5></div>\n";
        $content = $content . "<div class=\"text-center\"><table class=\"table\">\n";
        $content = $content . "<tr><td colspan=2>" . $dbdata[2] . "</td></tr>\n";
        $content = $content . "<tr><td>Poll deadline:</td><td class=\"text-left\">" . $dbdata[3] . " - " . $dbdata[4] . "</td></tr>\n";
        $sql = "SELECT PollOptionID, Description FROM exp1_polloptions WHERE Deleted = 0 AND PollID = " . $PollID;
        $dboptions = $wpdb->get_results($sql, ARRAY_N);
        if (count($dboptions) < 1) {
            $content = "<h5 class=\"text-center\">There are no valid reply options for this poll.</h5>";
            return $content;
        }
        // $content = $content . var_dump($dboptions) . "\n";
        $content = $content . "<tr><td></td><td><table class=\"table-sm text-left\">\n";
        foreach ($dboptions as $dboption) {
            switch ($dbdata[6]) {
                case 1: // Checkboxes
                    $content = $content . "<tr><td><input type=\"checkbox\" value=\"" . $dboption[0] . "\" name=\"chkPollOption" . $dboption[0] . "\" id=\"chkPollOption" . $dboption[0] . "\"";
                    if (! empty($PollReplyID)) {
                        for ($i = 0 ; $i < count($token) ; $i++) {
                            if ($dboption[0] == intval($token[$i])) $content = $content . " checked";
                        }
                    }
                    $content = $content . "></td><td><label for=\"chkPollOption" . $dboption[0] . "\">" . $dboption[1] . "</label></td></tr>\n";
                    break;
                case 2: // Radio buttons
                    $content = $content . "<tr><td><input type=\"radio\" name=\"rdoPollOption\" value=\"" . $dboption[0] . "\" id=\"rdoPollOption" . $dboption[0] . "\"";                  
                    if (! empty($PollReplyID)) {
                        if ($dbreply[1] == $dboption[0]) $content = $content . " checked";
                    }
                    $content = $content . "></td><td><label for=\"rdoPollOption" . $dboption[0] . "\">" . $dboption[1] . "</label></td></tr>\n";
                    break;
                case 3: // Free form text
                    $content = $content . "<tr><td>" . $dbOption[1] . "</td><td><input type=\"text\" name=\"txtPollOption" . $dboption[0] . "\" id=\"txtPollOption" . $dboption[0] . "\" size=50></td></tr>";
                    break;
                default: // Unknown
                    break;
            }                        
        }
        $content = $content . "</table></td></tr>\n";
        $content = $content . "<tr><td>Notes / comments:</td><td class=\"text-left\"><input type=\"text\" id=\"txtPollComment\" name=\"txtPollComment\" size=50 ";
        if (! empty($PollReplyID)) {
            $content = $content . "value=\"" . $dbreply[2] . "\"";
        }
        $content = $content . "></td></tr>\n</table>\n";
        $content = $content . "<div class=\"text-align-center text-center w-100\">\n";
        $content = $content . "\t<button type=\"button\" class=\"btn btn-primary btn-group-justified\" id=\"btnSaveReply\">Save your reply</button> \n";
        $content = $content . "</div>\n";
        $content = $content . "</form>\n";
        $content = $content . "</div>\n";
    } else {
        $content = "<h5>Unknown poll reference.</h5>\n";
    }
    return $content;
}

function wpendeavourpoll_allpolls($atts = [], $content = null) {
    global  $wpdb;

    // Prepare the content to replace the tag
    // Table definition
    $content = "<div class=\"text-center\"><table class=\"table\">\n";
    // Table head
    $content = $content . "\t<thead>\n\t\t<tr>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Poll ID</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Title</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Description</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Deadline Date</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Deadline Time</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">No. options</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Type</th>\n";
    $content = $content . "\t\t\t<th scope=\"col\">Status</th>\n";

    $content = $content . "\t\t</tr>\n\t</thead>\n";

    // Table content
    $wpdb->flush();
    $sql = "CALL GetAllPolls()";
    // $sql = "SELECT P.PollID, P.PollTitle, P.PollDescription, P.DeadlineDate, P.DeadlineTime, P.NoOptions, T.Description, S.Description, P.Deleted ";
    // $sql = $sql . "FROM	exp1_polls P, exp1_polltypes T, exp1_pollstatus S ";
    // $sql = $sql . "WHERE	P.Deleted = 0 AND T.PollTypeID = P.PollTypeID AND S.PollStatusID = P.PollStatusID ";
    // $sql = $sql . "ORDER BY P.DeadlineDate, P.DeadlineTime DESC;";
    $dbdata = $wpdb->get_results($sql, ARRAY_N);
    if (count($dbdata) > 0) {
        foreach ($dbdata as $dbrow) {
            // $content = $content . "<tr><td colspan=8>SQL:" . $sql . "<br>" . var_dump($dbrow) . "</td></tr>";
            $content = $content . "\t\t<tr class=\"poll\" data-toggle=\"modal\" data-target=\"#modalEditPoll\" id=\"" . $dbrow[0]. "\"><th scope=\"row\" class=\"text-center\">" . esc_html($dbrow[0]) . "</th>";
            $content = $content . "<td class=\"text-left\">" . esc_html($dbrow[1]) . "</td>";
            $content = $content . "<td class=\"text-left\">" . esc_html($dbrow[2]) . "</td>";
            $content = $content . "<td class=\"text-center\">" . esc_html($dbrow[3]) . "</td>";
            $content = $content . "<td class=\"text-center\">" . esc_html($dbrow[4]) . "</td>";
            $content = $content . "<td class=\"text-center\">" . esc_html($dbrow[5]) . "</td>";
            $content = $content . "<td class=\"text-center\">" . esc_html($dbrow[6]) . "</td>";
            $content = $content . "<td class=\"text-center\">" . esc_html($dbrow[7]) . "</td></tr>\n";
        }
        $wpdb->flush();
    } else {
        $content = $content . "\t\t<tr><td colspan=8 class=\"text-align-center\">There are no poll records in the database.</td></tr>\n";
    }
    // Close the table
    $content = $content . "</table></div>\n";
    $content = $content . "<div class=\"text-align-center text-center w-100\">\n";
    $content = $content . "\t<button type=\"button\" class=\"btn btn-primary btn-group-justified\" data-toggle=\"modal\" data-target=\"#modalEditPoll\" id=\"btnAddPoll\" name=\"btnAddPoll\">New Poll</button> \n";
    $content = $content . "</div>\n";
   
    $content = $content . "<div class=\"modal fade\" id=\"modalEditPoll\" tabindex=\"-1\" aria-labelledby=\"modalEditPollLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog modal-lg modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalEditPollLabel\">Add new / Edit existing poll</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalEditPollBody\">\n";
    $content = $content . "\t\t\t<h5>Retrieving Data</h5>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" id=\"btnClosePoll\" data-dismiss=\"modal\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" name=\"btnAddOption\" id=\"btnAddOption\">Add option</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnSavePoll\">Save changes</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    $content = $content . "<div class=\"modal fade\" id=\"modalEditOption\" tabindex=\"-1\" aria-labelledby=\"modalEditOptionLabel\" aria-hidden=\"true\">\n";
    $content = $content . "\t<div class=\"modal-dialog modal-lg modal-dialog-centered\">\n\t\t<div class=\"modal-content\">\n\t\t\t<div class=\"modal-header\">\n";
    $content = $content . "\t\t\t\t<h5 class=\"modal-title\" id=\"modalEditOptionLabel\">Add new / edit existing poll option</h5>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-body\" id=\"modalEditOptionBody\">\n";
    $content = $content . "\t\t\t<form name=\"frmEditOption\" id=\"frmEditOption\" method=\"POST\" action=\"\">\n";
    $content = $content . "\t\t\t<input type=\"hidden\" id=\"OptionPollID\" name=\"OptionPollID\" value=\"-1\">";
    $content = $content . "\t\t\t<input type=\"hidden\" id=\"OptionOptionID\" name=\"OptionOptionID\" value=\"-1\">";
    $content = $content . "\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtOptionDescription\" name=\"txtOptionDescription\" size=50 required=\"required\"></td></tr>\n";
    $content = $content . "\t\t\t</form>\n";
    $content = $content . "\t\t\t</div>\n\t\t\t<div class=\"modal-footer\">\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" id=\"btnCloseOption\" data-dismiss=\"modal\">Close</button>\n";
    $content = $content . "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnSaveOption\">Save changes</button>\n";
    $content = $content . "\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n";

    // always return
    return $content;
}

function wpendeavourpoll_getpoll() {
    global  $wpdb;

    // Handle the ajax request
    check_ajax_referer('wpendeavourpoll_getpoll');

    $content = array();
    $PollID = $_GET['PollID'];
    $content['PollID'] = $PollID;

    if (! empty($PollID)) {      
        if ($PollID > 0) {
            $wpdb->flush();
            $sql = "SELECT PollID, PollTitle, PollDescription, DeadlineDate, DeadlineTime, NoOptions, PollStatusID, PollTypeID ";
            $sql = $sql . "FROM exp1_polls WHERE Deleted = 0 AND PollID = " . $PollID;
            $content['SQL1'] = $sql;
            $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
            $wpdb->flush();
            if (count($dbdata) > 0) {
                $content = array(
                    'PollID'=> esc_html($dbdata[0]),
                    'PollTitle' => esc_html($dbdata[1]),
                    'PollDescription' => esc_html($dbdata[2]),               
                    'DeadlineDate' => esc_html($dbdata[3]),
                    'DeadlineTime' => esc_html($dbdata[4]),
                    'NoOptions' => esc_html($dbdata[5]),
                    'PollStatusID' => esc_html($dbdata[6]),
                    'PollTypeID' => esc_html($dbdata[7]));
            } else {
                $content['PollID'] = $PollID;
                $content = array ( 'PollTitle' => 'PollID not found in database');
            }
            $sql = "SELECT PollOptionID, Description FROM exp1_polloptions WHERE Deleted = 0 AND PollID = " . $PollID . " ORDER BY Description ASC";
            $dbdata = $wpdb->get_results($sql, ARRAY_N);
            $wpdb->flush();
            $PollOptionsData = array();
            if (count($dbdata) > 0) {
                foreach($dbdata as $dbrow) {
                    $PollOptionsData[] = array('PollOptionID' => esc_html($dbrow[0]), 'OptionDescription' => esc_html($dbrow[1]));
                }
            }
            $content['PollOptionsNo'] = count($PollOptionsData);
            $content['SQL4'] = $sql;

            $sql = "SELECT R.PollReplyID, R.WPID, R.ReplyValue, R.ReplyComment, R.DateSubmitted, U.display_name FROM exp1_pollreplies R, " . $wpdb->base_prefix . "users U WHERE R.Deleted = 0 AND U.ID = R.WPID AND R.PollID = " . $PollID;
            $dbdata = $wpdb->get_results($sql, ARRAY_N);
            $wpdb->flush();
            $PollRepliesData = array();
            if (count($dbdata) > 0) {
                foreach($dbdata as $dbrow) {
                    $PollRepliesData[] = array('PollReplyID' => $dbrow[0], 'WPID' => $dbrow[1], 'ReplyValue' => $dbrow[2], 'ReplyComment' => esc_html($dbrow[3]), 'ReplyDate' => $dbrow[4], 'display_name' => esc_html($dbrow[5]));
                }
            }
            $content['PollRepliesNo'] = count($PollRepliesData);
            $content['SQL5'] = $sql;

            if (count($PollOptionsData) > 0) $content['PollOptionsData'] = $PollOptionsData;
            if (count($PollRepliesData) > 0) $content['PollRepliesData'] = $PollRepliesData;
        } else {
            $content['PollOptionsNo'] = 0;
            $content['PollRepliesNo'] = 0;
        }

        $sql = "SELECT PollStatusID, Description FROM exp1_pollstatus WHERE Deleted = 0 ORDER BY Description ASC";
        $dbdata = $wpdb->get_results($sql, ARRAY_N);
        $wpdb->flush();
        $PollStatusData = array();
        if (count($dbdata) > 0) {
            foreach($dbdata as $dbrow) {
                $PollStatusData[] = array('PollStatusID' => esc_html($dbrow[0]), 'Description' => esc_html($dbrow[1]));
            }
        }
        $content['PollStatusNo'] = count($PollStatusData);
        $content['SQL2'] = $sql;

        $sql = "SELECT PollTypeID, Description FROM exp1_polltypes WHERE Deleted = 0 ORDER BY Description ASC";
        $dbdata = $wpdb->get_results($sql, ARRAY_N);
        $wpdb->flush();
        $PollTypesData = array();
        if (count($dbdata) > 0) {
            foreach($dbdata as $dbrow) {
                $PollTypesData[] = array('PollTypeID' => esc_html($dbrow[0]), 'Description' => esc_html($dbrow[1]));
            }
        }
        $content['PollTypesNo'] = count($PollTypesData);
        $content['SQL3'] = $sql;

        if (count($PollStatusData) > 0) $content['PollStatusData'] = $PollStatusData;
        if (count($PollTypesData) > 0) $content['PollTypesData'] = $PollTypesData;

    }
    // Send the data back
    wp_send_json($content);
   
    wp_die(); // All ajax handlers die when finished
}

function wpendeavourpoll_savepoll($atts = [], $content = null) {
    global  $wpdb;
    
    // check_ajax_referer('wpendeavourpoll_savepoll');
    
    // Declare the return data
    $content = array();
    if (! empty($_POST['formdata'])) {
        $newdata = $_POST['formdata'];
        foreach ($newdata as $dbdata) {
            switch ($dbdata['name']) {
                case "PollID":
                    $PollID = $dbdata['value'];
                    break;
                case "txtTitle":
                    $PollTitle = $dbdata['value'];
                    break;
                case "selType":
                    $PollTypeID = $dbdata['value'];
                    break;
                case "selStatus":
                    $PollStatusID = $dbdata['value'];
                    break;
                case "txtDescription":
                    $PollDescription = $dbdata['value'];
                    break;
                case "dateEnd":
                    $DeadlineDate = $dbdata['value'];
                    break;
                case "timeEnd":
                    $DeadlineTime = $dbdata['value'];
                    break;
                default:
                    break;
            }
        }

        if ($PollID < 0) {
            $sql = "INSERT INTO exp1_polls (PollTitle, PollTypeID, PollStatusID, PollDescription, DeadlineDate, DeadlineTime) ";
            $sql = $sql . "VALUES ('" . $PollTitle . "', " . $PollTypeID . ", " . $PollStatusID . ", '" . $PollDescription . "', '" . $DeadlineDate . "', '" . $DeadlineTime . "')";    
        } else {
            $sql = "UPDATE exp1_polls SET PollTitle = '" . $PollTitle . "', PollTypeID = " . $PollTypeID . ", PollStatusID = " . $PollStatusID . ", PollDescription = '" . $PollDescription . "'";
            $sql = $sql . ", DeadlineDate = '" . $DeadlineDate . "', DeadlineTime = '" . $DeadlineTime . "' WHERE PollID = " . $PollID;
        }
        $wpdb->query($sql);
        $wpdb->flush();
        $content['success'] = 1;
        $content['SQL'] = $sql;
    }

    return $content;
}

function wpendeavourpoll_getoption() {
    global  $wpdb;

    // Handle the ajax request
    check_ajax_referer('wpendeavourpoll_getoption');

    $content = array();
    $PollOptionID = $_GET['PollOptionID'];
    $content['PollOptionID'] = $PollOptionID;

    if (! empty($PollOptionID)) {
        // Flush the DB cache and run the query
        if ($PollOptionID > 0) {
            $wpdb->flush();
            $sql = "SELECT PollOptionID, Description, PollID FROM exp1_polloptions WHERE Deleted = 0 AND PollOptionID = " . $PollOptionID . " ORDER BY Description ASC";
            $content['SQL'] = $sql;
            $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
            $wpdb->flush();
            if (count($dbdata) > 0) {
                $content = array(
                    'PollID'=> esc_html($dbdata[2]),
                    'PollOptionID' => esc_html($dbdata[0]),
                    'PollDescription' => esc_html($dbdata[1]),
                    'success' => 1);
            } else {
                $content['success'] = 0;
                $content['error'] = "Invalid PollOptionID.";
            }
        } else {
            $content['success'] = 0;
            $content['error'] = "No PollOptionID supplied.";
        }

    }
    // Send the data back
    wp_send_json($content);
   
    wp_die(); // All ajax handlers die when finished
}

function wpendeavourpoll_saveoption() {
    global  $wpdb;
    
    // check_ajax_referer('wpendeavourpoll_saveoption');
    // Declare the return data
    $content = array();
    if (! empty($_POST['formdata'])) {
        $formdata = $_POST['formdata'];
        foreach ($formdata as $formentry) {
            switch ($formentry['name']) {
                case "OptionPollID":
                    $PollID = $formentry['value'];
                    break;
                case "OptionOptionID":
                    $OptionID = $formentry['value'];
                    break;
                case "txtOptionDescription":
                    $OptionDescription = $formentry['value'];
                    break;
                default:
                    break;
            }
        }
        // echo "Action: " . $_POST['actiontype'] . " for ID: " . $expID . ". ";
        
        if (empty($OptionID)) {
            if (empty($PollID)) {
                $content['success'] = 0;
                $content['error'] = "PollID and OptionID are empty.";
                wp_send_json($content);
                wp_die();
            }    
            $sql = "INSERT INTO exp1_polloptions (PollID, Description) VALUES (" . $PollID . ", '" . $OptionDescription . "')";
        } else {
            $sql = "UPDATE exp1_polloptions SET Description = '" . $OptionDescription . "' WHERE PollOptionID = " . $OptionID;
        }
        $wpdb->query($sql);
        $wpdb->query("UPDATE exp1_polls SET NoOptions = (SELECT COUNT(*) FROM exp1_polloptions WHERE PollID = " . $PollID . ") WHERE PollID = " . $PollID);
    
        $sql = "SELECT PollOptionID, Description FROM exp1_polloptions WHERE Deleted = 0 AND PollID = " . $PollID . " ORDER BY Description ASC";
        $dbdata = $wpdb->get_results($sql, ARRAY_N);
        $PollOptionsData = array();
        if (count($dbdata) > 0) {
            foreach($dbdata as $dbrow) {
                $PollOptionsData[] = array('PollOptionID' => esc_html($dbrow[0]), 'OptionDescription' => esc_html($dbrow[1]));
            }
        }
        $content['PollOptionsNo'] = count($PollOptionsData);
        if (count($PollOptionsData) > 0) $content['PollOptionsData'] = $PollOptionsData;
        $content['SQL4'] = $sql;
    }    
    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavourpoll_savereply() {
    global  $wpdb;

    // check_ajax_referer('wpendeavourpoll_saveoption');

    // Declare the return data
    $content = array();
    if (! empty($_POST['formdata'])) {
        $formdata = $_POST['formdata'];
        $chkValue = "";
        $txtValue = "";
        $rdoPollOption = "";
        $OptionComment = "";
        foreach ($formdata as $formentry) {
            switch ($formentry['name']) {
                case "PollID":
                    $PollID = $formentry['value'];
                    break;
                case "OptionOptionID":
                    $OptionID = $formentry['value'];
                    break;
                case "txtPollComment":
                    $OptionComment = $formentry['value'];
                    break;
                case "rdoPollOption":
                    $rdoPollOption = $formentry["value"];
                    break;
                default:
                    if (strlen($formentry['name']) < 4) break;
                    if (intval($formentry['value']) == 0) break;                    
                    if (strncmp($formentry['name'], "chkPollOption", 13) == 0) {
                        if ($chkValue == "") {
                            $chkValue = $formentry['value'] . ";";
                        } else {
                            $chkValue = $chkValue . $formentry['value'] . ";";
                        }
                    } elseif (strncmp($formentry['name'], "txtPollOption", 13) == 0) {
                        $txtValue = $formentry['value'];
                    }
                    break;
            }
        }
        $sql = "SELECT COUNT(*) FROM exp1_pollreplies WHERE WPID = " . get_current_user_id() . " AND PollID = " . $PollID;
        $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
        if ($dbdata[0] > 0) {
            $sql = "SELECT PollReplyID FROM exp1_pollreplies WHERE WPID = " . get_current_user_id() . " AND PollID = " . $PollID;
            $dbdata = $wpdb->get_row($sql, ARRAY_N, 0);
            if (! empty($rdoPollOption)) {
                $sql = "UPDATE exp1_pollreplies SET ReplyValue='" . $rdoPollOption . "', ReplyComment='" . $OptionComment . "', DateSubmitted = CURRENT_TIMESTAMP WHERE PollReplyID = " . $dbdata[0];
            } elseif (! empty($chkValue)) {
                $sql = "UPDATE exp1_pollreplies SET ReplyValue='" . $chkValue . "', ReplyComment='" . $OptionComment . "', DateSubmitted = CURRENT_TIMESTAMP WHERE PollReplyID = " . $dbdata[0];
            } elseif (! empty($txtValue)) {
                $sql = "UPDATE exp1_pollreplies SET ReplyValue='" . $txtValue . "', ReplyComment='" . $OptionComment . "', DateSubmitted = CURRENT_TIMESTAMP WHERE PollReplyID = " . $dbdata[0];
            }
        } else {
            if (! empty($rdoPollOption)) {
                $sql = "INSERT INTO exp1_pollreplies(PollID, WPID, ReplyValue, ReplyComment, DateSubmitted) VALUES (" . $PollID . ", " . get_current_user_id() . ", '" . $rdoPollOption . "', '" . $OptionComment . "', CURRENT_TIMESTAMP)";
            } elseif (! empty($chkValue)) {
                // remove the final semicolon
                $chkValue = substr($chkValue, 0, strlen($chkValue) - 1);
                $sql = "INSERT INTO exp1_pollreplies(PollID, WPID, ReplyValue, ReplyComment, DateSubmitted) VALUES (" . $PollID . ", " . get_current_user_id() . ", '" . $chkValue . "', '" . $OptionComment . "', CURRENT_TIMESTAMP)";
            } elseif (! empty($txtValue)) {
                $sql = "INSERT INTO exp1_pollreplies(PollID, WPID, ReplyValue, ReplyComment, DateSubmitted) VALUES (" . $PollID . ", " . get_current_user_id() . ", '" . $txtValue . "', '" . $OptionComment . "', CURRENT_TIMESTAMP)";
            }
           
        }
        $wpdb->query($sql);
        $content['success'] = 1;
    } else {
        $content['success'] = 0;
    }
    
    wp_send_json($content);

    wp_die(); // All ajax handlers die when finished
}

function wpendeavourpoll_enqueuescript( $hook ) {
    // If this is not a page, return. Otherwise, enqueue the script
    wp_enqueue_script( 'ajax-endeavourpoll', plugins_url('js/wp-endeavourpoll-endeavourpoll.js', __FILE__ ), array('jquery') );
    $nonce_allpolls = wp_create_nonce( 'wpendeavourpoll_allpolls' );
    $nonce_getpoll = wp_create_nonce( 'wpendeavourpoll_getpoll' );
    $nonce_savepoll = wp_create_nonce( 'wpendeavourpoll_savepoll' );
    $nonce_getoption = wp_create_nonce( 'wpendeavourpoll_getoption' );
    $nonce_saveoption = wp_create_nonce( 'wpendeavourpoll_saveoption' );
    $nonce_getreply = wp_create_nonce( 'wpendeavourpoll_getreply' );
    $nonce_savereply = wp_create_nonce( 'wpendeavourpoll_savereply' );

    wp_localize_script( 'ajax-endeavourpoll', 'ajaxdata_getpoll', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getpoll,
     ) );
     wp_localize_script( 'ajax-endeavourpoll', 'ajaxdata_savepoll', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_savepoll,
     ) );
     wp_localize_script( 'ajax-endeavourpoll', 'ajaxdata_getoption', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getoption,
     ) );
     wp_localize_script( 'ajax-endeavourpoll', 'ajaxdata_saveoption', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_saveoption,
     ) );
     wp_localize_script( 'ajax-endeavourpoll', 'ajaxdata_getreply', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_getreply,
     ) );
     wp_localize_script( 'ajax-endeavourpoll', 'ajaxdata_savereply', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => $nonce_savereply,
     ) );
    }

register_activation_hook( __FILE__, 'wpendeavourpoll_activate' );
register_deactivation_hook( __FILE__, 'wpendeavourpoll_deactivate' );

// Shortcode to trigger the leader plugin from the page 
add_shortcode('EndeavourPoll_AllPolls', 'wpendeavourpoll_allpolls');

// Shortcode to trigger the explorer plugin from the page
add_shortcode('EndeavourPoll_Poll', 'wpendeavourpoll_poll');

// Add the JQuery scripts to the page
add_action('wp_enqueue_scripts' , 'wpendeavourpoll_enqueuescript');

// Add the handler for AJAX request to get a single (or new) poll 
add_action('wp_ajax_get_poll', 'wpendeavourpoll_getpoll');

// Add the handler for AJAX request to save a poll 
add_action('wp_ajax_save_poll', 'wpendeavourpoll_savepoll');

// Add the handler for AJAX request to get a poll option
add_action('wp_ajax_get_option', 'wpendeavourpoll_getoption');

// Add the handler for AJAX request to save a poll option 
add_action('wp_ajax_save_option', 'wpendeavourpoll_saveoption');

// Add the handler for AJAX request to save a reply to a poll
add_action('wp_ajax_save_reply', 'wpendeavourpoll_savereply');
?>
