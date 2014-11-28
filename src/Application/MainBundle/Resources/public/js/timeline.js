$(document).ready(function() {
    $('#timeline_filter_reviews a').on('click', function() {
        if ($('#timeline_filter_reviews').hasClass('active')) {
            return;
        }

        $('#timeline_filter_reviews').addClass('active');
        $('#timeline_filter_all').removeClass('active');
        $('.timeline .collapse').hide();
    });

    $('#timeline_filter_all a').on('click', function() {
        if ($('#timeline_filter_all').hasClass('active')) {
            return;
        }

        $('#timeline_filter_all').addClass('active');
        $('#timeline_filter_reviews').removeClass('active');
        $('.timeline .collapse').show();
    });
});