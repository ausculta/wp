jQuery(document).ready(function($) { 
    // Create inner HTML based on calling button
    $( "#btnExplorerClose" ).click(function() {
        // document.getElementById("modalUpdateExplorer").modal('hide');
        $('#modalUpdateExplorer').modal('hide')
    });
    $( "#btnEditStatus" ).click(function() {
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
            action: "getexplorerdata",
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