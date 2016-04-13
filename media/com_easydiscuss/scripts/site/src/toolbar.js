ed.define('site/src/toolbar', ['edq', 'easydiscuss', 'abstract'], function($, EasyDiscuss, Abstract){

    var App = new Abstract(function(self) {

        return {
            opts: {
                '{items}'   : '.toolbarItem',
                '{dropdowns}'   : '.dropdown-menu',

                // Notifications
                "notifications": {
                    "enabled": false,
                    "interval": 30000
                },
                "{notificationWrapper}": "[data-ed-notifications-wrapper]",
                "{notificationCounter}": "[data-ed-notifications-counter]",

                // Conversations
                "conversations": {
                    "enabled": false,
                    "interval": 30000
                },
                "{conversationWrapper}": "[data-ed-conversations-wrapper]",
                "{conversationCounter}": "[data-ed-conversations-counter]",

                // Login
                '{loginLink}'   : '.loginLink',
                '{loginDropDown}'   : '.loginDropDown',

                // Profile
                '{profileLink}' : '.profileLink',
                '{profileDropDown}' : '.profileDropDown'
            },

            init: function() {

                // Check for new notification items
                self.checkNotifications();

                // Check for new conversations
                self.checkConversations();
            },

            checkNotifications: function() {

                // We should only run this if necessary
                if (!self.options.notifications.enabled) {
                    return;
                }

                (function poll(){
                    setTimeout(function() {

                        EasyDiscuss
                            .ajax('site/views/notifications/count',{}, {
                                "type": "jsonp"
                            })
                            .done(function(count) {

                                // If there is nothing new, just skip this.
                                if (count == 0) {
                                    self.notificationWrapper().removeClass('has-new');
                                    poll();
                                    return;
                                }

                                // Update the counter
                                self.notificationWrapper().addClass('has-new');
                                self.notificationCounter().html(count);
                                poll();
                            });

                    }, self.options.notifications.interval);
                })();
            },

            checkConversations: function() {

                // We should only run this if necessary
                if (!self.options.conversations.enabled) {
                    return;
                }

                (function poll(){
                    setTimeout(function() {

                        EasyDiscuss
                            .ajax('site/views/conversation/count',{}, {
                                "type": "jsonp"
                            })
                            .done(function(count) {

                                // If there is nothing new, just skip this.
                                if (count == 0) {
                                    self.conversationWrapper().removeClass('has-new');
                                    poll();
                                    return;
                                }

                                // Update the counter
                                self.conversationWrapper().addClass('has-new');
                                self.conversationCounter().html(count);
                                poll();
                            });

                    }, self.options.notifications.interval);
                })();
            },

            '{loginLink} click' : function() {
                self.messageDropDown().hide();
                self.notificationDropDown().hide();

                self.loginDropDown().toggle();
            },

            '{profileLink} tap' : function() {
                self.messageDropDown().hide();
                self.notificationDropDown().hide();

                self.profileDropDown().toggle();
            }
        }
    });

    return App;
});
