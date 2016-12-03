ed.define('easydiscuss', ['edjquery'], function($) {

	var EasyDiscuss = {

	  	ajax: function(namespace, params, callback) {

			var self = this;
		    var options = {
				url: this.getUrl(),
				data: $.extend(params, {
						option: 'com_easydiscuss',
						namespace: namespace
				})
			};

		    // Set the token in the request
		    options.data[this.getToken()] = 1;

		    // This is for server-side function arguments
		    if (options.data.hasOwnProperty('args')) {
		        options.data.args = $.toJSON(options.data.args);
		    }

		    if ($.isPlainObject(callback)) {

		        if (callback.type) {

		            switch (callback.type) {

		                case 'jsonp':

		                    callback.dataType = 'jsonp';

		                    // This ensure jQuery doesn't use XHR should it detect the ajax url is a local domain.
		                    callback.crossDomain = true;

		                    options.data.transport = 'jsonp';
		                    break;

		                case 'iframe':

		                    // For use with iframe-transport
		                    callback.iframe = true;

		                    callback.processData = false;

		                    callback.files = options.data.files;

		                    delete options.data.files;

		                    options.data.transport = 'iframe';
		                    break;
		            }

		            delete callback.type;
		        }

		        $.extend(options, callback);
		    }

		    if ($.isFunction(callback)) {
		        options.success = callback;
		    }

		    var ajax = $.server(options);

		    ajax.progress(function(message, type, code) {
		    });

		    return ajax;
		},

		getUrl: function() {
			return $('[data-ed-ajax-url]').val();
		},

		getToken: function() {
			return $('[data-ed-token]').val();
		}
	};

	// Expose jquery to our own object
	EasyDiscuss.$ = $;

	// Expose this to the window so we can play around with it with firebug
	window.ED = window.EasyDiscuss = EasyDiscuss;

	return EasyDiscuss;
});