ed.require.config({
    baseUrl: window.ed_site ? window.ed_site + 'media/com_easydiscuss/scripts' : '/media/com_easydiscuss/scripts',
    paths: {
        'abstract': 'vendors/abstract',

        // EasyDiscuss version of jquery
        'easydiscuss': 'vendors/easydiscuss',
        'edjquery': 'vendors/jquery',
        'jquery.utils': 'vendors/jquery.utils',
        'jquery.uri': 'vendors/jquery.uri',
        'jquery.server': 'vendors/jquery.server',
        'jquery.migrate': 'vendors/jquery.migrate',
        'jquery.flot': 'admin/vendors/jquery.flot',
        'jquery.joomla': 'admin/vendors/jquery.joomla',
        'bootstrap.colorpicker': 'admin/vendors/bootstrap.colorpicker',

        'dialog': 'vendors/dialog',
        'bootstrap': 'vendors/bootstrap',
        'lodash': 'vendors/lodash',
        'chartjs': 'vendors/chart',
        'selectize': 'vendors/selectize',
        'composer': 'vendors/composer',
        'markitup': 'vendors/markitup',
        'jquery.expanding': "vendors/jquery.expanding",
        'jquery.atwho': 'vendors/jquery.atwho',
        'jquery.caret': 'vendors/jquery.caret',
        'chosen': 'vendors/jquery.chosen'
    }
});

ed.define('edq', ['edjquery', 'easydiscuss', 'jquery.uri', 'bootstrap', 'jquery.utils', 'jquery.migrate', 'jquery.server', 'lodash', 'dialog', 'jquery.joomla', 'chartjs'], function($) {

    // Implement popover
    $(document).on("mouseover", "[rel=ed-popover]", function(){
        $(this).popover({container: 'body', delay: { show: 100, hide: 100},animation: false, trigger: 'hover'});
    });

    function isMobile() {
      try{ document.createEvent("TouchEvent"); return true; }
      catch(e){ return false; }
    }

    // Tooltips
    // detect if mouse is being used or not.
    var tooltipLoaded = false;
    var mouseCount = 0;
    window.onmousemove = function() {

        mouseCount++;

        addTooltip();
    };

    var addTooltip = $._.debounce(function(){

        if (!tooltipLoaded && mouseCount > 10) {

            tooltipLoaded = true;
            mouseCount = 0;

            $(document).on('mouseover.tooltip.data-ed-api', '[data-ed-provide=tooltip]', function() {

                $(this)
                    .tooltip({
                        delay: {
                            show: 200,
                            hide: 100
                        },
                        animation: false,
                        template: '<div id="ed" class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
                        container: 'body'
                    })
                    .tooltip("show");
            });
        } else {
            mouseCount = 0;
        }
    }, 500);


    return $;
});
