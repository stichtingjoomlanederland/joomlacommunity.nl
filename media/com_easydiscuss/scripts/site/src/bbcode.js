ed.define('site/src/bbcode', ['edq', 'abstract', 'markitup', 'lodash', 'site/vendors/jquery.expanding'], function($, Abstract, markitup, _){

    var BBcode = new Abstract(function(self) {

        return {
            opts: {
                editorType: null,
                operation: null,
                '{editor}': '[name=dc_reply_content]',
                '{tabs}': '.formTabs [data-foundry-toggle=tab]',
                '{form}': 'form[name=dc_submit]',
                '{attachmentItem}': "[data-attachment-item]",
                '{attachments}': 'input.fileInput',
                '{submitButton}': '.submit-reply',
                '{cancelButton}': '.cancel-reply',
                '{notification}': '.replyNotification',
                '{loadingIndicator}': '.reply-loading'
            },

            init: function() {

                // Composer ID
                self.id = self.element.data('id');

                // Composer operation
                self.options.operation = self.element.data('operation');

                // Composer editor
                self.options.editorType = self.element.data('editortype');

                // Render the markitup editor
                if (self.options.editorType == 'bbcode') {
                    self.editor()
                        .markItUp(self.options.bbcodeSettings)
                        .expandingTextarea();
                }
            },

            initMentions: function() {
              self.editor().mentionsInput({
                allowRepeat: true,
                minChars: 1,
                onDataRequest:function (mode, query, callback) {
                  var data = [
                    { id:1, name:'Kenneth Auchenberg', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:2, name:'Jon Froda', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:3, name:'Anders Pollas', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:4, name:'Kasper Hulthin', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:5, name:'Andreas Haugstrup', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:6, name:'Pete Lacey', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:7, name:'kenneth@auchenberg.dk', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:8, name:'Pete Awesome Lacey', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
                    { id:9, name:'Kenneth Hulthin', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' }
                  ];

                  data = _.filter(data, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });

                  callback.call(this, data);
                },
                onCaret: true
              });

              $('.get-syntax-text').click(function() {
                $('textarea.mention').mentionsInput('val', function(text) {
                  alert(text);
                });
              });

              $('.get-mentions').click(function() {
                $('textarea.mention').mentionsInput('getMentions', function(data) {
                  alert(JSON.stringify(data));
                });
              }) ;
            },

            '{submitButton} click': function() {
                self.submit();
            },

            '{cancelButton} click': function() {
                self.trigger('cancel');
            },

            notify: function(type, message) {
                self.notification()
                    .addClass('alert-' + type)
                    .html(message)
                    .show();
            },

            submit: function() {

                var params = self.form().serializeObject();

                // Ambiguity with normal reply form
                params.content = params.dc_reply_content;

                params.files = self.attachmentItem(':not(.new)').find('input[type=file]');
                // params.files = self.attachments();

                EasyDiscuss.ajax('site/views/post/saveReply', params, {
                        type: 'iframe',

                        beforeSend: function() {
                            self.submitButton().prop('disabled', true);
                            self.loadingIndicator().show();
                        },

                        notify: self.notify,

                        reloadCaptcha: function() {
                            typeof Recaptcha !== 'undefined' && Recaptcha.reload();
                        },

                        complete: function() {

                            if (self._destroyed) {
                                return;
                            }

                            self.submitButton().removeAttr('disabled');
                            self.loadingIndicator().hide();
                        }
                    }).done(function(content) {
                        self.trigger('save', content);
                    })
                    .fail(self.notify);
            }
        }
    });

    return BBcode;
});
