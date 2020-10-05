jQuery(document).ready(function($) { 
    $('#modalAddEvent').on('show.bs.modal', function() {
        var newcontent;

        $.get(ajaxdata_nonexpusers.ajax_url, {
            _ajax_nonce: ajaxdata_nonexpusers.nonce,
            action: "get_nonexpusers",
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmAllExplorers\" id=\"frmAllExplorers\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            // List of explorers goes here from JSON data
            // var nonexpusers = JSON.parse(data);
            if (newdata.expno > 0) {
                if (newdata.ExpTypesNo > 0) {
                    for (var i = 0 ; i < newdata.ExpTypesNo ; i++) {
                        newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"radio\" id=\"rdoType" + newdata.ExpTypes[i].TypeID + "\" name=\"rdoType\" value=\"" + newdata.ExpTypes[i].TypeID + "\"";
                        if (newdata.ExpTypeID == newdata.ExpTypes[i].TypeID) newcontent = newcontent + " checked";
                        newcontent = newcontent + "></td><td><label for=\"rdoType" + newdata.ExpTypes[i].TypeID + "\">"  + newdata.ExpTypes[i].Description + "</label></td><tr>\n";
                    }
                } else {
                    newcontent = newcontent + "\t\t<tr><td class=\"text-align-center\">Could not retrieve data from server.</td></tr>\n";
                }
                newcontent = newcontent + "\t\t\t\t<tr><td>From:</td><td><input type=\"date\" id=\"dateFrom\" name=\"dateFrom\"></td></td>\n";   
                for (var i = 0 ; i < newdata.expno ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"chk" + newdata.explist[i].id + "\" name=\"chk" + newdata.explist[i].id + "\" value=\"" + newdata.explist[i].id + "\"></td>";
                    newcontent = newcontent + "<td><label for =\"chk" + newdata.explist[i].id + "\">" + newdata.explist[i].display_name + "</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalAddExplorersBody").innerHTML = newcontent;
        });
    });
});

$( "#btnAddEventNA" ).click(function() {
    var ExplorerID = document.getElementById("ExplorerID");
    var ExplorerName = document.getElementById("ExplorerName");
    var expID = ExplorerID.value;
    var expName = ExplorerName.value;
    newcontent = "\t\t\t\t<form name=\"frmExplorerData\" id=\"frmExplorerData\" method=\"POST\" action=\"\">\n";
    newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"UpdateType\" name=\"UpdateType\" value=\"AddNA\">";
    newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerID\" name=\"ExplorerID\" value=\"" + expID + "\">";
    newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerName\" name=\"ExplorerName\" value=\"" + expName + "\">";
    newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtDescription\" name=\"txtDescription\" size=50></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>Location:</td><td><input type=\"text\" id=\"txtLocation\" name=\"txtLocation\" size=50></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>No of Nights Away:</td><td><input type=\"text\" id=\"txtDays\" name=\"txtDays\" size=10></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>Start:</td><td><input type=\"date\" id=\"dateStart\" name=\"dateStart\"></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>End:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\"></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t</table>\n";
    newcontent = newcontent + "\t\t\t\t</form>\n";
    console.log(newcontent);
    document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
    document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
});
$( "#btnAddEventHike" ).click(function() {
    var ExplorerID = document.getElementById("ExplorerID");
    var ExplorerName = document.getElementById("ExplorerName");
    var expID = ExplorerID.value;
    var expName = ExplorerName.value;
    newcontent = "\t\t\t\t<form name=\"frmExplorerData\" id=\"frmExplorerData\" method=\"POST\" action=\"\">\n";
    newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"UpdateType\" name=\"UpdateType\" value=\"AddHike\">";
    newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerID\" name=\"ExplorerID\" value=\"" + expID + "\">";
    newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerName\" name=\"ExplorerName\" value=\"" + expName + "\">";
    newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtDescription\" name=\"txtDescription\" size=50></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>No of Days:</td><td><input type=\"text\" id=\"txtHikeDays\" name=\"txtHikeDays\" size=10></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>Start:</td><td><input type=\"date\" id=\"dateStart\" name=\"dateStart\"></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t<tr><td>End:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\"></td></tr>\n";
    newcontent = newcontent + "\t\t\t\t</table>\n";
    newcontent = newcontent + "\t\t\t\t</form>\n";
    console.log(newcontent);
    document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
    document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
});
$( "#btnAddEventBadge" ).click(function() {
    var ExplorerID = document.getElementById("ExplorerID");
    var ExplorerName = document.getElementById("ExplorerName");
    var expID = ExplorerID.value;
    var expName = ExplorerName.value;
    // console.log("Edit Type: " + expID);

    var newcontent;
    $.get(ajaxdata_getexplorerdata.ajax_url, {
        _ajax_nonce: ajaxdata_getexplorerdata.nonce,
        action: "get_explorerdata",
        actiontype: "AddBadge",
        ExpID: expID,
    }, function(newdata) {
        // console.log("callback");
        newcontent = "\t\t\t\t<form name=\"frmExplorerData\" id=\"frmExplorerData\" method=\"POST\" action=\"\">\n";
        newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"UpdateType\" name=\"UpdateType\" value=\"AddBadge\">\n";
        newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerID\" name=\"ExplorerID\" value=\"" + expID + "\">\n";
        newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerName\" name=\"ExplorerName\" value=\"" + expName + "\">\n";
        newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
        if (newdata.BadgesNo > 0) {
            newcontent = newcontent + "\t\t\t\t\t<tr><td>Badge:</td><td><select id=\"selBadge\" name=\"selBadge\">\n";
            for (var i = 0 ; i < newdata.BadgesNo ; i++) {
                newcontent = newcontent + "\t\t\t\t\t<option value=\"" + newdata.Badges[i].BadgeID + "\">" + newdata.Badges[i].Description + "</option>\n";
            }
            newcontent = newcontent + "\t\t\t\t\t</select></td></tr>\n";
        } else {
            newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">Could not retrieve data from server.</td></tr>\n";
        }
        newcontent = newcontent + "\t\t\t\t<tr><td>Start:</td><td><input type=\"date\" id=\"dateStart\" name=\"dateStart\"></td></tr>\n";
        newcontent = newcontent + "\t\t\t\t<tr><td>End:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\"></td></tr>\n";
        newcontent = newcontent + "\t\t\t\t</table>\n";
        newcontent = newcontent + "\t\t\t\t</form>\n";
        // console.log(newcontent);
        document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
        document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
    });
});