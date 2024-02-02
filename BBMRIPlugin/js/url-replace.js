jQuery(document).ready(function($) {
    //Read current domain. Base URL hardcoded.
    let inputcheck = ['', 'eeSFL_FileNameNew']
    const domain = window.location.host;
    const currentURL = window.location.href;
    if (currentURL.includes("/de/")) {
        var baseurl = 'https://' + domain + '/de/start/intranet-2/';
    } else {
        var baseurl = 'https://' + domain + '/home/intranet/';
    }

    var pattern_count = 0;
    function updatePattern(element) {
        const currentName = $(element).attr('name');
        if (currentName === 'eeSFL_NewFolderName') {
            //$(element).attr('required', 'required');
            $(element).attr('pattern', '^[a-zA0-9_\\-]*$');
            $(element).attr('title', 'Only alphanumeric characters, as well as _ and - allowed');
        } else if (currentName === 'eeSFL_FileNameNew') {

        } else if (pattern_count > 0) {
            $(element).removeAttr('pattern');
            $(element).removeAttr('title');
        } else {}
    }

    if (currentURL.indexOf(baseurl) !== -1) {
        //Include links and copy link button.
        $('a').each(function() {
            var currentHref = $(this).attr('href');
            var currentCopyLink = $(this).attr('onclick');
            //Check if URL fits CDN or original domain name, split URL and use latter part to construct working URL w/o CDN.
            if (currentHref && currentHref.includes('/ee-get-file/') && currentHref.indexOf(baseurl) === -1) {
                var urlParts = currentHref.split('/ee-get-file/');
                var newHref = 'https://' + domain + '/ee-get-file/' + urlParts[1];
                $(this).attr('href', newHref);
            }
            if (currentCopyLink && currentCopyLink.includes('/ee-get-file/') && currentCopyLink.indexOf(baseurl) === -1) {
                var urlParts = currentCopyLink.split('/ee-get-file/');
                var newCopyLink = 'eeSFL_CopyLinkToClipboard(\'https://' + domain + '/ee-get-file/' +urlParts[1];
                $(this).attr('onclick', newCopyLink)
            }
        });
        
        //Insert pattern into relevant forms once on loading page.
        $('input[type="text"]').each(function() {
                updatePattern(this);
                pattern_count += 1;
        });

        //Observe change in FileOpsActions to remove and re-add pattern as needed.
        const targetElement = document.getElementById('eeSFL_FileOpsActionInput');
        const observer = new MutationObserver(function(mutationsList) {
            mutationsList.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'name') {
                    updatePattern(targetElement);
                }
            });
        });
        const observerConfig = { attributes: true };
        observer.observe(targetElement, observerConfig);

        //Start with unknown value. If pattern matches, do nothing, else reset the input value and return an error message.
        var previousValidValue = 'unknown';
        document.getElementById('eeSFL_FileNameNew').addEventListener('input', function() {
            var inputField = this.value;
            var pattern = /^[a-zA-Z0-9-_]+(\.[a-zA-Z0-9-_]+)?\.(avi|bmp|csv|doc|docm|docx|jpeg|jpg|mov|mp3|mp4|msg|odp|ods|odt|pdf|png|ppt|pptm|pptx|svg|tif|tiff|tsv|txt|vsd|xls|xlsm|xlsx|zip)$/;
            var pattern2 = /^[a-zA-Z0-9-_]*$/;
            
            if (!pattern.test(inputField) && !pattern2.test(inputField)) {
                alert('Invalid input. Only alphanumeric characters, as well as \'_\' and \'-\' allowed. Use "File Nice Name" for complex names.');
                if (previousValidValue == 'unknown') {
                    previousValidValue = document.querySelector('#eeSFL_Modal_EditFile > div:nth-child(2) > p:nth-child(3)').innerHTML;
                    this.value = previousValidValue;
                } else {
                    this.value = previousValidValue;
                }
            } else {
                previousValidValue = inputField;
            }
        });
        function resetValidValue() {
            previousValidValue = 'unknown'
        }

        //Reset previously saved state to 'unknown'. When Modal Form is closed.
        const modal_button1 = document.querySelector('.button[onclick="eeSFL_FileEditSaved()"]');
        const modal_button2 = document.querySelector('.eeSFL_ModalClose');
        if (modal_button1 && modal_button2) {
            modal_button1.addEventListener('unknown', resetValidValue);
            modal_button2.addEventListener('unknown', resetValidValue);
        } else {
            //console.log("Not found");
        }
    }
});
