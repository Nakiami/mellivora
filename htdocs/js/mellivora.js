$(document).ready(function () {
    $(".team_" + global_dict["user_id"]).addClass("label label-info");

    $('#login-modal').on('hidden.bs.modal', function (e) {
        $('#login-modal').first('input').focus();
    });
});

