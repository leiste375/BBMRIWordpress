jQuery(document).ready(function($) {
    //Read current domain. Base URL hardcoded to https://example.com/intranet/.
    const domain = window.location.host;
    const baseurl = 'https://' + domain + '/intranet/';

    if (window.location.href.indexOf(baseurl) !== -1) {
        $('a').each(function() {
            var currentHref = $(this).attr('href');

            if (currentHref.includes('/ee-get-file/') && currentHref.indexOf(baseurl) === -1) {
                //Save string after ee-get-file.
                var urlParts = currentHref.split('/ee-get-file/');
                
                //Construct URL to forego CDN.
                var newHref = 'https://' + domain + '/ee-get-file/' + urlParts[1];
                $(this).attr('href', newHref);
            }
        });
    }
});
