Komento.module('sharelinks', function($) {
	var module = this;

	$.sharelinks = function(elem, type, options, callback) {
		var node = this[type].call($(elem), type, options, callback);
	}

	$.fn.sharelinks = function(type, options, callback) {
		/*
		options{
			url
			content
			image
		}

		type {
			facebook
			twitter
			googleplus
			linkedin
			pinterest
			tumblr
			digg
			delicious
			reddit
			stumbleupon
			identica
			stumpedia
			technorati
			blogmarks
		}
		*/

		var node = this;

		if(!node.attr('loaded')) {
			node.attr('loaded', 1);

			if(!type) {
				var type = node.attr('type');
			}

			if(!options) {
				var url, title, content;

				if(node.attr('url')) {
					url = encodeURIComponent(node.attr('url'));
				}
				if(node.attr('title')) {
					title = encodeURIComponent(node.attr('title'));
				}
				if(node.attr('content')) {
					content = encodeURIComponent(node.attr('content'));
				}

				var options = {
					'url': url,
					'title': title,
					'content': content
				}
			}

			$(document).ready(function() {
				var callback = function(node, link, type) {
					$.sharelinks['cleanup'].call(node);

					node.bind('click', function(event) {
						$.sharelinks['popup'].call(node, type, link);
					});
				}

				$.sharelinks[type].call(node, type, options, callback);
			});
		}
	};

	$.sharelinks.facebook = function(type, options, callback) {
		// p[url]
		// p[title]
		// p[summary]
		// p[images]

		var node = this,
			link = 'http://www.facebook.com/sharer.php?s=100',
			url,
			title,
			summary,
			images;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;

			if(options.content) {
				summary = options.content;
			}
		} else {
			if(options.content) {
				title = 'Comments';
				summary = options.content;
			}
		}

		if(options.image) {
			images = options.image;
		}

		link += '&p[url]=' + url + '&p[title]=' + title + '&p[summary]=' + summary + '&p[images]=' + images;

		callback(node, link, type);
	};

	$.sharelinks.twitter = function(type, options, callback) {
		// url
		// text

		var node = this,
			link = 'http://twitter.com/intent/tweet',
			url,
			text;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			text = options.title;
		} else {
			if(options.content) {
				text = options.content;
			}
		}

		link += '?url=' + url + '&text=' + text;

		callback(node, link, type);
	};

	$.sharelinks.googleplus = function(type, options, callback) {
		// url

		var node = this,
			link = 'http://plus.google.com/share',
			url;

		if(options.url) {
			url = options.url;
		}

		link += '?url=' + url;

		callback(node, link, type);
	};

	$.sharelinks.linkedin = function(type, options, callback) {
		// url
		// title
		// summary

		var node = this,
			link = 'http://linkedin.com/shareArticle?mini=true',
			url,
			title,
			summary;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;

			if(options.content) {
				summary = options.content;
			}
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '&url=' + url + '&title=' + title + '&summary=' + summary;

		callback(node, link, type);
	};

	$.sharelinks.pinterest = function(type, options, callback) {
		// url
		// description
		// media (mandatory)

		var node = this,
			link = 'http://pinterest.com/pin/create/button/',
			url,
			description,
			media = options.image;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			description = options.title;
		} else {
			if(options.content) {
				description = options.content;
			}
		}

		link += '?media=' + media + '&url=' + url + '&description=' + description;

		callback(node, link, type);
	};

	$.sharelinks.tumblr = function(type, options, callback) {
		// url
		// name
		// description

		var node = this,
			link = 'http://www.tumblr.com/share/link',
			url,
			name,
			description;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			name = options.title;

			if(options.content) {
				description = options.content;
			}
		} else {
			if(options.content) {
				name = options.content;
			}
		}

		link += '?url=' + url + '&name=' + name + '&description=' + description;

		callback(node, link, type);
	};

	$.sharelinks.digg = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://digg.com/submit',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.delicious = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://delicious.com/post',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.reddit = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://reddit.com/submit',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.stumbleupon = function(type, options, callback) {
		// url
		// title
		var node = this,
			link = 'http://www.stumbleupon.com/submit',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '?url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.indentica = function(type, options, callback) {
		// url
		// title

		var node = this,
			link = 'http://identi.ca/index.php?action=bookmarkpopup',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '&url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.stumpedia = function(type, options, callback) {
		// url

		var node = this,
			link = 'http://www.stumpedia.com/submit',
			url;

		if(options.url) {
			url = options.url;
		}

		link += '?url=' + url;

		callback(node, link, type);
	};

	$.sharelinks.technorati = function(type, options, callback) {
		// add (url)

		var node = this,
			link = 'http://technorati.com/faves';
			add;

		if(options.url) {
			add = options.url;
		}

		link += '?add=' + url;

		callback(node, link, type);
	};

	$.sharelinks.blogmarks = function(type, options, callback) {
		// url
		// title

		var node = this,
			link = 'http://blogmarks.net/my/new.php?mini=1',
			url,
			title;

		if(options.url) {
			url = options.url;
		}

		if(options.title) {
			title = options.title;
		} else {
			if(options.content) {
				title = options.content;
			}
		}

		link += '&url=' + url + '&title=' + title;

		callback(node, link, type);
	};

	$.sharelinks.dialog = function(url) {
		Komento.require().library('dialog').done(function() {
			$.dialog(url);
		});
	};

	$.sharelinks.cleanup = function() {
		var node = this;
		node.removeAttr('url')
			.removeAttr('type')
			.removeAttr('title')
			.removeAttr('content')
			.removeAttr('image')
			.removeAttr('commentid')
			.attr('loaded', 1);
	};

	$.sharelinks.popup = function(type, url) {
		var optionString = 'menubar=0,resizable=0,scrollbars=0,';

		/*switch(type) {
			case 'facebook':
				optionString += 'width=640,height=320';
				break;
			case 'twitter':
				optionString += 'width=640,height=320';
				break;
			case 'googleplus':
				optionString += 'width=640,height=320';
				break;
			case 'linkedin':
				optionString += 'width=640,height=320';
				break;
		}*/

		optionString += 'width=660,height=320';

		window.open(url, '', optionString);
	}

	module.resolve();
});
