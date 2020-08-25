<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewJdidealgateway $this */

HTMLHelper::_('behavior.polyfill', array('event'), 'lt IE 9');
HTMLHelper::_('formbehavior.chosen');

$editor = Factory::getApplication()->input->getCmd('editor', '');

if (!empty($editor))
{
	// This view is used also in com_menus. Load the xtd script only if the editor is set!
	Factory::getDocument()->addScriptOptions('xtd-jdideal', array('editor' => $editor));
}

// Add the JavaScript
HTMLHelper::_('script', 'com_jdidealgateway/admin-jdideal-modal.js', array('version' => 'auto', 'relative' => true));

?>
<div class="container-popup">
	<form class="form-horizontal">

		<div class="control-group">
			<label for="title" class="control-label"><?php echo Text::_('COM_ROPAYMENTS_TITLE'); ?></label>
			<div class="controls"><input type="text" id="title" name="title" /></div>
		</div>

		<div class="control-group">
			<label for="amount" class="control-label"><?php echo Text::_('COM_ROPAYMENTS_AMOUNT'); ?></label>
			<div class="controls"><input type="text" id="amount" name="amount" /></div>
		</div>

		<div class="control-group">
			<label for="email" class="control-label"><?php echo Text::_('COM_ROPAYMENTS_USEREMAIL'); ?></label>
			<div class="controls"><input type="text" id="email" name="email" /></div>
		</div>

		<div class="control-group">
			<label for="remark" class="control-label"><?php echo Text::_('COM_ROPAYMENTS_REMARK'); ?></label>
			<div class="controls"><input type="text" id="remark" name="remark" /></div>
		</div>

		<div class="control-group">
			<label for="order_number" class="control-label"><?php echo Text::_('COM_ROPAYMENTS_ORDERNR'); ?></label>
			<div class="controls"><input type="text" id="order_number" name="order_number" /></div>
		</div>

		<div class="control-group">
			<label for="silent" class="control-label"><?php echo Text::_('COM_ROPAYMENTS_SILENT'); ?></label>
			<div class="controls">
				<select id="silent" name="silent" class="advancedSelect">
					<option value="0"><?php echo Text::_('JNO'); ?></option>
					<option value="1"><?php echo Text::_('JYES'); ?></option>
				</select>
			</div>
		</div>

		<button onclick="jdidealButton();" class="btn btn-success pull-right">
			<?php echo Text::_('COM_ROPAYMENTS_INSERT_LINK'); ?>
		</button>

	</form>
</div>
