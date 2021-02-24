
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

(function ($) {

    if (typeof this.Docman === 'undefined') {
        this.Docman = {};
    }

    this.Docman.Chunker = Koowa.Class.extend({
        completed: 0,
        getOptions: function() {
            var self = this;

            return $.extend(this.supr(), {
                event_container: $('<div />'), // Events are fired on this element
                init_offset: 0,
                offset: 30,
                url: '',
                timeout: 30000,
                request: {
                    type: 'post',
                    data: {}
                },
                callbacks: {
                    success: function (response) {
                        var data = response.result;

                        if (!data) {
                            self.trigger('processFailed', $.extend({}, response));

                            return;
                        }

                        // Update progress bar.
                        self.update(data);

                        if (data.remaining) {
                            self.request(data.offset);
                        } else {
                            self.trigger('processComplete', $.extend({}, data));
                        }
                    },
                    error: function (data, textStatus) {
                        if (textStatus == 'timeout') {
                            this.tryCount++;
                            var request = this;
                            if (this.tryCount <= this.retryLimit) {
                                //try again
                                setTimeout(function() {
                                    $.ajax(request);
                                }, 30000);
                            } else {
                                self.trigger('processFailed', {error: 'Request timed out'});
                            }

                            return;
                        }

                        var response;

                        try {
                            response = $.parseJSON(data.responseText);
                        } catch (error) {
                            response = {};
                        }

                        self.trigger('processFailed', $.extend({}, response));
                    }
                }
            });
        },
        initialize: function(options){
            this.supr();
            this.setOptions(options);
        },
        update: function (data) {
            // Update total completed amount.
            this.completed += parseInt(data.completed, 10);
            var percentage = 100;
            if (data.remaining) {
                percentage = parseInt(this.completed * 100 / (this.completed + parseInt(data.remaining, 10)), 10);
            }

            this.trigger('processUpdate', $.extend({percentage: percentage}, data));
        },

        start: function () {
            this.request(this.options.init_offset);
        },

        request: function (offset) {
            this.options.request.data.offset = offset;

            return $.ajax(this.options.url, {
                type: this.options.request.type,
                data: this.options.request.data,
                timeout: this.options.timeout,
                success: this.options.callbacks.success,
                error: this.options.callbacks.error,
                tryCount : 0,
                retryLimit : 0
            });
        },

        bind: function (event, callback) {
            this.options.event_container.on(event, callback);

            return this;
        },
        trigger: function (event, data) {
            this.options.event_container.trigger(event, data);

            return this;
        }
    });

})(kQuery);


(function($) {

    if (typeof this.Docman === 'undefined') {
        this.Docman = {};
    }

    var import_in_process = false;

    window.onbeforeunload = function(e) {
        if (import_in_process) {
            return 'Navigating away from this page may result in a broken site. Are you sure you want to continue?';
        }
    };

    $(function() {
        var showStep = function(step) {
                $('.migrator__wrapper').hide();
                $('.migrator--step'+step).show();

                $('.migrator__steps__list__item').removeClass('item--active');
                $('.migrator__steps__list__item:nth-child('+step+')').addClass('item--active');
            },
            showError = function(response)
            {
                var message = "There's been an error while executing the job";

                if (response.error) {
                    message = response.error;
                }
                else if (response.errors)  {
                    message = response.errors[0].message;
                }

                $('.migrator_alert').fadeOut('fast', function() {
                    $(this).html(message).fadeIn('fast');
                });
                $('.bar').removeClass('bar-success').addClass('bar-danger')
                    .parent().removeClass('active');
            },
            ajax = function(url, config)
            {
                if (!config.method) {
                    config.method = 'POST';
                }

                if (!config.data) {
                    config.data = {};
                }

                if (!config.timeout) {
                    config.timeout = 30000;
                }

                config.data.csrf_token = Docman.token;

                var error = function(data) {
                    var response;

                    try {
                        response = $.parseJSON(data.responseText);
                    } catch (error) {
                        response = {};
                    }

                    showError(response);
                };

                return $.ajax(url, config).fail(error);
            },
            processQueue = function(queue)
            {
                if (queue.length)
                {
                    var job = queue.shift();

                    var config = Docman.jobs[job];

                    var container = $('<div class="migrator__content" style="display: none;"></div>').appendTo($('.migrator--step1'))
                        .append('<h3>' + config.label + '</h3>');

                    var progress_bar = $('<div class="bar" style="width: 0%"></div>');

                    container.show();

                    // Automatically scroll down.
                    $('html,body').animate({scrollTop: container.offset().top});

                    $('<div class="progress progress-striped active"></div>').appendTo(container)
                        .append(progress_bar);

                    var url = Docman.base_url+'&job='+job;

                    if (config.chunkable)
                    {
                        new Docman.Chunker({
                            url: url,
                            request: {
                                type: 'post',
                                data: {
                                    _action: config.action || job,
                                    csrf_token: Docman.token
                                }
                            }
                        }).bind('processUpdate', function(e, data) {
                            progress_bar.css('width', data.percentage + '%');
                        }).bind('processFailed', function(e, data) {
                            showError(data);
                        }).bind('processComplete', function(e, data) {
                            progress_bar.css('width', '100%').addClass('bar-success')
                                .parent().removeClass('active');

                            processQueue(queue);
                        }).start();
                    }
                    else
                    {
                        progress_bar.css('width', '50%');

                        ajax(url, {
                            data: {
                                _action: config.action || job
                            }
                        }).done(function() {
                            progress_bar.css('width', '100%').addClass('bar-success')
                                .parent().removeClass('active');

                            processQueue(queue);
                        });
                    }
                }
                else
                {
                    // If queue is empty, proceed to download.
                    setTimeout(function () {
                        showStep(2);
                    }, 1000);
                }
            };

        var queue = [];

        $.map(Docman.jobs, function(value, index) {
            queue.push(index);
        });
        processQueue(queue);

    });

})(kQuery);