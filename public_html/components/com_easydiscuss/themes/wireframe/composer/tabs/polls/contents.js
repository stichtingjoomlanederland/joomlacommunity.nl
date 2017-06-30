ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	var wrapper = $('[<?php echo $editorId;?>]');
	var list = $('[data-ed-polls-list]');
	
	var insertPoll = wrapper.find('[data-ed-polls-insert]');
	var removePoll = wrapper.find('[data-ed-polls-remove]');
	var inputPoll = wrapper.find('[data-ed-polls-input]');

	// Bind the add url reference
	$(document)
		.on('click.ed.polls.insert', insertPoll.selector, function() {

			// Clone the first item on the list.
			var item = list.children(':first').clone();

			// Clear the input
			item.find('input').val('');

			// Append item into the list.
			list.append(item);
		});

	// Bind the remove url reference
	$(document)
		.on('click.ed.polls.remove', removePoll.selector, function() {

			var parent = $(this).parent();
			var id = parent.find('input').data('id');

			// Whenever the poll is removed, we need to store the id in the remove queue
			var removeQueue = wrapper.find('[data-ed-polls-removed-items]');
			var removeValue = removeQueue.val();

			removeValue = removeValue.length > 0 ? removeValue.split('_') : [];
			
			// Remove the id
			if (id) {
				removeValue.push(id);
			}

			removeQueue.val(removeValue.join(','));
			
			// Remove the parent
			parent.remove();
		});

	// Bind enter key 
	$(document)
		.on('keyup.ed.polls.input', inputPoll.selector, function(event) {

			if (event.keyCode == 13) {

				// Simulating the click event to insert new items
				$('[data-ed-polls-insert]').click();

				// Focus on it as soon as the input is added.
				var item = list.children(':last').find('input');
				item.focus();
			}
		});
});