jQuery(document).ready(function($) {
    $('#btnSaveExplorers').click(function() {
        var i = 0;
        var nonexpform = $("#frmAllExplorers").serializeArray()
        $.post(ajaxdata_newexplorers.ajax_url, {
           _ajax_nonce: ajaxdata_newexplorers.nonce,
            action: "save_newexplorers",
            dbdata: nonexpform,
        }, function(newdata) {
            document.getElementById("modalAddExplorersBody").innerHTML = "<h5 class=\"text-align-center\">Inserted " + newdata.dbrecords + " records.</h5>\n";
        });
    });
});
