ed.require(['edq', 'easydiscuss', 'responsive'], function($, EasyDiscuss) {

    // Apply responsive on wrapper
    var wrapper = $('[data-ed-wrapper]');

    wrapper.responsive([
            // {at: 818,  switchTo: 'w768'},
            {at: 640,  switchTo: 'w640'},
            {at: 480,  switchTo: 'w480'}
        ]);

    // Apply responsive on searchbar
    var searchBar = $('[data-ed-searchbar]');

    searchBar.responsive([
        {at: 600, switchTo: "narrow"}
    ]);

    window.insertVideoCode = function(videoURL , caretPosition , elementId) {

        if (videoURL.length == 0) {
            return false;
        }

        var textarea = $('textarea[name=' + elementId + ']');
        var tag = '[video]' + videoURL + '[/video]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0) {

            $(textarea).val(tag);
            EasyDiscuss.dialog().close();
            return true;
        }

        var contents = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
    };

    window.insertPhotoCode = function(photoURL , caretPosition , elementId) {

        if (photoURL.length == 0) {
            return false;
        }

        var textarea = $('textarea[name=' + elementId + ']');
        var tag = '[img]' + photoURL + '[/img]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0) {

            $(textarea).val(tag);
            EasyDiscuss.dialog().close();
            return true;
        }

        var contents = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
    };

    window.insertLinkCode = function(linkURL , linkTitle, caretPosition , elementId) {

        if (linkURL.length == 0) {
            return false;
        }

        if (linkTitle.length == 0) {
            linkTitle = 'Title';
        }

        var textarea = $('textarea[name=' + elementId + ']');
        var tag = '[url=' + linkURL + ']'+ linkTitle +'[/url]';

        // If this is at the first position, we don't want to do anything here.
        if (caretPosition == 0) {

            $(textarea).val(tag);
            EasyDiscuss.dialog().close();
            return true;
        }

        var contents = $(textarea).val();

        $(textarea).val(contents.substring(0, caretPosition) + tag + contents.substring(caretPosition, contents.length));
    };
});
