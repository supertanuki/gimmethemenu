$(document).ready(function() {
    $('input').iCheck();
});

function showMyAlertModal(content) {
    var myAlertModal = $('#myAlertModal');
    myAlertModal.find('.modal-body').text(content);
    myAlertModal.modal('show');
}
