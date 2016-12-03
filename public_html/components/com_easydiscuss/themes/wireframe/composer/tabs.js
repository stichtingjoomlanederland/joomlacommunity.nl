ed.require(['edq'], function($) {

    // Set the first tab as active.
    var active = $('[data-ed-ask-tabs]').children(':first');

    // Get the tab content
    var activeContent = $(active.find('a').attr('href'));

    // Add active classes
    active.addClass('active');
    activeContent.addClass('active in')
});