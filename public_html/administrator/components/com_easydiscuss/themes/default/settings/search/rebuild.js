ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

var pages = <?php echo json_encode($items); ?>;
var result = {
	'name': 'settings',
	'description': 'Settings database for search',
	'items': []
};

var requests = [];

$(pages).each(function(n, page) {

requests.push($.ajax({
	"type": "GET",
	"url": '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=settings&layout=' + page,
	"success": function(html) {
		d = html.replace(/(<\/?)html( .+?)?>/gi,'$1NOTHTML$2>',html)
		d = d.replace(/(<\/?)body( .+?)?>/gi,'$1NOTBODY$2>',d)

		// select the `notbody` tag and log for testing
		var y = $(d).find('notbody').html();

		y = $(y);

		var tabs = y.find('.tab-pane');

		tabs.each(function(i, tab) {
			var tab = $(tab);
			var tabId = tab.attr('id');
			var items = tab.find('.o-form-group');


			items.each(function(x, item) {
				var label = $(item).find('label');
				var labelText = $.trim(label.text());
				var labelUid = label.data('uid');

				if (!labelUid) {
					var exclude = $(item).data('search-exclude');

					// Explicitly exclude
					if (exclude !== undefined) {
						return
					}

					console.log($(item).html());
					alert('Error retrieving uid for label');
					
					return;
				}

				// If there are no labels, we should just skip this
				if (!labelText) {
					console.log($(item).html());
					alert('Error processing item');

					return;
				}

				var desc = $(item).find('i').data('content');

				var data = {
					"id": labelUid,
					"page": page,
					"tab": tabId,
					"label": labelText,
					"description": desc
				};

				result.items.push(data);
			});

		});
	}
}));

});

$.when.apply(null, requests).done(function() {
	var resultString = JSON.stringify(result);

	// Finalize and send the data to the server
	EasyDiscuss.ajax('admin/views/settings/rebuildSearch', {
		dataString: resultString
	}).done(function() {
		window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=settings';
	});
});


});
