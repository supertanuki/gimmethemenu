$(document).ready(function() {
//    $('input').iCheck();
});

function showMyAlertModal(content, title) {
    var myAlertModal = $('#myAlertModal');
    myAlertModal.find('.modal-body').text(content);

    if (title != undefined) {
        myAlertModal.find('.modal-title').text(title);
    }

    myAlertModal.modal('show');
}

function hideMyAlertModal() {
    $('#myAlertModal').modal('hide');
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