<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/** @var JdidealgatewayViewEmails $this */

if (JVERSION < 4)
{
	HTMLHelper::_('formbehavior.chosen');
}
?>
<div class="container-fluid">
	<div class="control-group">
		<div class="control-label">
			<label for="width">
				<?php echo Text::_('COM_ROPAYMENTS_SELECT_EMAIL')?>
			</label>
		</div>
		<div class="controls">
			<?php echo HTMLHelper::_('select.genericlist', $this->emails, 'emailId', 'class="custom-select advancedSelect"'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="width">
				<?php echo Text::_('COM_ROPAYMENTS_TESTMAIL_ADDRESS')?>
			</label>
		</div>
		<div class="controls">
			<input class="form-control" type="text" name="email" id="email" required>
		</div>
	</div>
</div>
