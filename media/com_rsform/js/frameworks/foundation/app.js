jQuery(document).foundation();

jQuery(document).ready(function(){
	jQuery('.has-tip').each(function(){
		var tooltipTitle = jQuery(this).attr('title');
		if (tooltipTitle.length > 0) {
			new Foundation.Tooltip(jQuery(this), {});
		} else {
			jQuery(this).removeClass('has-tip');
		}
	})
});
