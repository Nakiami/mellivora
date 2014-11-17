$(document).ready(function() {
    highlightSelectedMenuItem();
    highlightLoggedOnTeamName();

    initialiseLoginDialog();
    initialiseTooltips();
    initialiseCountdowns();
});

function initialiseLoginDialog() {
    $('#login-dialog').on('shown.bs.modal', function (e) {
        $('#login-dialog').find('input').first().focus();
    });
}

function highlightSelectedMenuItem() {
    var path = window.location.pathname;
    var activeMenuItems = document.querySelectorAll('.nav a[href*="' + path + '"]');

    for (var i = 0; i < activeMenuItems.length; i++) {
        if (activeMenuItems[i] && activeMenuItems[i].parentNode) {
            activeMenuItems[i].parentNode.className = 'active';
        }
    }
}

function highlightLoggedOnTeamName() {
    $(".team_" + global_dict["user_id"]).addClass("label label-info");
}

function initialiseCountdowns() {
    var $countdowns = $('[data-countdown]');
    var countdownsOnPage = $('[data-countdown]').length;

    if (countdownsOnPage) {
        setInterval(function() {
            $countdowns.each(function () {
                var $countdown = $(this);
                var availableUntil = $countdown.data('countdown');
                var availableUntilDate = new Date(availableUntil * 1000);
                var secondsLeft = Math.floor((availableUntilDate.getTime() - Date.now()) / 1000);

                var doneMessage = $countdown.attr('data-countdown-done') || 'No time remaining';
                var countdownMessage = secondsLeft <= 0 ? doneMessage : prettyPrintTime(secondsLeft);
                $countdown.text(countdownMessage);
            });

        }, 1000);
    }
}

function initialiseTooltips() {
    $('.has-tooltip').tooltip();
}

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