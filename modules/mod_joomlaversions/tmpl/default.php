<?php
/**
 * @package     Joomla_Versions
 * @subpackage  mod_joomlaversions
 *
 * @copyright   Copyright (C) 2016 Joomla! Community. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;
?>
<div class="latest_version"></div>

<script>
	(function ($) {
		$(document).ready(function() {
			var request = {
				'option': 'com_ajax',
				'module': 'joomlaversions',
				'prefixes': <?php echo json_encode($params->get('prefixes', array())); ?>,
				'update_url' : '<?php echo $params->get('update_url', 'https://update.joomla.org/core/list.xml'); ?>',
				'format': 'raw'
			};

			$.ajax({
				type: 'POST',
				data: request,
				dataType: 'json',
				success: function (response) {
					$.each(response, function(index, value) {
						$('.latest_version').append('<div class="jversion"><span class="icon icon-joomla"></span><span class="text">' + value + '</span></div>');
					});
				},
				error: function (response) {
					$('.latest_version').html('<?php echo JText::_('MOD_JOOMLAVERSIONS_RESPONSE_ERROR'); ?>');
				}
			});

			return false;
		});
	})(jQuery)
</script>
