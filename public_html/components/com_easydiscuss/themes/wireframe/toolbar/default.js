ed.require(['edq', 'site/src/toolbar', 'site/src/discuss'], function($, App, discuss) {

    var toolbarSelector = '[data-ed-toolbar]';

    // Implement the abstract
    App.execute(toolbarSelector, {
        "notifications": {
            "interval": <?php echo $this->config->get('main_notifications_interval') * 1000; ?>,
            "enabled": <?php echo $this->my->id && $this->config->get('main_notifications') ? 'true' : 'false';?>
        },
        "conversations": {
            "interval": <?php echo $this->config->get('main_conversations_notification_interval') * 1000 ?>,
            "enabled": <?php echo $this->my->id && $this->config->get('main_conversations') && $this->config->get('main_conversations_notification') ? 'true' : 'false';?>
        }
    });

    // Logout button
    $('[data-ed-toolbar-logout]').live('click', function() {
        $('[data-ed-toolbar-logout-form]').submit();
    });


    // search
    $('[data-search-input]').live('keydown', function(e) {
        if (e.keyCode == 13) {
            $('[data-search-toolbar-form]').submit();
        }
    });

    // Toggle sidebar for mobile view
    var toggleSubmenu = $('[data-ed-navbar-submenu-toggle]');
    var submenu = $('[data-ed-navbar-submenu]');

    toggleSubmenu.on('click', function(event) {
        if($(submenu).hasClass("is-open")) {
            $(submenu).removeClass("is-open");
        } else {
            $(submenu).removeClass("is-open");
            $(submenu).addClass("is-open");
        }
   });

    <?php if ($this->config->get('main_responsive')) { ?>
    $.responsive($(toolbarSelector), {
        elementWidth: function() {
            return $(toolbarSelector).outerWidth(true) - 80;
        },
        conditions: {
            at: (function() {
                var listWidth = 0;

                $(toolbarSelector + ' .nav > li').each(function(i, element) {
                    listWidth += $(element).outerWidth(true);
                });
                return listWidth;

            })(),
            alsoSwitch: {
                toolbarSelector: 'narrow'
            },
            targetFunction: function() {
                $(toolbarSelector).removeClass('wide');
            },
            reverseFunction: function() {
                $(toolbarSelector).addClass('wide');
            }
        }

    });
    <?php } ?>

});
