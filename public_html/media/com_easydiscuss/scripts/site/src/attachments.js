ed.define('site/src/attachments', ['edq', 'abstract'], function($, Abstract) {

    var options = {
        limit: 0,
        editable: false,
        types: {
            'image': ["jpg","png","gif"],
            'archive': ["zip","rar","gz","gzip"],
            'pdf': ["pdf"]            
        }
    };

    // Clone the form once
    var clonedForm = $('[data-ed-attachment-form]').clone();

    var fileInput = $('[data-ed-attachment-item-input]');

    fileInput.on('change', function() {

        // Get the form 
        var form = el.parents(self.opts["{form}"]);

        // Insert a new item on the result
        insertAttachment(form);
    });

    var getAttachmentType = function(filename) {

        var extension = filename.substr((filename.lastIndexOf('.') + 1));
        var type = 'default';

        // Image type
        if ($.inArray(extension, self.opts.types.image) != -1) {
            type = 'image';
        }

        // Archive type
        if ($.inArray(extension, self.opts.types.archive) != -1) {
            type = 'archive';
        }

        // Archive type
        if ($.inArray(extension, self.opts.types.pdf) != -1) {
            type = 'pdf';
        }

        return type;
    };

    var insertAttachment = function(form) {

        var fileInput = form.find("input:not(:hidden)");

        // Get the file attributes
        var file = {
            title: fileInput.val(),
            type: getAttachmentType(fileInput.val())
        };

        // Chrome fix
        if (file.title.match(/fakepath/)) {
            file.title = file.title.replace(/C:\\fakepath\\/i, '');
        }

        // Set the file title
        var title = form.children(self.opts["{title}"]);
        title.html(file.title);

        // Add the attachment type class
        form
            .removeClass('ed-attachment-form')
            .addClass('attachment-type-' + file.type)

        // Add it into the list
        var list = $('[data-ed-attachments-list]');
        
        form.appendTo(list);

        // Once it is added, we want to attach a new form to the list
        resetAttachmentForm();
    };

    var resetAttachmentForm = function() {
        var form = clonedForm.clone();

        // Re-append a new form
        self.element.append(form);
    };


        return {
            opts: {

                clonedForm: null,

                "{list}": "[data-ed-attachments-list]",

                "{form}": "[data-ed-attachment-form]",

                "{title}": "[data-ed-attachment-item-title]",
                "{input}": "[data-attachment-item-input]",
                "{removeItem}": "[data-ed-attachment-item-remove]"
            },

            init: function() {

                // We need to clone the file input so that we can use that later on.
                self.clonedForm = self.form().clone();
            }, 

            // Returns a list of known extensions
            getType: function(filename) {

            },



            remove: function(item) {

                // We should also check if this is being edited and removed as we should alert the user.

                // Remove the item
                item.remove();
            },

            reset: function() {

            },

            "{input} change": function(el, event) {

            },

            "{removeItem} click": function(el, event) {
                var item = el.parents('.attachment-item');

                self.remove(item);
            }
        };
    // });

    // return Attachments;
});