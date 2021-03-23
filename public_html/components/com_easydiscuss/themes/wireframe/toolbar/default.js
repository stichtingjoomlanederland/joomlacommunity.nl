ed.require(['edq', 'site/src/toolbar', 'site/src/floatlabels', 'site/vendors/mmenu'], function($, App) {

	var toolbarSelector = '[data-ed-toolbar]';

	// If conversekit is installed, do not redirect the user
	if (window.ck !== undefined) {

		$('[data-ed-external-conversation]').on('click', function(event) {
			event.preventDefault();
		});
	}

	// Close the subscribe popbox
	$(document).on('click.unsubscribe.toolbar', '[data-ed-unsubscribe-toolbar]', function() {
		$('[data-ed-subscriptions]').click();
	});

	// Prevent closing toolbar dropdown
	$(document).on('click.toolbar', '[data-ed-toolbar-dropdown]', function(event) {
		event.stopPropagation();
	});

	// Implement the abstract
	App.execute(toolbarSelector, {
		"notifications": {
			"interval": <?php echo $this->config->get('system_polling_interval') * 1000; ?>,
			"enabled": <?php echo $this->my->id && $this->config->get('main_notifications') ? 'true' : 'false';?>
		},
		"conversations": {
			"interval": <?php echo $this->config->get('main_conversations_notification_interval') * 1000 ?>,
			"enabled": <?php echo $this->my->id && $this->config->get('main_conversations') && $this->config->get('main_conversations_notification') ? 'true' : 'false';?>
		}
	});

	// Logout button
	$('[data-ed-toolbar-logout]').live('click', function() {
		$('[data-ed-toolbar-logout-form]').submit();
	});


	// search
	$('[data-search-input]').live('keydown', function(e) {
		if (e.keyCode == 13) {
			$('[data-search-toolbar-form]').submit();
		}
	});

	// We need to unbind the click for conflicts with pagespeed
	$(document)
		.off('click.search.toggle')
		.on('click.search.toggle', '[data-ed-toolbar-search-toggle]', function() {
			var searchBar = $('[data-toolbar-search]');

			$(toolbarSelector).toggleClass('ed-toolbar--search-on');
	});

	<?php if ($showToolbar && $showNavigationMenu && $this->isMobile() || $this->isTablet()) { ?>
	if ($('#ed-canvas').length > 0) {
		new Mmenu("#ed-canvas", {
			"extensions": [
				"pagedim-black",
				"theme-dark",
				"fullscreen",
				"popup"
			],
			searchfield : {
				panel: true,
				placeholder: '<?php echo JText::_('COM_EASYDISCUSS_SEARCH', true);?>',
				noResults: '<?php echo JText::_('COM_ED_NO_SEARCH_RESULTS', true);?>'
			},
			"navbars": [
				{
					"position": "top",
					"content": [
						"searchfield",
						"close"
					]
				},
				{
					"position": "bottom",
					"content": [
						"<a class='fas fa-rss-square' href='<?php echo ED::feeds()->getFeedUrl('view=index');?>'></a>",
						
						<?php if ($isSubscribed) { ?>
						"<a class='fas fa-at' href='javascript:void(0);' data-ed-unsubscribe data-type='site' data-cid=0 data-sid='<?php echo $isSubscribed->id;?>'></a>"
						<?php } ?>

						<?php if (!$isSubscribed) { ?>
							"<a class='fas fa-at' href='javascript:void(0);' data-ed-subscribe data-type='site' data-cid=0></a>"
						<?php } ?>
					]
				}

			],
			"navbar": {
				"title": "<?php echo JText::_("COM_ED_MENU", true);?>"
			}
		
		});
	}
	<?php } ?>
});
