EasyDiscuss.module('layout/lightbox', function($) {

	var module = this;

	EasyDiscuss.require()
		.library('fancybox')
		.stylesheet('fancybox/default')
		.script('legacy')
		.done(function(){

			discuss.attachments.initGallery({
				type: 'image',
				helpers: {
					overlay: null
				}
			});

			module.resolve();
		});
});