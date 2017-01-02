<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div class="control-group">
	<label class="control-label" for="ipworkarounds"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_TIP'); ?>"
	>
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_IPWORKAROUNDS'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('ipworkarounds', array(), $this->wafconfig['ipworkarounds']); ?>

		<div class="help-block">
			<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_RECOMMENDED'); ?>
			<span class="label label-default" id="ipWorkaroundsRecommendedSetting">
				<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_IPWORKAROUNDS_RECOMMENDED_WAIT'); ?>
			</span>
		</div>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="ipwl"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>"
		   data-content="<?php echo JText::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_IPWL_TIP', 'index.php?option=com_admintools&view=WhitelistedAddresses') ?>"
	>
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPWL'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('ipwl', array(), $this->wafconfig['ipwl']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="ipbl"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPBL'); ?>"
		   data-content="<?php echo JText::sprintf('COM_ADMINTOOLS_CONFIGUREWAF_IPBL_TIP', 'index.php?option=com_admintools&view=BlacklistedAddresses') ?>"
	>
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_IPBL'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('ipbl', array(), $this->wafconfig['ipbl']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="adminpw"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINPW'); ?>
	</label>

	<div class="controls">
		<input type="text" size="20" name="adminpw" value="<?php echo $this->escape($this->wafconfig['adminpw']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="awayschedule_from"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE'); ?>
	</label>

	<div class="controls">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_FROM'); ?>
		<input type="text" name="awayschedule_from" id="awayschedule_from" class="input-mini"
			   value="<?php echo $this->wafconfig['awayschedule_from'] ?>"/>
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_TO'); ?>
		<input type="text" name="awayschedule_to" id="awayschedule_to" class="input-mini"
			   value="<?php echo $this->escape($this->wafconfig['awayschedule_to']); ?>"/>

		<div class="alert alert-info" style="margin-top: 10px">
			<?php
			$date = new JDate('now', JFactory::getConfig()->get('offset', 'UTC'));
			echo JText::sprintf('COM_ADMINTOOLS_LBL_CONFIGUREWAF_AWAYSCHEDULE_TIMEZONE', $date->format('H:i', true));
			?>
		</div>
	</div>
</div>

<div class="well well-small">
	<h3>
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_CUSTOMADMIN_NOTICE_HEAD'); ?>
	</h3>
	<p>
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_CUSTOMADMIN_NOTICE_TEXT'); ?>
	</p>
	<?php
	$disabled = '';
	$message = '';

	if (!JFactory::getConfig()->get('sef') || !JFactory::getConfig()->get('sef_rewrite'))
	{
		$disabled = ' disabled="true"';
		$message = '<div class="alert" style="margin:10px 0 0">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER_ALERT') . '</div>';
	}
	?>

	<div class="control-group">
		<label class="control-label" for="adminlogindir"
			   rel="popover"
			   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER'); ?>"
			   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER_TIP'); ?>">
			<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_ADMINLOGINFOLDER'); ?>
		</label>

		<div class="controls">
			<input type="text" <?php echo $disabled ?>size="20" name="adminlogindir"
				   value="<?php echo $this->escape($this->wafconfig['adminlogindir']); ?>"/>
			<?php echo $message ?>
		</div>
	</div>
</div>
