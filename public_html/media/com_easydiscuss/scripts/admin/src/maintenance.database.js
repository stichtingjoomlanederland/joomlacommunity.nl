ed.define('admin/src/maintenance.database', ['edq', 'easydiscuss', 'abstract'], function($, EasyDiscuss, Abstract){

    return new Abstract(function(self){
        return {
            opts: {
                '{container}': '[data-maintenance-database]',

                '{start}': '[data-start]',

                '{progress}': '[data-progress]',

                '{progressBox}': '[data-progress-box]',

                '{progressBar}': '[data-progress-bar]',

                '{progressPercentage}': '[data-progress-percentage]',

                '{errorBox}': '[data-error]',

                '{successBox}': '[data-success]'

            },

            init: function() {

            },

            counter: 0,

            versions: [],

            '{start} click': function(el) {

                el.hide();
                self.progress().show();

                self.process();
            },

            process: function() {
                self.getStats().done(function(versions) {
                    self.versions = versions;

                    self.execute();
                });
            },

            getStats: function() {
                return EasyDiscuss.ajax('admin/controllers/maintenance/getDatabaseStats');
            },

            execute: function() {

                if (self.versions[self.counter] === undefined) {
                    return self.completed();
                }

                self.successBox().hide();

                EasyDiscuss.ajax('admin/controllers/maintenance/synchronizeDatabase', {
                    version: self.versions[self.counter]
                })
                .fail(function(msg) {

                    var errorBox = self.errorBox();

                    errorBox.show();

                    errorBox.append('<div class="alert alert-danger">' + msg + '</div>');

                }).always(function() {
                    self.counter++;

                    var percentage = Math.floor((self.counter/self.versions.length) * 100) + '%';

                    self.progressBar().css('width', percentage);

                    self.progressPercentage().text(percentage);

                    self.execute();
                });
            },

            completed: function() {

                self.progressBar().css('width', '100%');
                self.progressPercentage().text('100%');

                self.progressBox()
                    .removeClass('progress-info')
                    .addClass('progress-success');

                self.successBox().delay(1000).fadeIn(400);
            }

        }
    });
});


// EasyBlog.module('admin/maintenance/database', function($) {
//     var module = this;

//     EasyBlog.Controller('Maintenance.Database', {
//         defaultOptions: {
//             '{start}': '[data-start]',

//             '{progress}': '[data-progress]',

//             '{progressBox}': '[data-progress-box]',

//             '{progressBar}': '[data-progress-bar]',

//             '{progressPercentage}': '[data-progress-percentage]',

//             '{errorBox}': '[data-error]',

//             '{successBox}': '[data-success]'
//         }
//     }, function(self) {
//         return {
//             init: function() {

//             },

//             '{start} click': function(el) {
//                 el.hide();

//                 self.progress().show();

//                 self.process();
//             },

//             counter: 0,

//             versions: [],

//             process: function() {
//                 self.getStats().done(function(versions) {
//                     self.versions = versions;

//                     self.execute();
//                 });
//             },

//             getStats: function() {
//                 return EasyBlog.ajax('admin/controllers/maintenance/getDatabaseStats');
//             },

//             execute: function() {
//                 if (self.versions[self.counter] === undefined) {
//                     return self.completed();
//                 }

//                 self.successBox().hide();

//                 EasyBlog.ajax('admin/controllers/maintenance/synchronizeDatabase', {
//                     version: self.versions[self.counter]
//                 }).fail(function(msg) {
//                     self.errorBox().show();

//                     self.errorBox().append('<div class="alert alert-danger">' + msg + '</div>');

//                 }).always(function() {
//                     self.counter++;

//                     var percentage = Math.floor((self.counter/self.versions.length) * 100) + '%';

//                     self.progressBar().css('width', percentage);

//                     self.progressPercentage().text(percentage);

//                     self.execute();
//                 });
//             },

//             completed: function() {
//                 self.progressBar().css('width', '100%');

//                 self.progressPercentage().text('100%');

//                 self.progressBox()
//                     .removeClass('progress-info')
//                     .addClass('progress-success');

//                 self.successBox().delay(1000).fadeIn(400);
//             }
//         }
//     });

//     module.resolve();
// });
