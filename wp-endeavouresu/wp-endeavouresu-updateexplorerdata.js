jQuery(document).ready(function($) { 
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
                document.getElementById("modalUpdateExplorer").innerHTML = "<h5 class=\"text-align-center\">An error occured: the database was not updated.</h5>\n";
            }
        });
    });
});
