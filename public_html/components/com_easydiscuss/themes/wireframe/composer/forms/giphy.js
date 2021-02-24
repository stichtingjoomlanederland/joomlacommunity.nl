ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
	var gifsTab = $('[data-giphy-gifs-tab]');
	var stickersTab = $('[data-giphy-stickers-tab]');
	var gifsList = $('[data-gifs-list]');
	var stickersList = $('[data-stickers-list]');
	var container = $('[data-giphy-container]');
	var searchInput = $('[data-giphy-search]');
	var giphyItem = $('[data-giphy-item]');
	var editor = $('[data-ed-editor]');
	var browser = $('[data-giphy-browser]');
	var giphyButton = $('.markitup-giphy');

	// The first view will always be GIFs
	var currentView = 'gifs';

	<?php if (isset($editorId) && $editorId) { ?>
		editor = $('[<?php echo $editorId; ?>]');

		gifsTab = editor.find('[data-giphy-gifs-tab]');
		stickersTab = editor.find('[data-giphy-stickers-tab]');
		container = editor.find('[data-giphy-container]');
		searchInput = editor.find('[data-giphy-search]');
		giphyItem = editor.find('[data-giphy-item]');
		browser = editor.find('[data-giphy-browser]');
		giphyButton = editor.find('.markitup-giphy');
		gifsList = editor.find('[data-gifs-list]');
		stickersList = editor.find('[data-stickers-list]');
	<?php } ?>

	var currentQuery= [];
	currentQuery['gifs'] = '';
	currentQuery['stickers'] = '';

	var isLoaded = [];
	isLoaded['gifs'] = false;
	isLoaded['stickers'] = false;

	var show = function(el, type) {
		var list = editor.find('[data-' + type + '-list]');

		el.addClass('active');
		list.removeClass('t-d--none');
	};

	var hide = function(type) {
		var tab = editor.find('[data-giphy-' + type + '-tab]');
		var list = editor.find('[data-' + type + '-list]');

		tab.removeClass('active');
		list.addClass('t-d--none');
	};

	var loadGiphy = function(query, type, list) {
		EasyDiscuss.ajax('site/views/giphy/search', {
			"query": query,
			"type": type
		}).done(function(hasGiphies, html) {
			// Remove the loader
			container.removeClass('is-loading');

			if (hasGiphies) {
				// Make sure that the current view is match to the type
				// Then only show
				if (currentView == type) {
					list.removeClass('t-d--none');
				}

				list.html(html);
			} else {
				// Show the empty result message if there is no result
				container.addClass('is-empty');
			}
		});
	};

	gifsTab.on('click', function() {
		currentView = 'gifs';

		show($(this), 'gifs');

		// Show back the query that the user left before changing to another tab
		searchInput.val(currentQuery['gifs']);

		hide('stickers');
	});

	stickersTab.on('click', function() {
		currentView = 'stickers';

		// Initialize the stickers once if haven't load
		if (!isLoaded['stickers']) {
			isLoaded['stickers'] = true;

			// Show the loader
			container.addClass('is-loading');

			loadGiphy('', 'stickers', stickersList);
		}

		show($(this), 'stickers');

		// Show back the query that the user left before changing to another tab
		searchInput.val(currentQuery['stickers']);

		hide('gifs');
	});

	$(document).on('keyup', searchInput.selector, $.debounce(function() {
		var query = $(this).val();
		var trendingTitle = browser.find('[data-giphy-trending]');

		// By default the type is gifs
		var type = 'gifs';

		if (stickersTab.hasClass('active')) {
			type = 'stickers';
		}

		// Store the current query so that we can show it back after switching back the tab
		currentQuery[type] = query;

		var giphyList = editor.find('[data-' + type + '-list]'); 

		// Hide the list
		giphyList.addClass('t-d--none');

		// Show the loader
		container.addClass('is-loading');

		// Always remove the empty message first
		container.removeClass('is-empty');

		if (browser.find('[data-giphy-trending]').length == 0) {
			trendingTitle = $('[data-giphy-trending]');
		}

		trendingTitle.removeClass('t-d--none');

		if (query != '') {
			trendingTitle.addClass('t-d--none');
		}

		loadGiphy(query, type, giphyList);
	}, 300));

	$(document).on('click', giphyItem.selector, function(){
		var textarea = editor.find('[data-ed-editor]');
		var original = $(this).data('original');
		var value = textarea.val() + ' [giphy]' + original + '[/giphy]';

		textarea.val(value);
	});

	$(document).on('initializeGiphy', function() {
		// initialize it once
		if (isLoaded['gifs']) {
			return;
		}

		isLoaded['gifs'] = true;

		// Show the loader
		container.addClass('is-loading');

		// Intialize the GIFs only as it will always show first
		loadGiphy('', 'gifs', gifsList);
	});
});