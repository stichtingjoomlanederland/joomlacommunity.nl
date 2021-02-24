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
if (JVERSION >= 4)
{
	foreach ($this->pspForm->getFieldset('ogone') as $field) {
		if ($field->getAttribute('type') === 'radio')
		{
			$this->pspForm->setFieldAttribute($field->getAttribute('name'), 'layout', 'joomla.form.field.radio.switcher');
		}
	}
}
?>
<div class="span10">
	<?php foreach ($this->pspForm->getFieldset('ogone') as $field) : ?>
		<?php if (in_array($field->getAttribute('name'), ['payment', 'dynamic_parameters'], true)) : ?>
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
		<thead>
		<tr>
			<th><?php echo Text::_('COM_ROPAYMENTS_PRODUCTION_DASHBOARD'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_TEST_DASHBOARD'); ?></th>
		</tr>
		</thead>
		<tfoot><tr><td></td><td></td></tr></tfoot>
		<tbody>
		<tr>
			<td class="center">
				<?php
					echo HTMLHelper::_(
						'link',
						'https://secure.ogone.com/ncol/prod/',
						HTMLHelper::_('image', 'com_jdidealgateway/ingenico.gif', 'Ingenico', false, true),
						'target="_new"');
				?>
			</td>
			<td class="center">
				<?php
					echo HTMLHelper::_(
						'link',
						'https://secure.ogone.com/ncol/test/',
						HTMLHelper::_('image', 'com_jdidealgateway/ingenico.gif', 'Ingenico', false, true),
						'target="_new"'
					);
				?>
			</td>
		</tr>
		</tbody>
	</table>
</div>
