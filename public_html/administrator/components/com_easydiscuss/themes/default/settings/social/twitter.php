<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_SOCIAL_TWITTER_TITLE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_twitter_button', 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_USE_TWITTER_BUTTON'); ?>
					<?php echo $this->html('settings.toggle', 'integration_twitter_card', 'COM_ED_TWITTER_LOAD_TWITTER_CARDS'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
