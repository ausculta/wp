jQuery(document).ready(function($) { 
    // Add Explorer button on main plugin page: when button is clicked, create content for modalAddExplorer         
    $( "#btnAddExplorers" ).click(function() {
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
    // Click handler for explorer list on modalAddExplorer
     $('#btnSaveExplorers').click(function() {
        var i = 0;
        var nonexpform = $("#frmAllExplorers").serializeArray()
        rdoType = document.getElementById("frmAllExplorers")["rdoType"].value;
        var dateFrom = document.getElementById("dateFrom").value;
        $.post(ajaxdata_newexplorers.ajax_url, {
            _ajax_nonce: ajaxdata_newexplorers.nonce,
            action: "save_newexplorers",
            dbdata: nonexpform,
            rdoType: rdoType,
            dateFrom: dateFrom,
        }, function(newdata) {
            alert("Inserted " + newdata.dbrecords + " records.");
            $('#modalAddExplorers').modal('hide');
            window.location.reload();
        });
    });
    $('#btnCloseExplorers').click(function() {
        window.location.reload();
    })
     // Click handler for explorer list on main plugin page: displays and explorer (badges, hikes, NA, etc.)
    $( ".explorer" ).click(function() {
        var newcontent;
        $.get(ajaxdata_getexplorer.ajax_url, {
            _ajax_nonce: ajaxdata_getexplorer.nonce,
            action: "get_explorer",
            ExpID: this.id,
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmExplorer\" id=\"frmExplorer\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerID\" name=\"ExplorerID\" value=\"" + newdata.ExpID + "\">\n"
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerName\" name=\"ExplorerName\" value=\"" + newdata.Name + "\">";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>Login:</td><td>" + newdata.Login + " (" + newdata.Status + " - " + newdata.ExpType + " - " + newdata.DateStart + " - " + newdata.DateEnd + ")</td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td class=\"align-text-top\">" + newdata.BadgesNo + " Awards / Badges:</td><td>\t\t\t\t\t\t<table class=\"table\">\n" 
            if (newdata.BadgesNo > 0) {
                for (i = 0 ; i < newdata.ExpBadges.length ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t\t<tr><td><img height=\"25px\" src=\"" + newdata.ExpBadges[i].IconPath + "\"></td><td>" + newdata.ExpBadges[i].Description + "</td>";
                    newcontent = newcontent + "<td>"  + newdata.ExpBadges[i].DateStart + " - ";
                    if (newdata.ExpBadges[i].DateEnd === "") {
                        newcontent = newcontent + "in progress";
                    } else {
                        newcontent = newcontent + newdata.ExpBadges[i].DateEnd;
                    }
                    newcontent = newcontent + "</td><tr>\n";
                }
            }
            newcontent = newcontent + "\t\t\t\t\t\t</table></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td class=\"align-text-top\">" + newdata.NightsAway + " nights away:</td><td>\t\t\t\t\t\t<table class=\"table\">\n\n" 
            if (newdata.NANo > 0) {
                for (i = 0 ; i < newdata.ExpNAs.length ; i++)
                newcontent = newcontent + "\t\t\t\t\t\t<tr><td>" + newdata.ExpNAs[i].Description + " ("+ newdata.ExpNAs[i].NALocation + ": " + newdata.ExpNAs[i].NADays + " night(s) - " + newdata.ExpNAs[i].DateStart + " - " + newdata.ExpNAs[i].DateEnd  + ")</td><tr>";
            }
            newcontent = newcontent + "\t\t\t\t\t\t</table></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td class=\"align-text-top\">" + newdata.Hikes + " Hikes:</td><td>\t\t\t\t\t\t<table class=\"table\">\n\n" 
            if (newdata.HikeNo > 0) {
                for (i = 0 ; i < newdata.ExpHikes.length ; i++)
                newcontent = newcontent + "\t\t\t\t\t\t<tr><td>" + newdata.ExpHikes[i].Description + " (" + newdata.ExpHikes[i].HikeDays + " hikes: " + newdata.ExpHikes[i].DateStart + " - " + newdata.ExpHikes[i].DateEnd  + ")</td><tr>";
            }
            newcontent = newcontent + "\t\t\t\t\t\t</table></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
        
            document.getElementById("modalGetExplorerLabel").innerHTML = newdata.Name + " (id: " + newdata.ExpID + ")";
            document.getElementById("modalGetExplorerBody").innerHTML = newcontent;
        });
    });
    $('#btnExplorerClose').click(function() {
        window.location.reload();
    });
    // Edit Status button on modalGetExplorer (edits a single explorer) 
    $( "#btnEditStatus" ).click(function() {
        var ExplorerID = document.getElementById("ExplorerID");
        var ExplorerName = document.getElementById("ExplorerName");
        var expID = ExplorerID.value;
        var expName = ExplorerName.value;

        var newcontent;
        $.get(ajaxdata_getexplorerdata.ajax_url, {
            _ajax_nonce: ajaxdata_getexplorerdata.nonce,
            action: "get_explorerdata",
            actiontype: "EditStatus",
            ExpID: expID,
        }, function(newdata) {
            console.log("callback");
            newcontent = "\t\t\t\t<form name=\"frmExplorerData\" id=\"frmExplorerData\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"UpdateType\" name=\"UpdateType\" value=\"EditStatus\">";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerID\" name=\"ExplorerID\" value=\"" + expID + "\">";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerName\" name=\"ExplorerName\" value=\"" + expName + "\">";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpStatusNo > 0) {
                for (var i = 0 ; i < newdata.ExpStatusNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"radio\" id=\"rdoStatus" + newdata.ExpStatus[i].StatusID + "\" name=\"rdoStatus\" value=\"" + newdata.ExpStatus[i].StatusID + "\"";
                    if (newdata.ExpStatusID == newdata.ExpStatus[i].StatusID) newcontent = newcontent + " checked";
                    newcontent = newcontent + "></td><td><label for=\"rdoStatus" + newdata.ExpStatus[i].StatusID + "\">"  + newdata.ExpStatus[i].Description + "</label></td><tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td class=\"text-align-center\">Could not retrieve data from server.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
            document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
        });
    });
    // Edit explorer type button on modalGetExplorer (edits a single explorer)
    $( "#btnEditType" ).click(function() {
        var ExplorerID = document.getElementById("ExplorerID");
        var ExplorerName = document.getElementById("ExplorerName");
        var expID = ExplorerID.value;
        var expName = ExplorerName.value;

        var newcontent;
        $.get(ajaxdata_getexplorerdata.ajax_url, {
            _ajax_nonce: ajaxdata_getexplorerdata.nonce,
            action: "get_explorerdata",
            actiontype: "EditType",
            ExpID: expID,
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmExplorerData\" id=\"frmExplorerData\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"UpdateType\" name=\"UpdateType\" value=\"EditType\">";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerID\" name=\"ExplorerID\" value=\"" + expID + "\">";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"ExplorerName\" name=\"ExplorerName\" value=\"" + expName + "\">";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpTypesNo > 0) {
                for (var i = 0 ; i < newdata.ExpTypesNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"radio\" id=\"rdoType" + newdata.ExpTypes[i].TypeID + "\" name=\"rdoType\" value=\"" + newdata.ExpTypes[i].TypeID + "\"";
                    if (newdata.ExpTypeID == newdata.ExpTypes[i].TypeID) newcontent = newcontent + " checked";
                    newcontent = newcontent + "></td><td><label for=\"rdoType" + newdata.ExpTypes[i].TypeID + "\">"  + newdata.ExpTypes[i].Description + "</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">Could not retrieve data from server.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t<tr><td>From:</td><td><input type=\"date\" id=\"dateFrom\" name=\"dateFrom\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
            document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
        });
    });
    // Add Night Away button on modalGetExplorer (adds to single explorer)
    $( "#btnAddNA" ).click(function() {
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
        document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
        document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
    });
    // Add hike button on modalGetExplorer (adds to single explorer)
    $( "#btnAddHike" ).click(function() {
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
        document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
        document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
    });
    // Add badge button on modalGetExplorer (adds to single explorer)
    $( "#btnAddBadge" ).click(function() {
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
            document.getElementById("modalUpdateExplorerLabel").innerHTML = expName + " (id: " + expID + ")";
            document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
        });
    });
    // 
    $( "#btnUpdateSave").click(function() {
        console.log("Saving explorer data.");
        var i = 0;
        var formdata = $("#frmExplorerData").serializeArray();
        var updatetype = document.getElementById("UpdateType").value;

        console.log("Sending AJAX request.");
        $.post(ajaxdata_updateexplorerdata.ajax_url, {
            _ajax_nonce: ajaxdata_updateexplorerdata.nonce,
            action: "update_explorerdata",
            actiontype: updatetype,
            dbdata: formdata,
        }, function(newdata) {
            if (newdata.success == 1) {
                $('#modalUpdateExplorer').modal('hide');
            } else {
                document.getElementById("modalUpdateExplorerBody").innerHTML = "<h5 class=\"text-align-center\">An error occured: the database was not updated.</h5>\n";
            }
        });
    });
    // Add badge button on main plugin page (adds to selected explorers explorer)
    $( "#btnAddEventBadge" ).click(function() {
        var newcontent;
        $.get(ajaxdata_geteventdata.ajax_url, {
            _ajax_nonce: ajaxdata_geteventdata.nonce,
            action: "get_eventdata",
            actiontype: "AddEventBadge",
        }, function(newdata) {
            // console.log("callback");
            newcontent = "\t\t\t\t<form name=\"frmAddEvent\" id=\"frmAddEvent\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"AddEventType\" name=\"AddEventType\" value=\"AddEventBadge\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpNo > 0) {
                for (var i = 0 ; i < newdata.ExpNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"chk" + newdata.Explorers[i].ExpID + "\" name=\"chk" + newdata.Explorers[i].ExpID + "\" value=\"" + newdata.Explorers[i].ExpID + "\"></td>";
                    newcontent = newcontent + "<td><label for =\"chk" + newdata.Explorers[i].ExpID + "\">" + newdata.Explorers[i].ExpName + " (" + newdata.Explorers[i].ExpType + " - " + newdata.Explorers[i].ExpStatus + ")</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
            }
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
            document.getElementById("modalAddEventLabel").innerHTML = "Add Badge for multiple explorers";
            document.getElementById("modalAddEventBody").innerHTML = newcontent;
        });
    });
   // Add NA button on main plugin page (adds to all selected explorers explorer)
   $( "#btnAddEventNA" ).click(function() {
        var newcontent;
        $.get(ajaxdata_geteventdata.ajax_url, {
            _ajax_nonce: ajaxdata_geteventdata.nonce,
            action: "get_eventdata",
            actiontype: "AddEventNA",
        }, function(newdata) {
            // console.log("callback");
            newcontent = "\t\t\t\t<form name=\"frmAddEvent\" id=\"frmAddEvent\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"AddEventType\" name=\"AddEventType\" value=\"AddEventNA\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpNo > 0) {
                for (var i = 0 ; i < newdata.ExpNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"chk" + newdata.Explorers[i].ExpID + "\" name=\"chk" + newdata.Explorers[i].ExpID + "\" value=\"" + newdata.Explorers[i].ExpID + "\"></td>";
                    newcontent = newcontent + "<td><label for =\"chk" + newdata.Explorers[i].ExpID + "\">" + newdata.Explorers[i].ExpName + " (" + newdata.Explorers[i].ExpType + " - " + newdata.Explorers[i].ExpStatus + ")</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtDescription\" name=\"txtDescription\" size=50></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Location:</td><td><input type=\"text\" id=\"txtLocation\" name=\"txtLocation\" size=50></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>No of NA:</td><td><input type=\"text\" id=\"txtDays\" name=\"txtDays\" size=10></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Start:</td><td><input type=\"date\" id=\"dateStart\" name=\"dateStart\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>End:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalAddEventLabel").innerHTML = "Add Night Away for multiple explorers";
            document.getElementById("modalAddEventBody").innerHTML = newcontent;
        });
    });
    $( "#btnAddEventReqt" ).click(function() {
        var newcontent;
        $.get(ajaxdata_geteventdata.ajax_url, {
            _ajax_nonce: ajaxdata_geteventdata.nonce,
            action: "get_eventdata",
            actiontype: "AddEventReqt",
        }, function(newdata) {
            // console.log("callback");
            newcontent = "\t\t\t\t<form name=\"frmAddEvent\" id=\"frmAddEvent\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"AddEventType\" name=\"AddEventType\" value=\"AddEventReqt\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpNo > 0) {
                for (var i = 0 ; i < newdata.ExpNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"chk" + newdata.Explorers[i].ExpID + "\" name=\"chk" + newdata.Explorers[i].ExpID + "\" value=\"" + newdata.Explorers[i].ExpID + "\"></td>";
                    newcontent = newcontent + "<td><label for =\"chk" + newdata.Explorers[i].ExpID + "\">" + newdata.Explorers[i].ExpName + " (" + newdata.Explorers[i].ExpType + " - " + newdata.Explorers[i].ExpStatus + ")</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
            }
            if (newdata.BadgesNo > 0) {
                newcontent = newcontent + "\t\t\t\t\t<tr><td>Badge:</td><td><select id=\"selBadgeReqt\" name=\"selBadgeReqt\" class=\"badgereqts\">\n";
                for (var i = 0 ; i < newdata.BadgesNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<option value=\"" + newdata.Badges[i].BadgeID + "\">" + newdata.Badges[i].Description + "</option>\n";
                }
                newcontent = newcontent + "\t\t\t\t\t</select></td></tr>\n";
            } else {
                newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">Could not retrieve data from server.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t<tr><td>Req'ts:</td><td name=\"tdBadgeReqts\" id=\"tdBadgeReqts\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Start:</td><td><input type=\"date\" id=\"dateStart\" name=\"dateStart\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>End:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            // console.log(newcontent);
            document.getElementById("modalAddEventLabel").innerHTML = "Add Badge Requirements for multiple explorers";
            document.getElementById("modalAddEventBody").innerHTML = newcontent;
        });
    });  
    // Add Hike button on main plugin page (adds to all selected explorers explorer)
   $( "#btnAddEventHike" ).click(function() {
        $.get(ajaxdata_geteventdata.ajax_url, {
            _ajax_nonce: ajaxdata_geteventdata.nonce,
            action: "get_eventdata",
            actiontype: "AddEventHike",
        }, function(newdata) {
            // console.log("callback");
            var newcontent = "\t\t\t\t<form name=\"frmAddEvent\" id=\"frmAddEvent\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" id=\"AddEventType\" name=\"AddEventType\" value=\"AddEventHike\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpNo > 0) {
                for (var i = 0 ; i < newdata.ExpNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"chk" + newdata.Explorers[i].ExpID + "\" name=\"chk" + newdata.Explorers[i].ExpID + "\" value=\"" + newdata.Explorers[i].ExpID + "\"></td>";
                    newcontent = newcontent + "<td><label for =\"chk" + newdata.Explorers[i].ExpID + "\">" + newdata.Explorers[i].ExpName + " (" + newdata.Explorers[i].ExpType + " - " + newdata.Explorers[i].ExpStatus + ")</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td colspan=2 class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtDescription\" name=\"txtDescription\" size=50></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>No of Days:</td><td><input type=\"text\" id=\"txtHikeDays\" name=\"txtHikeDays\" size=10></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Start:</td><td><input type=\"date\" id=\"dateStart\" name=\"dateStart\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>End:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalAddEventLabel").innerHTML = "Add Hike for multiple explorers";
            document.getElementById("modalAddEventBody").innerHTML = newcontent;
        });
    });
    $("body").on('change', 'select.badgereqts', function() {
        $.get(ajaxdata_getbadgereqts.ajax_url, {
            _ajax_nonce: ajaxdata_getbadgereqts.nonce,
            action: "get_badgereqts",
            actiontype: document.getElementById("selBadgeReqt").value,
        }, function(newdata) {
            var newcontent = "\t\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.reqtsno > 0) {
                for (var i = 0 ; i < newdata.reqtsno ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"reqt" + newdata.reqts[i].reqtid + "\" name=\"reqt" + newdata.reqts[i].reqtid + "\" value=\"" + newdata.reqts[i].reqtid + "\"></td>";
                    newcontent = newcontent + "<td><label for =\"reqt" + newdata.reqts[i].reqtid + "\">" + newdata.reqts[i].reqtdesc + "</label></td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t\t\t\t<tr><td colspan=2 class=\"text-align-center\">There are no matching badge requirements records in the database.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t\t</table>\n";
            document.getElementById("tdBadgeReqts").innerHTML = newcontent;
        });
    })
    $( "#btnSaveEvent").click(function() {
        console.log("Saving explorer data.");
        var i = 0;
        var formdata = $("#frmAddEvent").serializeArray();
        var updatetype = document.getElementById("AddEventType").value;

        $.post(ajaxdata_addeventdata.ajax_url, {
            _ajax_nonce: ajaxdata_addeventdata.nonce,
            action: "add_eventdata",
            actiontype: updatetype,
            dbdata: formdata,
        }, function(newdata) {
            if (newdata.success == 1) {
                alert("Event added succesfully.");
                $('#modalAddEvent').modal('hide');
            } else {
                document.getElementById("modalAddEventBody").innerHTML = "<h5 class=\"text-align-center\">An error occured: the database was not updated.</h5>\n";
            }
        });
    });
});

