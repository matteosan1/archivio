var maxInactiveTime = 1800000;
var logoutUrl = '/logout.php';
var startTime = +new Date();

function checkForWarning() {
    if (+new Date() - startTime > maxInactiveTime) {
	window.location = logoutUrl;
    }
}

function ResetTimeoutTimer() {
    startTime = +new Date();
}

$(document)
    .on('click', ResetTimeoutTimer)
    .on('mousemove', ResetTimeoutTimer);
