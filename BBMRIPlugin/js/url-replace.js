jQuery(document).ready(function($) {
    //Read current domain. Base URL hardcoded.
    const domain = window.location.host;
    const currentURL = window.location.href;
    if (currentURL.includes("/de/")) {
        var baseurl = 'https://' + domain + '/de/start/intranet-2/';
    } else {
        var baseurl = 'https://' + domain + '/home/intranet/';
    }

    if (currentURL.indexOf(baseurl) !== -1) {
        $('a').each(function() {
            var currentHref = $(this).attr('href');

            //Split URL and use latter part to construct working URL w/o CDN.
            if (currentHref.includes('/ee-get-file/') && currentHref.indexOf(baseurl) === -1) {
                var urlParts = currentHref.split('/ee-get-file/');
                var newHref = 'https://' + domain + '/ee-get-file/' + urlParts[1];
                $(this).attr('href', newHref);
            }
        });
    }
});
