Komento.ready(function($) {
	if (Komento.options.responsive)
	{
		$(function() {
			$('#section-kmt').responsive([
				{at: 600, switchTo: 'w600'},
				{at: 400, switchTo: 'w600 w320'}
			]);
		});
	}
});
