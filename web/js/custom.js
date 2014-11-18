$(document).ready(function() {
    $('input').iCheck();
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
