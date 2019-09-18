<?php if ($this->config->get('integration_google_adsense_script')) { ?>
ed.require(['edq', '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js'], function($, responsive) {

	// Determine how many ads unit is available
	var adsElement = $('.adsbygoogle');

	if (adsElement.length > 0) {
		$.each(adsElement, function() {

			// Activate the ads
			(adsbygoogle = window.adsbygoogle || []).push({});
		});
	}
});
<?php } ?>