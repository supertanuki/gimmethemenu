$(document).ready(function() {
    // validator for forms
    $('form').bootstrapValidator();

    $('.readmore').readmore({
        speed: 75,
        maxHeight: 20
    });

    // images gallery photoswipe
    initPhotoSwipeFromDOM('.images-gallery');

    /* activate jquery isotope */
    $('#dishes').imagesLoaded( function(){
        $('#dishes').isotope({
            itemSelector : '.item'
        });
    });

    $('a[data-open-comments]').on('click', function() {
        $('#' + $(this).attr('data-open-comments')).slideDown();
        $(this).hide();
        return false;
    });

    $('button[data-yummy]').on('click', function() {
        var span = $(this).find($('span'));
        if ($(this).hasClass('btn-default')) {
            $(this).removeClass('btn-default');
            $(this).addClass('btn-warning');
            span.removeClass('glyphicon glyphicon-heart-empty');
            span.addClass('glyphicon glyphicon-heart');
            $(this).blur();
        } else {
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-default');
            span.removeClass('glyphicon glyphicon-heart');
            span.addClass('glyphicon glyphicon-heart-empty');
            $(this).blur();
        }

        return false;
    });

    $('form.comment-form').unbind('submit').submit(function(event) {

        event.preventDefault();
        event.stopImmediatePropagation();

        var target = '#' + $(this).attr('data-add-comment');
        var input = $(this).find($('input[type=text]'));
        var templateHtml = $('#comment_template').html();
        var comment = input.val();
        input.val('');

        var html = $(templateHtml);
        html.find('span.comment').append(comment);
        $(target).after(html);
        input.blur();
        return false;
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