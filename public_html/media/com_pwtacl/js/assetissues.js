jQuery(document).ready(function () {
    jQuery('#system-message-container').prepend(
        '<div class="alert alert-warning alert-joomlaupdate">'
        + pwtacl.PWTACLDIAGNOSTICS_MESSAGE
        + ' <button class="btn btn-primary" onclick="document.location=\'' + pwtacl.PWTACLDIAGNOSTICS_URL + '\'">' + pwtacl.PWTACLDIAGNOSTICS_BUTTON + '</button>'
        + '</div>'
    );
});