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
					$('.latest-versions').append(value);
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

<div class="block block-downloads">
	<h3><i class="fa fa-download " aria-hidden="true"></i> <?php echo $module->title; ?></h3>
	<p class="lead">Joomla! is gratis te downloaden en in het Nederlands beschikbaar. De nieuwste versie is <span class="latest-versions"></span></p>
	<a href="<?php echo $params->get("download_url", "http://downloads.joomla.org"); ?>" target="<?php echo $params->get("update_url_target", "_blank"); ?>" class="btn btn-downloads btn-block">
		<?php echo JText::_("MOD_JVERSIONS_DOWNLOAD_TEXT"); ?> <span class="latest-versions"></span>
	</a>
</div>