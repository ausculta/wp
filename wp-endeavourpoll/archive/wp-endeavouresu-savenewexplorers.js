jQuery(document).ready(function($) {
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
});
