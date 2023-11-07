jQuery(document).ready(function($) {
    // Check the current URL to restrict the execution of the script
    if (window.location.href.indexOf("https://d1u.c1c.myftpupload.com/intranet/") !== -1) {
        var urlPattern1 = /https:\/\/d1uc1c\.n3cdn1\.secureserver\.net\/ee-get-file\//g;
        //var urlPattern2 = /https:\/\/d1u\.c1c\.myftpupload\.com\/ee-get-file\//g;

        // Function to replace the URLs on the page
        function replaceURLs() {
            $('a').each(function() {
                var currentHref = $(this).attr('href');

                if (urlPattern1.test(currentHref)) {
                    var newHref = currentHref.replace(urlPattern1, "https://d1u.c1c.myftpupload.com/ee-get-file/");
                    $(this).attr('href', newHref);
                }
            });
        }

        // Call the function to replace URLs
        replaceURLs();
    }
});