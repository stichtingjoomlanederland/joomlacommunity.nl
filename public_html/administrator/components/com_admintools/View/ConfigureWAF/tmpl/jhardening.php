<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Params;
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>
<div class="control-group">
	<label class="control-label" for="nonewadmins"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('nonewadmins', array(), $this->wafconfig['nonewadmins']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="resetjoomlatfa"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RESETJOOMLATFA'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RESETJOOMLATFA_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RESETJOOMLATFA'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('resetjoomlatfa', array(), $this->wafconfig['resetjoomlatfa']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="nofesalogin"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('nofesalogin', array(), $this->wafconfig['nofesalogin']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="trackfailedlogins"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TRACKFAILEDLOGINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TRACKFAILEDLOGINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TRACKFAILEDLOGINS'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('trackfailedlogins', array(), $this->wafconfig['trackfailedlogins']); ?>

	</div>
</div>

<?php
// Detect user registration and activation type
$disabled = '';
$message  = '';
$classes  = array('class' => 'input-small');

JLoader::import('cms.component.helper');
$userParams = JComponentHelper::getParams('com_users');

// User registration disabled
if (!$userParams->get('allowUserRegistration'))
{
	$classes['disabled'] = 'true';
	$disabled = ' disabled="true" ';
	$message = '<div style="margin-top:10px" class="alert alert-info">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_ALERT_NOREGISTRATION') . '</div>';
}
// Super User user activation
elseif ($userParams->get('useractivation') == 2)
{
	$message = '<div style="margin-top: 10px" class="alert">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_ALERT_ADMINACTIVATION') . '</div>';
}
// No user activation
elseif ($userParams->get('useractivation') == 0)
{
	$classes['disabled'] = 'true';
	$disabled = ' disabled="true" ';
	$message = '<div style="margin-top:10px" class="alert alert-info">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_ALERT_NOUSERACTIVATION') . '</div>';
}
?>

<div class="control-group">
	<label class="control-label"
		   for="deactivateusers"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DEACTIVATEUSERS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DEACTIVATEUSERS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DEACTIVATEUSERS'); ?>
	</label>

	<div class="controls">
		<input class="input-mini pull-left" type="text" size="5" name="deactivateusers_num" <?php echo $disabled ?>
		value="<?php echo $this->escape($this->wafconfig['deactivateusers_num']); ?>"/>
		<span class="floatme"><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_DEACTIVATENUMFREQ'); ?></span>
		<input class="input-mini" type="text" size="5" name="deactivateusers_numfreq" <?php echo $disabled ?>
		value="<?php echo $this->escape($this->wafconfig['deactivateusers_numfreq']); ?>"/>
		<?php
		echo Select::trsfreqlist('deactivateusers_frequency', $classes, $this->wafconfig['deactivateusers_frequency']);

		echo $message;
		?>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="blockedemaildomains"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BLOCKEDEMAILDOMAINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BLOCKEDEMAILDOMAINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BLOCKEDEMAILDOMAINS'); ?>
	</label>

	<div class="controls">
		<textarea id="blockedemaildomains" name="blockedemaildomains" rows="5"><?php echo $this->escape($this->wafconfig['blockedemaildomains']); ?></textarea>
	</div>
</div>