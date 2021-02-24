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
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_JOMSOCIAL_INTEGRATIONS', '', '/docs/easydiscuss/administrators/integrations/integrations#jomsocial'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_jomsocial_points', 'COM_EASYDISCUSS_JOMSOCIAL_USERPOINTS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_toolbar_jomsocial_profile', 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_PROFILE'); ?>
					<?php echo $this->html('settings.toggle', 'integration_jomsocial_messaging', 'COM_EASYDISCUSS_LINK_TO_JOMSOCIAL_MESSAGING'); ?>
					<?php echo $this->html('settings.toggle', 'integration_jomsocial_stream', 'COM_ED_JOMSOCIAL_INTEGRATE_ACTIVITY_STREAM'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
