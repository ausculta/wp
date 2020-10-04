jQuery(document).ready(function($) { 
    $( ".explorer" ).click(function() {
        var newcontent;
        
        // alert("getexplorer: " + this.id);

        $.get(ajaxdata_getexplorer.ajax_url, {
            _ajax_nonce: ajaxdata_getexplorer.nonce,
            action: "get_explorer",
            ExpID: this.id,
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmExplorer\" id=\"frmExplorer\" method=\"POST\" action=\"\">\n";
            newcontent = "\t\t\t\t<input type=\"hidden\" id=\"ExpID\" value=\"" + newdata.ExpID + "\">\n"
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>Login:</td><td>" + newdata.Login + " (" + newdata.Status + " - " + newdata.ExpType + " - " + newdata.DateStart + " - " + newdata.DateEnd + ")</td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>" + newdata.NightsAway + " Nights Away:</td><td>\t\t\t\t\t\t<table class=\"table\">\n\n" 
            if (newdata.NAno > 0) {
                for (i = 0 ; i < newdata.ExpNAs.length ; i++)
                newcontent = newcontent + "\t\t\t\t\t\t<tr><td>" + newdata.ExpNAs[i].Description + " (" + newdata.ExpNAs[i].NADays + " night(s) - " + newdata.ExpNAs[i].DateStart + " - " + newdata.ExpNAs[i].DateEnd  + ")</td><tr>";
            }
            newcontent = newcontent + "\t\t\t\t\t\t</table></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>" + newdata.Hikes + " Hikes:</td><td>\t\t\t\t\t\t<table class=\"table\">\n\n" 
            if (newdata.HikeNo > 0) {
                for (i = 0 ; i < newdata.ExpNAs.length ; i++)
                newcontent = newcontent + "\t\t\t\t\t\t<tr><td>" + newdata.ExpHikes[i].Description + " (" + newdata.ExpHikes[i].DateStart + " - " + newdata.ExpHikes[i].DateEnd  + ")</td><tr>";
            }
            newcontent = newcontent + "\t\t\t\t\t\t</table></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>" + newdata.BadgeNo + " Awards / Badges:</td><td>\t\t\t\t\t\t<table class=\"table\">\n\n" 
            if (newdata.NAno > 0) {
                for (i = 0 ; i < newdata.ExpNAs.length ; i++)
                newcontent = newcontent + "\t\t\t\t\t\t<tr><td><img src=\"" + newdata.ExpBadges[i].IconPath + "</td><td>" + newdata.ExpBadges[i].BadgeName + "</td>";
                newcontent = newcontent + "<td>" + newdata.ExpBadges[i].BadgeName + " (" + newdata.ExpBadges[i].BadgeStatus + " - " + newdata.ExpBadges[i].DateStart + " - " + newdata.ExpBadges[i].DateEnd  + ")</td><tr>\n";
            }
            newcontent = newcontent + "\t\t\t\t\t\t</table></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";

            newcontent = newcontent + "\t\t\t\t<div class=\"modal fade\" id=\"modalUpdateExplorer\" tabindex=\"-1\" aria-labelledby=\"modalUpdateExplorerLabel\" aria-hidden=\"true\">\n";
            newcontent = newcontent + "\t\t\t\t<div class=\"modal-dialog modal-xl modal-dialog-centered\">\n\t\t\t\t<div class=\"modal-content\">\n\t\t\t\t<div class=\"modal-header\">\n";
            newcontent = newcontent + "\t\t\t\t<h5 class=\"modal-title\" id=\"modalUpdateExplorerLabel\">" + newdata.Name + " (" + newdata.ExpID + ")</h5>\n";
            newcontent = newcontent + "\t\t\t\t<button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>\n";
            newcontent = newcontent + "\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-body\" id=\"modalUpdateExplorerBody\">\n";
            newcontent = newcontent + "\t\t\t\t<h5>Retrieving Data</h5>\n";
            newcontent = newcontent + "\t\t\t\t</div>\n\t\t\t\t<div class=\"modal-footer\">\n";
            newcontent = newcontent + "\t\t\t\t<button type=\"button\" class=\"btn btn-secondary\" data-toggle=\"modal\" data-target=\"#modalUpdateExplorer\">Close</button>\n";
            newcontent = newcontent + "\t\t\t\t<button type=\"button\" class=\"btn btn-primary\" id=\"btnUpdateSave\">Save changes</button>\n";
            newcontent = newcontent + "\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t</div>\n";
        
            document.getElementById("modalGetExplorerLabel").innerHTML = newdata.Name + " (id: " + newdata.ExpID + ")";
            document.getElementById("modalGetExplorerBody").innerHTML = newcontent;
        });
    });
});