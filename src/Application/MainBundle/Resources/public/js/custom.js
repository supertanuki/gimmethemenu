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

        console.log('data-src = ' + $(this).attr('data-src'));

        var span = $(this).find($('span'));
        if ($(this).hasClass('btn-default')) {
            // add
            $(this).removeClass('btn-default');
            $(this).addClass('btn-warning');
            span.removeClass('glyphicon glyphicon-heart-empty');
            span.addClass('glyphicon glyphicon-heart');
            $(this).blur();
        } else {
            // remove
            $(this).removeClass('btn-warning');
            $(this).addClass('btn-default');
            span.removeClass('glyphicon glyphicon-heart');
            span.addClass('glyphicon glyphicon-heart-empty');
            $(this).blur();
        }



        $.post( $(this).attr('data-src'), function( data ) {});

        return false;
    });

    $('form.comment-form').unbind('submit').submit(function(event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        var target = $(this).attr('data-add-comment');
        var input = $(this).find($('input[type=text]'));
        var templateHtml = $('#comment_template').html();
        var comment = input.val();
        input.val('');

        var html = $(templateHtml);
        html.find('span.comment').append(strip_tags(comment));
        $('#' + target).append(html);

        // hide comments link
        $('a[data-open-comments="' + target + '"]').hide();

        if (!$('#' + target).is(':visible')) {
            $('#' + target).slideDown();
        };

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

function strip_tags(input, allowed) {
    //  discuss at: http://phpjs.org/functions/strip_tags/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Luke Godfrey
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //    input by: Pul
    //    input by: Alex
    //    input by: Marc Palau
    //    input by: Brett Zamir (http://brett-zamir.me)
    //    input by: Bobby Drake
    //    input by: Evertjan Garretsen
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Onno Marsman
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Eric Nagel
    // bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Tomasz Wesolowski
    //  revised by: Rafał Kukawski (http://blog.kukawski.pl/)
    //   example 1: strip_tags('<p>Kevin</p> <br /><b>van</b> <i>Zonneveld</i>', '<i><b>');
    //   returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
    //   example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
    //   returns 2: '<p>Kevin van Zonneveld</p>'
    //   example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>", "<a>");
    //   returns 3: "<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>"
    //   example 4: strip_tags('1 < 5 5 > 1');
    //   returns 4: '1 < 5 5 > 1'
    //   example 5: strip_tags('1 <br/> 1');
    //   returns 5: '1  1'
    //   example 6: strip_tags('1 <br/> 1', '<br>');
    //   returns 6: '1 <br/> 1'
    //   example 7: strip_tags('1 <br/> 1', '<br><br/>');
    //   returns 7: '1 <br/> 1'

    allowed = (((allowed || '') + '')
        .toLowerCase()
        .match(/<[a-z][a-z0-9]*>/g) || [])
        .join(''); // making sure the allowed arg is a string containing only tags in lowercase (<a><b><c>)
    var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi,
        commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
    return input.replace(commentsAndPhpTags, '')
        .replace(tags, function ($0, $1) {
            return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
        });
}