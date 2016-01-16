Komento.module('shortenurl', function($) {
	var module = this;

	$.shortenurl = function(url, type, options, callback) {
		var node = $.shortenurl[type](url, type, options, callback);
	}

	$.fn.shortenurl = function(type, options, callback) {
		var node = this;
	}

	$.shortenurl.google = function(url, type, options, callback) {
		$.ajax({
			type: 'POST',
			contentType: 'application/json',
			url: 'https://www.googleapis.com/urlshortener/v1/url',
			data: {
				'longUrl': url
			},
			success: function(data) {
			}
		})
	};

	module.resolve();
});
