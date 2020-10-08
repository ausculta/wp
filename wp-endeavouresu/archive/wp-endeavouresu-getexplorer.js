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
});