<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="dc-adsense-wrap">
	<script><!--
	<?php echo html_entity_decode("$adsense\n"); ?>
	//--></script>

	<?php if ($this->config->get('integration_google_adsense_script')) { ?>
		<script src="https://pagead2.googlesyndication.com/pagead/show_ads.js">
		</script>
	<?php } ?>
</div>