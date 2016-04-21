ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var migrateButton = $('[data-ed-migrate]');

	migrateButton.on('click', function() {

		// Hide the button
		//migrateButton.hide();

		// Update the buttons message
		migrateButton.html('<i class="fa fa-cog fa-spin"></i> <?php echo JText::_('COM_EASYDISCUSS_MIGRATING', true);?>');

		// Hide the no progress message
		$('[data-progress-empty]').addClass('hide');

		// Ensure that the progress is always reset to empty just in case the user runs it twice.
		$('[data-progress-status]').html('');

		// clear the stats.
		$('[data-progress-stat]').html('');

		//show the loading icon
		$('[data-progress-loading]').removeClass('hide');

		//process the migration
		window.migrateArticle();
		
	});

	window.migrateArticle = function() {

		EasyDiscuss.ajax('admin/views/migrators/migrate', {
			"component": "com_community"
		}).done(function(result, status) {

			// Append the current status
			$('[data-progress-status]').append(status);

			// If there's still items to render, run a recursive loop until it doesn't have any more items;
			if (result == true) {
				window.migrateArticle();
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			migrateButton.removeAttr('disabled');
			migrateButton.html('<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYDISCUSS_COMPLETED', true);?>');
			$('[data-progress-status]').append('<?php echo JText::_('COM_EASYDISCUSS_COMPLETED', true);?>');

			if (result == 'noitem'){
				migrateButton.removeAttr('disabled');
				migrateButton.html('<?php echo JText::_('COM_EASYDISCUSS_MIGRATORS_RUN_MIGRATION_TOOL', true);?>');
				$('[data-progress-status]').html('<?php echo JText::_('COM_EASYDISCUSS_NO_ITEM', true);?>');
			}
		});

		
	}
});