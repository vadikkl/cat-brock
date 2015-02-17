/**
 * Created by birlaver on 21.10.14.
 */

function updateContentHeight() {
    var $height = $('#nav-col').height();
    $('#content-wrapper').css({'min-height': $height});
}

$(window)
.load(function () {
    updateContentHeight();
})
.resize(function () {
    updateContentHeight();
});

jQuery('table thead th a').on('click', function() {
    if (jQuery('span', this).attr('class')) {
        if (jQuery(this).hasClass('asc')) {
            jQuery(this).removeClass('asc').addClass('desc');
        } else if (jQuery(this).hasClass('desc')) {
            jQuery(this).removeClass('desc').addClass('asc');
        } else {
            jQuery(this).addClass('asc');
        }
        var path = document.URL.split(window.location.host);
        jQuery.cookie('sort_table' ,jQuery('span', this).attr('class')+' '+jQuery(this).attr('class'), {path: path[1]});
        location.reload();
    }
    return false;
});
jQuery(document).ready(function() {
    if (jQuery.cookie('sort_table')) {
        var sort_cookie = jQuery.cookie('sort_table').split(' ');
        jQuery('table thead th .'+sort_cookie[0]).parents('a').addClass(sort_cookie[1]);
    }
});