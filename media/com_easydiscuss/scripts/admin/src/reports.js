ed.define('admin/src/reports', ['edq', 'easydiscuss', 'abstract'], function($, EasyDiscuss, Abstract){

    return new Abstract(function(self){
        return {
            opts: {
                "{item}": "[data-ed-report-item]",
                "{button}": "[data-ed-report-button]",
                "{message}": "[data-ed-report-msg]",
                "{textarea}": "[data-ed-report-textarea]",
                "{email}": "[data-ed-report-email]",
                "{actiontype}": "[data-action-type]",

                '{selectAction}' : '[data-action-type]',
            },

            init: function() {
            },

            "{button} click": function(el) {

                var parent = el.parent(self.item());
                var message = parent.find(self.message());
                var textarea = parent.find(self.textarea());
                var actiontype = parent.find(self.actiontype());
                var email = parent.find(self.email());

                var id = el.data('id');

                switch (actiontype.val()) {

                    case "E" :
                        // if(EasyDiscuss.$('#email-text-' + id).val().length <= 0)
                        // {
                        //     alert( '<?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_ENTER_CONTENTS' );?>' );
                        //     return false;
                        // }

                        // var inputs  = [];

                        // //post_id
                        // inputs.push( 'post_id=' + escape( id ) );

                        // //content
                        // val = $('#email-text-' + id).val().replace(/"/g, "&quot;");
                        // val = encodeURIComponent(val);
                        // inputs.push( 'content=' + escape( val ) );

                        // disjax.load('Reports', 'ajaxSubmitEmail', inputs);

                        EasyDiscuss.ajax('admin/views/reports/submitEmail', {
                            "id": id,
                            "content": textarea.val()
                        })
                        .done(function(msg) {
                            message.text(msg);

                            // reset the action dropdown selection.
                            actiontype.children(':first').prop('selected' , true);
                            email.hide();

                        })
                        .fail(function(msg) {
                            console.log('failed: ' + msg);
                        });

                        break;

                    case "D" :

                        // if( confirm( '<?php echo $this->escape( JText::_( 'COM_EASYDISCUSS_CONFIRM_DELETE_POST') );?>' ) ) {
                        //     self.submitReportForm(id, '', 'deletePost');
                        // }

                        // EasyDiscuss.ajax('admin/views/reports/ajaxDeleteConfirm', {
                        //     "id": id
                        // })
                        // .done(function(html) {

                        // });

                        EasyDiscuss.dialog({
                            "content": EasyDiscuss.ajax('admin/views/reports/deleteConfirm', {'id': id}),
                            "bindings": {
                                "{cancelButton} click": function() {
                                    EasyDiscuss.dialog.close();
                                },

                                "{closeButton} click": function() {
                                    EasyDiscuss.dialog.close();
                                },
                            }
                        });

                        break;

                    case "C" :
                        this.submitReportForm(id, '', 'removeReports');
                        break;

                    case "P" :
                        self.submitReportForm(id, '1', 'togglePublish');
                        break;

                    case "U" :
                        self.submitReportForm(id, '0', 'togglePublish');
                        break;

                    default:
                        break;
                }



            },


            submitReportForm: function(id, val, task) {
                EasyDiscuss.$('#post_id').val(id);
                EasyDiscuss.$('#post_val').val(val);
                EasyDiscuss.$('[data-ed-form-task]').val(task);
                EasyDiscuss.$('#adminForm').submit();
            },

            '{selectAction} change': function(el) {
                var id = $(el).data('id');
                var type = $(el).val();

                // var container = $(self.element).has('[data-id=' + id + ']');
                // var emailDiv = container.find('[data-ed-report-email]');
                // var textarea = container.find('[data-ed-report-textarea]');

                var parent = el.parent(self.item());
                var textarea = parent.find(self.textarea());
                var email = parent.find(self.email());

                //clear textarea text
                textarea.val('');

                if (type == 'E') {
                    email.show();
                } else {
                    email.hide();
                }
            }

        }
    });
});
