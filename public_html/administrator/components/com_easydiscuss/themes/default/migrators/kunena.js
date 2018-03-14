ed.require(['edq'], function($) {

	var migrateButton = $('[data-ed-migrate]');
	var migrateReplyButton = $('[data-ed-migrate-reply]');

	migrateButton.on('click', function() {

		// Hide the button
		//migrateButton.hide();

		// Update the buttons message
		migrateButton.html('<i class="fa fa-cog fa-spin"></i> <?php echo JText::_('COM_EASYDISCUSS_MIGRATING', true);?>');

		// Hide the no progress message
		$('[data-progress-empty]').addClass('hide');

		// Ensure that the progress is always reset to empty just in case the user runs it twice.
		$('[data-progress-status]').html('');

		//show the loading icon
		$('[data-progress-loading]').removeClass('hide');

		//process the migration
		window.migrateArticle();

	});

	window.migrateArticle = function() {

		EasyDiscuss.ajax('admin/views/migrators/migrate', {
			"component": "com_kunena",
			"resetHits": $('[data-migrator-kunena-hits]').is(':checked') ? 1 : 0,
			"migrateSignature": $('[data-migrator-kunena-signature]').is(':checked') ? 1 : 0,
			"migrateAvatar": $('[data-migrator-kunena-avatar]').is(':checked') ? 1 : 0
		}).done(function(result, status, total) {

			// Append the current status
			$('[data-progress-status]').append(status);

			// If there's still items to render, run a recursive loop until it doesn't have any more items;
			if (result == true) {
				window.migrateArticle();
				return;
			}

			// Once finished the migrating topics, we continue with the replies
			$('[data-progress-status]').append('<?php echo JText::_('COM_EASYDISCUSS_MIGRATOR_MIGRATE_REPLIES', true);?>');

			window.migrateReplies(total);
		});
	},

	window.migrateReplies = function(total) {

		EasyDiscuss.ajax('admin/views/migrators/migrate', {
			"component": "com_kunena",
			"resetHits": 0,
			"migrateSignature": 0,
			"replies": true,
			"total": total
		}).done(function(result, status, total) {

			// Append the current status
			$('[data-progress-status]').append(status);

			// If there's still items to render, run a recursive loop until it doesn't have any more items;
			if (result == true) {
				window.migrateReplies(total);
				return;
			}

			//remove loading icon.
			$('[data-progress-loading]').addClass('hide');

			migrateButton.attr('disabled', true);

			migrateButton.html('<i class="fa fa-check"></i> <?php echo JText::_('COM_EASYDISCUSS_COMPLETED', true);?>');
			$('[data-progress-status]').append('<?php echo JText::_('COM_EASYDISCUSS_COMPLETED', true);?>');
		});
	}


});
