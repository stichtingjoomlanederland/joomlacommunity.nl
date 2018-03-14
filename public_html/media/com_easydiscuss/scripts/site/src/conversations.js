ed.define('site/src/conversations', ['edq', 'easydiscuss', 'markitup', 'jquery.expanding', 'jquery.scrollto'], function($) {

    $(document).ready(function() {

        // Conversations content wrapper
        var contentsWrapper = $('[data-ed-conversation-contents-wrapper]'); 
        var messageContent = $('[data-ed-conversation-contents]');

        // Conversations reply form
        var replyForm = $('[data-ed-conversation-reply-form]');
        var replyFormNotice = $('[data-ed-conversation-reply-form-notice]');
        var replyButton = $('[data-ed-conversation-reply-button]');
        var textarea = $('[data-ed-conversation-reply-textarea]');

        // Conversations list
        var listWrapper = $('[data-ed-conversations-list]');
        var lists = $('[data-ed-conversations-list-items]');
        var listItem = lists.find('[data-ed-conversations-item]');
        var conversationTitle = $('[data-ed-conversation-title]');

        // Go to the latest message
        var goToLatest = function() {
            var scroller = $('[data-ed-conversation-contents-scroller]');
            var latest = $('[data-ed-conversation-latest]');

            // Scroll to the latest
            scroller.scrollTo(latest);
        };

        var resetForm = function() {
            textarea.val('');
        };

        // Set an active conversation item
        var setActiveConversation = function(item) {
            
            var lists = $('[data-ed-conversations-list-items]');
            var listItem = lists.find('[data-ed-conversations-item]');

            // Update the conversation area
            listItem.removeClass('is-active');

            // Addd active class on the element
            item.addClass('is-active');

            // Go to the latest content
            goToLatest();
        };

        var showArchiveDialog = function(id) {
            // Display a dialog for confirmation
            EasyDiscuss.dialog({
                content: EasyDiscuss.ajax('site/views/conversation/confirmArchive', {
                    "id": id
                })
            });
        };

        var showDeleteDialog = function(id) {
            // Display a dialog for confirmation
            EasyDiscuss.dialog({
                content: EasyDiscuss.ajax('site/views/conversation/confirmDelete', {
                    "id": id
                })
            });
        };

        var setUnread = function(id) {

            EasyDiscuss.ajax('site/views/conversation/unread', {
                "id": id
            }).done(function() {

            });
        };


        var conversationMenu = $('[data-ed-conversation-menu]');

        conversationMenu.live('click', function(event) {
            event.stopPropagation();
            event.preventDefault();

            var parent = $(this).parents(listItem.selector);
            var id = parent.data('id');
            var type = $(this).data('type');
            var el = $(this);

            if (type == 'archive') {
                showArchiveDialog(id);
            }

            if (type == 'delete') {
                showDeleteDialog(id);
            }

            if (type == 'unread') {
                setUnread(id);
                parent.removeClass('is-active');
                parent.addClass('is-unread');

                parent.find('.open').removeClass('open');
            }
        });

        // When page loads, go to the latest
        goToLatest();

        // Prevent bubbling up for dropdown
        listItem.find('[data-ed-toggle=dropdown]')
            .live('click', function(event) {
                event.stopPropagation();
                event.preventDefault();

            });

        // Apply bbcode on the textarea
        textarea.markItUp({
            markupSet: EasyDiscuss.bbcode
        });

    
        // Apply textarea expanding
        textarea.expandingTextarea();

        // When a conversation item is clicked, fetch contents
        listItem.live('click', function(event) {

            // Prevent bubbling up
            event.stopPropagation();
            event.preventDefault();

            // Find the anchor link so we can route it
            var item = $(this);
            var anchor = item.find('[data-link]');
            var id = $(this).data('id');

            anchor.route();

            // We need to update the reply form id.
            replyForm.data('id', id);

            // Add loader and clear up contents
            contentsWrapper.addClass('is-loading');
            messageContent.html('');

            EasyDiscuss.ajax('site/views/conversation/getConversation', {
                "id": id
            })
            .done(function(title, messages) {

                // Update the title                
                conversationTitle.html(title);

                // Update the contents
                messageContent.html(messages);

                // Set active conversation
                setActiveConversation(item);
            })
            .fail(function(error) {
                messageContent.html(error);
            })
            .always(function(){
                contentsWrapper.removeClass('is-loading');
            });

        });

        // Submit reply button
        replyButton.live('click', function(event) {

            // Prevent the form from submitting
            event.preventDefault();

            var id = replyForm.data('id');
            var button = $(this);
            var textarea = replyForm.find('[data-ed-conversation-reply-textarea]');
            var message = textarea.val();

            EasyDiscuss.ajax('site/controllers/conversation/reply', {
                "id": id,
                "message": message
            }).done(function(contents, message) {

                replyFormNotice
                    .addClass('text-success')
                    .html(message);

                var item = $(contents);

                // Append the message
                messageContent.append(item);

                // Reset the form
                resetForm();

                // Go to the latest content
                goToLatest();
            })
            .fail(function(message){

                replyFormNotice
                    .addClass('text-danger')
                    .html(message);
            });

        });
    

        // Conversation archiving
        var archiveButton = $('[data-ed-conversation-archive]');

        archiveButton.live('click', function() {

            var conversationId = replyForm.data('id');

            // Display the archive dialog
            showArchiveDialog(conversationId);
        });

        // Conversation unread
        var unreadButton = $('[data-ed-conversation-unread]');

        unreadButton.live('click', function() {
            var conversationId = replyForm.data('id');

            // Set it to unread
            setUnread(conversationId);
        });

        // Conversation archiving
        var deleteButton = $('[data-ed-conversation-delete]');

        deleteButton.live('click', function() {

            var conversationId = replyForm.data('id');

            showDeleteDialog(conversationId);
        });

        // Toggle sidebar for mobile view
        var toggleSidebar = $('[data-ed-conversations-toggle]');
        var sidebar = $('[data-ed-conversations-sidebar]');

        // toggleSidebar.on('click', function(event) {
        //     $('[data-ed-conversations-sidebar]').toggle();
        // });

        toggleSidebar.on('click', function(event) {
            if($(sidebar).hasClass("is-open")) {
                $(sidebar).removeClass("is-open");
            } else {
                $(sidebar).removeClass("is-open");
                $(sidebar).addClass("is-open");
            }
        });
        
        // Tabs
        var tabs = $('[data-ed-conversations-tab]');

        tabs.on('click', function(event) {

            event.stopPropagation();
            event.preventDefault();

            // Find the correct link
            var type = $(this).data('type');
            var tab = $(this);
            var anchor = tab.find('> a');

            anchor.route();

                
            // Add active class
            tabs.removeClass('active');
            tab.addClass('active');

            // Set the loading classes on all wrappers
            listWrapper.addClass('is-loading');
            contentsWrapper.addClass('is-loading');


            EasyDiscuss.ajax('site/views/conversation/getConversations', {
                "type": type
            }).done(function(conversations, activeConversation) {

                // Remove loading classes
                listWrapper.removeClass('is-loading');
                listWrapper.removeClass('is-empty');
                        
                contentsWrapper.removeClass('is-loading');
                contentsWrapper.removeClass('is-empty');
                    
                // Remove has-active by default
                $('[data-ed-content-wrapper]').removeClass('has-active');      

                if (conversations.length < 1) {
                    listWrapper.addClass('is-empty');
                    contentsWrapper.addClass('is-empty');
                }

                if (conversations.length > 1) {
                    $('[data-ed-content-wrapper]').addClass('has-active');
                }
                
                // Append the lists
                lists.html(conversations);


                // Find the first list item
                var firstItem = $(lists.selector).children(':first');

                // Click on the first item
                firstItem.click();
            });


        });

    });

});
