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
	foreach ($this->pspForm->getFieldset('ing-lite') as $field) {
		if ($field->getAttribute('type') === 'radio')
		{
			$this->pspForm->setFieldAttribute($field->getAttribute('name'), 'layout', 'joomla.form.field.radio.switcher');
		}
	}
}
?>
<div class="span10">
	<?php echo $this->pspForm->renderFieldset('ing-lite'); ?>
</div>
<div class="span2">
	<table class="table table-striped">
		<caption><?php echo Text::_('COM_ROPAYMENTS_DASHBOARD_LINKS')?></caption>
		<thead><tr><th><?php echo Text::_('COM_ROPAYMENTS_PRODUCTION_DASHBOARD'); ?></th><th><?php echo Text::_('COM_ROPAYMENTS_TEST_DASHBOARD'); ?></th></tr></thead>
		<tfoot><tr><td></td><td></td></tr></tfoot>
		<tbody>
			<tr>
				<td class="center"><?php echo HTMLHelper::_('link', 'https://ideal.secure-ing.com/ideal/logon_ing.do', HTMLHelper::_('image', 'com_jdidealgateway/ing.jpg', 'ING', false, true), 'target="_new"'); ?></td>
				<td class="center"><?php echo HTMLHelper::_('link', 'https://idealtest.secure-ing.com/ideal/logon_ing.do', HTMLHelper::_('image', 'com_jdidealgateway/ing.jpg', 'ING', false, true), 'target="_new"'); ?></td>
			</tr>
		</tbody>
	</table>
</div>
<div class="clr"></div>
