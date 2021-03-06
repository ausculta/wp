jQuery(document).ready(function($) { 
    $('#modalAddExplorers').on('show.bs.modal', function() {
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