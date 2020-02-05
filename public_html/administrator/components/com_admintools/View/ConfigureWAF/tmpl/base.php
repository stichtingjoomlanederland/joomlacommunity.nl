<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;
use FOF30\Date\Date;

defined('_JEXEC') or die;

$serverTZName = $this->container->platform->getConfig()->get('offset', 'UTC');

try
{
	$timezone = new DateTimeZone($serverTZName);
}
catch (Exception $e)
{
	$timezone = new DateTimeZone('UTC');
}

$date = new Date('now');
$date->setTimezone($timezone);
$timezoneName = $date->format('T', true);

?>
<div class="akeeba-form-group">
	<label for="ipworkarounds"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_TIP'); ?>"
	>
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>
	</label>

    <?php //echo \JHtml::_('FEFHelper.select.booleanswitch', 'ipworkarounds', $this->wafconfig['ipworkarounds']); ?>
    <?php echo Select::ipworkarounds('ipworkarounds', '' , $this->wafconfig['ipworkarounds'])?>
</div>

<div class="akeeba-form-group">
	<label for="ipwl"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>"
		   data-content="<?php echo JText::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_IPWL_TIP', 'index.php?option=com_admintools&view=WhitelistedAddresses') ?>"
	>
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'ipwl', $this->wafconfig['ipwl']); ?>
</div>

<div class="akeeba-form-group">
	<label for="ipbl"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPBL'); ?>"
		   data-content="<?php echo JText::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_IPBL_TIP', 'index.php?option=com_admintools&view=BlacklistedAddresses') ?>"
	>
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPBL'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'ipbl', $this->wafconfig['ipbl']); ?>
</div>

<div class="akeeba-form-group">
	<label for="adminpw"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>
	</label>

    <input type="text" size="20" name="adminpw" value="<?php echo $this->escape($this->wafconfig['adminpw']); ?>"/>
</div>

<div class="akeeba-form-group">
    <label for="selfprotect"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SELFPROTECT'); ?>"
           data-content="<?php echo JText::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_SELFPROTECT_TIP', 'plugins/system/admintools') ?>"
    >
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SELFPROTECT'); ?>
    </label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'selfprotect', $this->wafconfig['selfprotect']); ?>
</div>

<div class="akeeba-form-group">
	<label for="awayschedule_from"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE'); ?>
	</label>

	<div class="akeeba-form--inline">
		<?php echo \JText::sprintf('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_FROM', $timezoneName); ?>
		<input type="text" name="awayschedule_from" id="awayschedule_from" class="input-mini"
			   value="<?php echo $this->wafconfig['awayschedule_from'] ?>"/>
		<?php echo \JText::sprintf('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_TO', $timezoneName); ?>
		<input type="text" name="awayschedule_to" id="awayschedule_to" class="input-mini"
			   value="<?php echo $this->escape($this->wafconfig['awayschedule_to']); ?>"/>

		<div class="akeeba-block--info" style="margin-top: 10px">
			<?php
			echo JText::sprintf('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_TIMEZONE', $date->format('H:i T', true), $serverTZName);
			?>
		</div>
	</div>
</div>

<div class="akeeba-block--warning">
	<h3>
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_CUSTOMADMIN_NOTICE_HEAD'); ?>
	</h3>
	<p>
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_CUSTOMADMIN_NOTICE_TEXT'); ?>
	</p>
	<?php
	$disabled = '';
	$message = '';

	if (!$this->container->platform->getConfig()->get('sef') || !$this->container->platform->getConfig()->get('sef_rewrite'))
	{
		$disabled = ' disabled="true"';
		$message = '<div class="akeeba-block--warning" style="margin:10px 0 0">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER_ALERT') . '</div>';
	}
	?>

	<div class="akeeba-form-group">
		<label for="adminlogindir"
			   rel="akeeba-sticky-tooltip"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER_TIP'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER'); ?>
		</label>

		<div>
			<input type="text" <?php echo $disabled ?>size="20" name="adminlogindir"
				   value="<?php echo $this->escape($this->wafconfig['adminlogindir']); ?>"/>
			<?php echo $message ?>
		</div>
	</div>
</div>
