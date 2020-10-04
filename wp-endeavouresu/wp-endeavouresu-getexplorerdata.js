jQuery(document).ready(function($) { 
    // Create inner HTML based on calling button
    $( "#btnEditStatus" ).click(function() {
        var ExplorerID = document.getElementById("ExplorerID");
        var expID = ExplorerID.value;
        console.log("Edit Status: " + expID);

        var newcontent;
        $.get(ajaxdata_getexplorerdata.ajax_url, {
            _ajax_nonce: ajaxdata_getexplorerdata.nonce,
            action: "get_explorerdata",
            actiontype: "EditStatus",
            ExpID: expID,
        }, function(newdata) {
            console.log("callback");
            newcontent = "\t\t\t\t<form name=\"frmExplorerData\" id=\"frmExplorerData\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" name=\"UpdateType\" name=\"UpdateType\" value=\"EditStatus\">";
            newcontent = newcontent + "\t\t\t\t<input type=\"hidden\" name=\"ExplorerID\" name=\"ExplorerID\" value=\"" + expID + "\">";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            if (newdata.ExpStatusNo > 0) {
                for (var i = 0 ; i < newdata.ExpStatusNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"radio\" id=\"rdoActive" + newdata.ExpStatus[i].StatusID + "\" name=\"rdoActive\" value=\"" + newdata.ExpStatus[i].StatusID + "\"";
                    if (newdata.ExpStatusID == newdata.ExpStatus[i].StatusID) newcontent = newcontent + " checked";
                    newcontent = newcontent + "> <label for=\"rdoActive" + newdata.ExpStatus[i].StatusID + "\">"  + newdata.ExpStatus[i].Description + "</label>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td class=\"text-align-center\">Could not retrieve data from server.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            console.log(newcontent);
            document.getElementById("modalUpdateExplorerBody").innerHTML = newcontent;
        });
       
    });
    $( "#btnEditType" ).click(function() {
    });
    $( "#btnAddNA" ).click(function() {
    });
    $( "#btnAddHike" ).click(function() {
    });
    $( "#btnAddAward" ).click(function() {
        var newcontent;
        $.get(ajaxdata_getexplorerdata.ajax_url, {
            _ajax_nonce: ajaxdata_getexplorerdata.nonce,
            action: "get_explorerdata",
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmAllExplorers\" id=\"frmAllExplorers\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            // List of explorers goes here from JSON data
            // var nonexpusers = JSON.parse(data);
            if (newdata.expno > 0) {
                for (var i = 0 ; i < newdata.expno ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<tr><td><input type=\"checkbox\" id=\"chk" + newdata.explist[i].id + "\" name=\"chk" + newdata.explist[i].id + "\" value=\"" + newdata.explist[i].id + "\"></td>";
                    newcontent = newcontent + "<td>" + newdata.explist[i].display_name + "</td></tr>\n";
                }
            } else {
                newcontent = newcontent + "\t\t<tr><td class=\"text-align-center\">There are no explorer records in the database.</td></tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalUpdateExplorer").innerHTML = newcontent;
        });
    });
});