jQuery(document).ready(function($) { 
    // Add Explorer button on main plugin page: when button is clicked, create content for modalAddExplorer         
    $( "#btnAddPoll" ).click(function() {
        var newcontent;
        document.getElementById("btnAddOption").disabled = true;
        $.get(ajaxdata_getpoll.ajax_url, {
            _ajax_nonce: ajaxdata_getpoll.nonce,
            action: "get_poll",
            PollID: '-1',
            action_type: "new"
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmEditPoll\" id=\"frmEditPoll\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            // List of explorers goes here from JSON data
            // var nonexpusers = JSON.parse(data);
            newcontent = newcontent + "<input type=\"hidden\" id=\"PollID\" name=\"PollID\" value=\"-1\">"
            newcontent = newcontent + "\t\t\t\t<tr><td>Title:</td><td><input type=\"text\" id=\"txtTitle\" name=\"txtTitle\" size=50 required=\"required\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtDescription\" name=\"txtDescription\" size=50 required=\"required\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Deadline Date:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\" required=\"required\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Deadline Time:</td><td><input type=\"time\" id=\"timeEnd\" name=\"timeEnd\" required=\"required\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>Type of options:</td><td>\n";
            if (newdata['PollTypesNo'] > 0) {
                newcontent = newcontent + "<select id=\"selType\" name=\"selType\" required=\"required\">\n";
                newcontent = newcontent + "\t\t\t\t\t<option value=-1";
                if (newdata.PollTypeID == null) newcontent = newcontent + " selected";
                newcontent = newcontent + ">None selected</option>\n";
                for (var i = 0 ; i < newdata.PollTypesNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<option value=\"" + newdata.PollTypesData[i].PollTypeID + "\"";
                    if (newdata.PollTypeID == newdata.PollTypesData[i].PollTypeID) newcontent = newcontent + " selected";
                    newcontent = newcontent + ">" + newdata.PollTypesData[i].Description; + "</option>\n";
                }
                newcontent = newcontent + "\t\t\t\t\t</select>";
            } else {
                newcontent = newcontent + "Data not available.";
            }
            newcontent = newcontent + "</td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>Poll status:</td><td>\n";
            if (newdata['PollStatusNo'] > 0) {
                newcontent = newcontent + "<select id=\"selStatus\" name=\"selStatus\" required=\"required\">\n";
                newcontent = newcontent + "\t\t\t\t\t<option value=-1";
                if (newdata.PollStatusID == null) newcontent = newcontent + " selected";
                newcontent = newcontent + ">None selected</option>\n";
                for (var i = 0 ; i < newdata.PollStatusNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<option value=\"" + newdata.PollStatusData[i].PollStatusID + "\"";
                    if (newdata.PollStatusID == newdata.PollStatusData[i].PollStatusID) newcontent = newcontent + " selected";
                    newcontent = newcontent + ">" + newdata.PollStatusData[i].Description; + "</option>\n";
                }
                newcontent = newcontent + "\t\t\t\t\t</select>";
            } else {
                newcontent = newcontent + "Data not available.";
            }
            newcontent = newcontent + "</td></tr>\n";
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalEditPollBody").innerHTML = newcontent;
        });
    });
    $( "#btnSavePoll" ).click(function() {
        var formdata = $("#frmEditPoll").serializeArray()

        $.post(ajaxdata_savepoll.ajax_url, {
            _ajax_nonce: ajaxdata_savepoll.nonce,
            action: "save_poll",
            formdata: formdata,
        }, function(newdata) {
            $('#modalEditPoll').modal('hide');
            window.location.reload();
        });
    });
    $("#btnAddOption").on('click', function() {
        document.getElementById("txtOptionDescription").value = "";
        document.getElementById("OptionOptionID").value = "";
        document.getElementById("OptionPollID").value = document.getElementById("PollID").value;
        $('#modalEditOption').modal('show');
    });
    $( "#btnSaveOption" ).click(function() {
        document.getElementById("btnSaveOption").disabled = true;
        var formdata = $("#frmEditOption").serializeArray()
        $.post(ajaxdata_saveoption.ajax_url, {
            _ajax_nonce: ajaxdata_saveoption.nonce,
            action: "save_option",
            formdata: formdata,
        }, function(newdata) {
            $('#modalEditOption').modal('hide');
            if (newdata.PollOptionsNo > 0) {
                // There are options for this poll
                tempcontent = "\t\t\t\t\t<table class=\"table-sm\" border=0>\n";
                for (var i = 0 ; i < newdata.PollOptionsNo ; i++) {
                    tempcontent = tempcontent + "\t\t\t\t\t<tr class=\"polloption\" data-toggle=\"modal\" data-target=\"#modalEditOption\" id=\"" + newdata.PollOptionsData[i].PollOptionID + "\"><td>Option " + (i + 1) + "</td><td>" + newdata.PollOptionsData[i].OptionDescription + "</td></tr>\n";
                }
                tempcontent = tempcontent + "\t\t\t\t\t</table>\n";

                document.getElementById("tdPollOptions").innerHTML = tempcontent;
            }
            document.getElementById("btnSaveOption").disabled = false;
            // window.location.reload();
        });

    });
    $( "#btnSaveReply" ).click(function() {
        var formdata = $("#frmEditReply").serializeArray();

        $.post(ajaxdata_savereply.ajax_url, {
            _ajax_nonce: ajaxdata_savereply.nonce,
            action: "save_reply",
            formdata: formdata,
            PollID: this.id,
        }, function(newdata) {
            var newcontent = "";
            if (newdata.success > 0) {
                alert ("Reply recorded.");
            } else {
                alert ("Error recording your ")
            }
        });
    });
    $("body").on('click', '.poll', function() {
        document.getElementById("btnAddOption").disabled = false;
        $.get(ajaxdata_getpoll.ajax_url, {
            _ajax_nonce: ajaxdata_getpoll.nonce,
            action: "get_poll",
            PollID: this.id,
            action_type: "new"
        }, function(newdata) {
            newcontent = "\t\t\t\t<form name=\"frmEditPoll\" id=\"frmEditPoll\" method=\"POST\" action=\"\">\n";
            newcontent = newcontent + "<input type=\"hidden\" id=\"PollID\" name=\"PollID\" value=\"" + newdata.PollID + "\">"
            newcontent = newcontent + "\t\t\t\t<table class=\"table-sm\">\n";
            // List of explorers goes here from JSON data
            // var nonexpusers = JSON.parse(data);
            newcontent = newcontent + "\t\t\t\t<tr><td>Title:</td><td><input type=\"text\" id=\"txtTitle\" name=\"txtTitle\" size=50 required=\"required\" value=\"" + newdata.PollTitle + "\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Description:</td><td><input type=\"text\" id=\"txtDescription\" name=\"txtDescription\" size=50 required=\"required\" value=\"" + newdata.PollDescription + "\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Deadline Date:</td><td><input type=\"date\" id=\"dateEnd\" name=\"dateEnd\" required=\"required\" value=\"" + newdata.DeadlineDate + "\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td>Deadline Time:</td><td><input type=\"time\" id=\"timeEnd\" name=\"timeEnd\" required=\"required\" value=\"" + newdata.DeadlineTime + "\"></td></tr>\n";
            newcontent = newcontent + "\t\t\t\t\t<tr><td>Status:</td><td>";
            if (newdata['PollStatusNo'] > 0) {
                newcontent = newcontent + "<select id=\"selStatus\" name=\"selStatus\" required=\"required\">\n";
                newcontent = newcontent + "\t\t\t\t\t<option value=-1";
                if (newdata.PollStatusID == null) newcontent = newcontent + " selected";
                newcontent = newcontent + ">None selected</option>\n";
                for (var i = 0 ; i < newdata.PollStatusNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<option value=\"" + newdata.PollStatusData[i].PollStatusID + "\"";
                    if (newdata.PollStatusID == newdata.PollStatusData[i].PollStatusID) newcontent = newcontent + " selected";
                    newcontent = newcontent + ">" + newdata.PollStatusData[i].Description; + "</option>\n";
                }
                newcontent = newcontent + "\t\t\t\t\t</select>";
            } else {
                newcontent = newcontent + "Data not available.";
            }
            newcontent = newcontent + "</td></tr>\n";

            if (newdata['PollTypesNo'] > 0) {
                newcontent = newcontent + "\t\t\t\t\t<tr><td>Type of options:</td><td><select id=\"selType\" name=\"selType\" required=\"required\">\n";
                newcontent = newcontent + "\t\t\t\t\t<option value=-1";
                if (newdata.PollTypeID == null) newcontent = newcontent + " selected";
                newcontent = newcontent + ">None selected</option>\n";
                for (var i = 0 ; i < newdata.PollTypesNo ; i++) {
                    newcontent = newcontent + "\t\t\t\t\t<option value=\"" + newdata.PollTypesData[i].PollTypeID + "\"";
                    if (newdata.PollTypeID == newdata.PollTypesData[i].PollTypeID) newcontent = newcontent + " selected";
                    newcontent = newcontent + ">" + newdata.PollTypesData[i].Description; + "</option>\n";
                }
                newcontent = newcontent + "\t\t\t\t\t</select>";
            } else {
                newcontent = newcontent + "Data not available.";
            }
            newcontent = newcontent + "</td></tr>\n";
            newcontent = newcontent + "\t\t\t\t<tr><td class=\"align-top\">Poll options:</td><td name=\"tdPollOptions\" id=\"tdPollOptions\"></td></tr>\n";
            if (newdata.PollOptionsNo > 0) {
                // There are options for this poll
                optionscontent = "\t\t\t\t\t<table class=\"table-sm\" border=0>\n";
                for (var i = 0 ; i < newdata.PollOptionsNo ; i++) {
                    optionscontent = optionscontent + "\t\t\t\t\t<tr class=\"polloption\" data-toggle=\"modal\" data-target=\"#modalEditOption\" id=\"" + newdata.PollOptionsData[i].PollOptionID + "\">";
                    optionscontent = optionscontent + "<td>Option " + (i + 1) + "</td><td>" + newdata.PollOptionsData[i].OptionDescription + "</td></tr>\n";
                }
                optionscontent = optionscontent + "\t\t\t\t\t</table>\n";
            } else {
                optionscontent = "No options defined for this poll, yet.";
            }
            newcontent = newcontent + "\t\t\t\t<tr><td class=\"align-top\">Poll replies:</td><td name=\"tdPollReplies\" id=\"tdPollReplies\"></td></tr>\n";
            if (newdata.PollRepliesNo > 0) {
                // There are options for this poll
                repliescontent = "\t\t\t\t\t<table class=\"table-sm\" border=0>\n";
                for (var i = 0 ; i < newdata.PollRepliesNo ; i++) {
                    repliescontent = repliescontent + "\t\t\t\t\t<tr class=\"pollreply\" data-toggle=\"modal\" data-target=\"#modalEditReply\" id=\"" + newdata.PollRepliesData[0].PollReplyID + "\">\n";
                    repliescontent = repliescontent + "\t\t\t\t\t<td>" + newdata.PollRepliesData[i].display_name + "</td><td>" + newdata.PollRepliesData[i].ReplyValue + "</td><td>" + newdata.PollRepliesData[i].ReplyComment + "</td><td>" + newdata.PollRepliesData[i].ReplyDate + "</td>\n";
                    repliescontent = repliescontent + "\t\t\t\t\t</tr>\n";
                }
                repliescontent = repliescontent + "\t\t\t\t\t</table>\n";
            } else {
                repliescontent = "No replies for this poll, yet.";
            }
            newcontent = newcontent + "\t\t\t\t</table>\n";
            newcontent = newcontent + "\t\t\t\t</form>\n";
            document.getElementById("modalEditPollBody").innerHTML = newcontent;
            document.getElementById("tdPollOptions").innerHTML = optionscontent;
            document.getElementById("tdPollReplies").innerHTML = repliescontent;
        });
    });
    $("body").on('click', '.polloption', function() {
        $.get(ajaxdata_getoption.ajax_url, {
            _ajax_nonce: ajaxdata_getoption.nonce,
            action: "get_option",
            PollOptionID: this.id,
        }, function(newdata) {
            if (newdata.success > 0) {
                document.getElementById("txtOptionDescription").value = newdata.PollDescription;
                document.getElementById("OptionOptionID").value = newdata.PollOptionID;
                document.getElementById("OptionPollID").value = newdata.PollID;
            }
        });
    });
    $("body").on('click', '.reply', function() {
        $.get(ajaxdata_getreply.ajax_url, {
            _ajax_nonce: ajaxdata_getreply.nonce,
            action: "get_reply",
            PollID: this.id,
        }, function(newdata) {
            var newcontent = "";
        });
    });
    $('.modal').on("hidden.bs.modal", function (e) { 
        if ($('.modal:visible').length) { 
            $('body').addClass('modal-open');
        }
    });
});

