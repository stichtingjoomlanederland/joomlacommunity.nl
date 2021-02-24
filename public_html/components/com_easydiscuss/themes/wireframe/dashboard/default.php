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
<div class="ed-dashboard">
	<div class="l-stack">
		<h2 class="o-title"><?php echo JText::_('COM_EASYDISCUSS_DASHBOARD_TITLE'); ?></h2>

		<div class="o-tabs o-tabs--ed">
			<div class="o-tabs__item active" data-ed-tabs>
				<a href="#pending" data-ed-toggle="tab" class="o-tabs__link"><?php echo JText::_('Pending Posts');?></a>
			</div>

			<?php if ($this->config->get('main_work_schedule') && $this->acl->allowed('manage_holiday')) { ?>
			<div class="o-tabs__item" data-ed-tabs>
				<a href="#holidays" data-ed-toggle="tab" class="o-tabs__link"><?php echo JText::_('Manage Holidays');?></a>
			</div>	
			<?php } ?>
		</div>

		<div class="tab-content">
			<?php if (ED::isSiteAdmin() || $this->acl->allowed('manage_pending')) { ?>
			<div id="pending" class="tab-pane active">
				<?php echo $this->output('site/dashboard/pending/default', ['posts' => $posts]); ?>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_work_schedule') && $this->acl->allowed('manage_holiday')) { ?>
			<div id="holidays" class="tab-pane">
				<?php echo $this->output('site/dashboard/holidays/default', ['holidays' => $holidays]); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</div>