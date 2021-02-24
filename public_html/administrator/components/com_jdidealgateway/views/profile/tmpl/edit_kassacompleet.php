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

/** @var JdidealgatewayViewProfile $this */
?>
<div class="span10">
	<?php foreach ($this->pspForm->getFieldset('kassacompleet') as $field) : ?>
		<?php if ($field->getAttribute('name') === 'payment') : ?>
			<joomla-field-fancy-select>
				<?php echo $field->renderField(); ?>
			</joomla-field-fancy-select>
		<?php else : ?>
			<?php echo $field->renderField(); ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>
<div class="span2">
	<table class="table table-striped">
		<caption><?php echo Text::_('COM_ROPAYMENTS_DASHBOARD_LINKS')?></caption>
		<thead><tr><th><?php echo Text::_('COM_ROPAYMENTS_PRODUCTION_DASHBOARD'); ?></th></tr></thead>
		<tfoot><tr><td></td><td></td></tr></tfoot>
		<tbody>
			<tr>
				<td class="center"><?php echo HTMLHelper::_('link', 'https://portal.kassacompleet.nl/', HTMLHelper::_('image', 'com_jdidealgateway/ing.jpg', 'ING', false, true), 'target="_new"'); ?></td>
			</tr>
		</tbody>
	</table>
</div>
