$(document).ready(function () {
    $(".team_" + global_dict["user_id"]).addClass("label label-info");

    $('#login-dialog').on('shown.bs.modal', function (e) {
        $('#login-dialog').find('input').first().focus();
    });

    $('.has-tooltip').tooltip();
});

