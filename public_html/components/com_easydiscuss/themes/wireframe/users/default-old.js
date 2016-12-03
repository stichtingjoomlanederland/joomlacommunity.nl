ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {

	$('[data-search-button]').click(function(el){

		el.preventDefault();

		if ($('[data-user-search-text]').val() != "") {

			//submit the form
			$('[data-user-search-form]').submit();
		}
	});

});
