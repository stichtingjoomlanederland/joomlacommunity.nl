ed.define('dependencies', ['edq', 'easydiscuss'], function($, EasyDiscuss){

    function isMobile() {
        try{
            document.createEvent("TouchEvent");
            return true;
        }
        catch(e) {
            return false;
        }
    }

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

    // // Tooltips
    // // detect if mouse is being used or not.
    // var tooltipLoaded = false;
    // var mouseCount = 0;
    // window.onmousemove = function() {

    //     mouseCount++;

    //     addTooltip();
    // };

    // var addTooltip = $._.debounce(function(){

    //     if (!tooltipLoaded && mouseCount > 10) {

    //         tooltipLoaded = true;
    //         mouseCount = 0;

    //         $(document).on('mouseover.tooltip.data-ed-api', '[data-ed-provide=tooltip]', function() {

    //             $(this)
    //                 .tooltip({
    //                     delay: {
    //                         show: 200,
    //                         hide: 100
    //                     },
    //                     animation: false,
    //                     template: '<div id="ed" class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',
    //                     container: 'body'
    //                 })
    //                 .tooltip("show");
    //         });
    //     } else {
    //         mouseCount = 0;
    //     }
    // }, 500);

    // Popovers
    $(document).on("mouseover", "[rel=ed-popover]", function(){
        $(this).popover({container: 'body', delay: { show: 100, hide: 100},animation: false, trigger: 'hover'});
    });


    // Subscriptions
    $(document)
        .on('click', "[data-ed-subscribe]", function() {

            var el = $(this);
            var type = el.data('type');
            var cid = el.data('cid');

            EasyDiscuss.dialog({
                content: EasyDiscuss.ajax('site/views/subscription/form', {
                    "type": type,
                    "cid": cid
                })
            });
        });

    $(document)
        .on('click', "[data-ed-unsubscribe]", function() {

            var el = $(this);
            var sid = el.data('sid');
            var type = el.data('type');
            var cid = el.data('cid');

            EasyDiscuss.dialog({
                content: EasyDiscuss.ajax('site/views/subscription/unsubscribeDialog', {
                    "sid": sid,
                    "type": type,
                    "cid": cid
                })
            });
        });
});
