<?php
/**
 * @package     JVersions
 * @subpackage  mod_jversions
 *
 * @copyright   Copyright (C) 2016 Niels van der Veer. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

// Define script and get paramters
$script = "
(function ($) {
	$(document).ready(function() {
		var request = {
			'option': 'com_ajax',
			'module': 'jversions',
			'prefixes': " . json_encode($params->get('prefixes', array())) . ",
			'update_url' : '" . $params->get('update_url', 'https://update.joomla.org/core/list.xml') . "',
			'format': 'raw'
		};

		$.ajax({
			type: 'POST',
			data: request,
			dataType: 'json',
			success: function (response) {
				$.each(response, function(index, value) {
					$('.latest-versions').append('<div class=\"jversion\"><span class=\"icon icon-joomla\"></span><span class=\"text\">' + value + '</span></div>');
				});
			},
			error: function (response) {
				$('.latest-versions').html('" . JText::_('MOD_JVERSIONS_RESPONSE_ERROR') . "');
			}
		});

		return false;
	});
})(jQuery)
";

// Add script to the head section
JFactory::getDocument()->addScriptDeclaration($script);
?>

<div class="mod_joomlaversions">
	<div class="latest-versions"></div>
	<a href="<?php echo $params->get("download_url", "http://downloads.joomla.org"); ?>" class="btn btn-default btn-block"><?php echo JText::_("MOD_JVERSIONS_DOWNLOAD_TEXT"); ?></a>
</div>

