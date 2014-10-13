function pluralise(number, name) {
    if (!number) {
        return '';
    }

    return number + ' ' + name + (number > 1 ? 's' : '');

}

function prettyPrintTime(seconds) {
    seconds = Math.floor(seconds);
    var minutes = Math.floor(seconds / 60);
    var hours = Math.floor(minutes / 60);
    var days = Math.floor(hours / 24);

    var daysWords = pluralise(days, 'day');
    var hoursWords = pluralise(hours % 24, 'hour');
    var minutesWords = pluralise(minutes % 60, 'minute');
    var secondsWords = pluralise(seconds % 60, 'second');

    var timeParts = [];
    if (daysWords) timeParts.push(daysWords);
    if (hoursWords) timeParts.push(hoursWords);
    if (minutesWords) timeParts.push(minutesWords);
    if (secondsWords) timeParts.push(secondsWords);


    return timeParts.join(', ') + ' remaining';
}


$(document).ready(function () {
    $(".team_" + global_dict["user_id"]).addClass("label label-info");

    $('#login-dialog').on('shown.bs.modal', function (e) {
        $('#login-dialog').find('input').first().focus();
    });

    $('.has-tooltip').tooltip();

    var $countdowns = $('[data-countdown]');
    var countdownsOnPage = $('[data-countdown]').length;
    if (countdownsOnPage) {
        setInterval(function() {
            $countdowns.each(function () {
                var $countdown = $(this);
                var availableUntil = $countdown.data('countdown');
                var availableUntilDate = new Date(availableUntil * 1000);
                var secondsLeft = (availableUntilDate.getTime() - Date.now()) / 1000;
                $countdown.text(prettyPrintTime(secondsLeft));
            });

        }, 1000);
    }
});