$(document).ready(function() {
    // validator for forms
    $('form').bootstrapValidator();

    // images gallery photoswipe
    initPhotoSwipeFromDOM('.images-gallery');

    $('#dishes').isotope({
        itemSelector : '.item'
    });
});

function showMyAlertModal(content, title) {

    $('#messageGeolocate').html(
        (typeof title != 'undefined' ? '<b>'+title+'</b> ' : '')
        + '<i>' + content + '</i>'
    );

    /*
    var myAlertModal = $('#myAlertModal');
    myAlertModal.find('.modal-body').text(content);

    if (title != undefined) {
        myAlertModal.find('.modal-title').text(title);
    }

    myAlertModal.modal('show');
    */
}

function hideMyAlertModal() {
//    $('#myAlertModal').modal('hide');
}

function setCookie(cname, cvalue, expireseconds) {
    var d = new Date();
    d.setTime(d.getTime() + (expireseconds*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) != -1) return c.substring(name.length,c.length);
    }
    return "";
}

function encodeRFC5987ValueChars (str) {
    return encodeURIComponent(str).
        // Note that although RFC3986 reserves "!", RFC5987 does not,
        // so we do not need to escape it
        replace(/['()]/g, escape). // i.e., %27 %28 %29
        replace(/\*/g, '%2A').
        // The following are not required for percent-encoding per RFC5987,
        // so we can allow for a little better readability over the wire: |`^
        replace(/%(?:7C|60|5E)/g, unescape);
}