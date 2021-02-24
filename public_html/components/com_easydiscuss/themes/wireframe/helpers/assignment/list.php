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
<?php if ($moderators) { ?>
	<?php foreach ($moderators as $id => $name) { ?>
		<li>
			<a href="javascript:void(0);" class="o-dropdown__item" data-ed-moderator-item data-id="<?php echo $id; ?>">
				<?php if ($id == 0) { ?>
					<?php echo JText::_('COM_ED_NONE'); ?>
				<?php } else { ?>
					<?php echo $name; ?>
				<?php } ?>
			</a>

			<div class="t-d--none" data-tpl>
				<?php echo $this->output('site/helpers/assignment/assignee', ['moderator' => $id ? ED::user($id) : null]); ?>
			</div>
		</li>
		<?php if ($id == 0) { ?>
		<li><hr class="o-dropdown-divider"></li>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
<li>
	<div style="padding: 20px; width: 320px;">
		<?php echo JText::_('COM_EASYDISCUSS_NO_MODERATOR_FOUND'); ?>
	</div>
</li>
<?php } ?>