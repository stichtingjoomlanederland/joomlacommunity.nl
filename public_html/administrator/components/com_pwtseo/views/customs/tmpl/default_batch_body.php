<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

HTMLHelper::_('stylesheet', 'plg_system_pwtseo/pwtseo.css', array('version' => 'auto', 'relative' => true));
$plugin = PluginHelper::getPlugin('system', 'pwtseo');

$params = new Registry($plugin->params);

?>

<div class="form-horizontal">
	<?php echo HTMLHelper::_('bootstrap.startTabSet', 'tabs', array('active' => 'attrib-advfields')); ?>

	<?php echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'attrib-advfields', Text::_('COM_PWTSEO_BATCH_ADVANCED_FIELDS')); ?>
	<div class="container-fluid">
		<?php echo $this->batchForm->renderFieldSet('advfields') ?>
	</div>
	<?php echo HTMLHelper::_('bootstrap.endTab'); ?>

	<?php echo HTMLHelper::_('bootstrap.addTab', 'tabs', 'attrib-structured', Text::_('COM_CONFIG_STRUCTUREDDATA_FIELDSET_LABEL')); ?>
	<div class="container-fluid">
		<div class="control-group span3">
			<label id="jform_enabled-lbl" for="jform_enabled" class="modalTooltip" title="<?php echo HTMLHelper::_('tooltipText', 'COM_PWTSEO_BATCH_OVERRIDE_LABEL', 'COM_PWTSEO_BATCH_OVERRIDE_DESC'); ?>">
				<?php echo Text::_('COM_PWTSEO_BATCH_OVERRIDE_LABEL'); ?>
			</label>
			<select id="batch-override_structured" name="batch[override_structured]" class="chzn-color-state" size="1">
				<option value="1"><?php echo Text::_('JYES') ?></option>
				<option value="0" selected="selected"><?php echo Text::_('JNO') ?></option>
			</select>
		</div>
		<?php echo $this->batchForm->renderFieldSet('structured') ?>
	</div>
	<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
	<?php echo HTMLHelper::_('bootstrap.endTabSet'); ?>
</div>
<script>
	jQuery(document).ready(function () {
		var $sDescription = jQuery('#batch-metadesc');

		if ($sDescription && $sDescription.length) {
			$sDescription.on('keyup', function () {
				document.querySelector('.js-pwtseo-medescription-counter-amount').innerHTML = this.value.length;

				if (this.value.length > <?php echo $params->get('count_max_metadesc'); ?>) {
					jQuery('.pseo-meta-counter').removeClass('pwtseo-color-green').addClass('pwtseo-color-red');
				} else {
					jQuery('.pseo-meta-counter').removeClass('pwtseo-color-red').addClass('pwtseo-color-green');

				}
			});
		}
	});
</script>
