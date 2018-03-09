<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Params;
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>
<div class="akeeba-form-group">
	<label for="nonewadmins"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWADMINS'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nonewadmins', $this->wafconfig['nonewadmins']); ?>
</div>

<div class="akeeba-form-group">
	<label for="nonewfrontendadmins"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWFRONTENDADMINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWFRONTENDADMINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NONEWFRONTENDADMINS'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nonewfrontendadmins', $this->wafconfig['nonewfrontendadmins']); ?>
</div>

<div class="akeeba-form-group">
	<label for="configmonitor_global"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORGLOBAL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORGLOBAL_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORGLOBAL'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'configmonitor_global', $this->wafconfig['configmonitor_global']); ?>
</div>

<div class="akeeba-form-group">
	<label for="configmonitor_components"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORCOMPONENTS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORCOMPONENTS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORCOMPONENTS'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'configmonitor_components', $this->wafconfig['configmonitor_components']); ?>
</div>

<div class="akeeba-form-group">
	<label for="configmonitor_action"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORACTION'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORACTION_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONFIGMONITORACTION'); ?>
	</label>

    <?php echo Select::configMonitorAction('configmonitor_action', array(), $this->wafconfig['configmonitor_action']); ?>
</div>

<div class="akeeba-form-group">
    <label for="criticalfiles"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CRITICALFILES'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CRITICALFILES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CRITICALFILES'); ?>
    </label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'criticalfiles', $this->wafconfig['criticalfiles']); ?>
</div>

<div class="akeeba-form-group">
    <label for="superuserslist"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SUPERUSERSLIST'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SUPERUSERSLIST_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SUPERUSERSLIST'); ?>
    </label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'superuserslist', $this->wafconfig['superuserslist']); ?>
</div>

<div class="akeeba-form-group">
	<label
		   for="resetjoomlatfa"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RESETJOOMLATFA'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RESETJOOMLATFA_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_RESETJOOMLATFA'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'resetjoomlatfa', $this->wafconfig['resetjoomlatfa']); ?>
</div>

<div class="akeeba-form-group">
	<label for="nofesalogin"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_NOFESALOGIN'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'nofesalogin', $this->wafconfig['nofesalogin']); ?>
</div>

<div class="akeeba-form-group">
	<label
		   for="trackfailedlogins"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TRACKFAILEDLOGINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TRACKFAILEDLOGINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_TRACKFAILEDLOGINS'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'trackfailedlogins', $this->wafconfig['trackfailedlogins']); ?>
</div>

<?php
// Detect user registration and activation type
$disabled = '';
$message  = '';
$classes  = array('class' => 'akeeba-input-mini');

JLoader::import('cms.component.helper');
$userParams = JComponentHelper::getParams('com_users');

// User registration disabled
if (!$userParams->get('allowUserRegistration'))
{
	$classes['disabled'] = 'true';
	$disabled = ' disabled="true" ';
	$message = '<div style="margin-top:10px" class="akeeba-block--info">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_ALERT_NOREGISTRATION') . '</div>';
}
// Super User user activation
elseif ($userParams->get('useractivation') == 2)
{
	$message = '<div style="margin-top: 10px" class="akeeba-block--warning">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_ALERT_ADMINACTIVATION') . '</div>';
}
// No user activation
elseif ($userParams->get('useractivation') == 0)
{
	$classes['disabled'] = 'true';
	$disabled = ' disabled="true" ';
	$message = '<div style="margin-top:10px" class="akeeba-block--info">' . JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_ALERT_NOUSERACTIVATION') . '</div>';
}
?>

<div class="akeeba-form-group">
	<label
		   for="deactivateusers"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DEACTIVATEUSERS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DEACTIVATEUSERS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DEACTIVATEUSERS'); ?>
	</label>

	<div class="akeeba-form--inline">
		<input class="akeeba-input-mini" type="text" size="5" name="deactivateusers_num" <?php echo $disabled ?>
		value="<?php echo $this->escape($this->wafconfig['deactivateusers_num']); ?>"/>
		<span><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_DEACTIVATENUMFREQ'); ?></span>
		<input class="akeeba-input-mini" type="text" size="5" name="deactivateusers_numfreq" <?php echo $disabled ?>
		value="<?php echo $this->escape($this->wafconfig['deactivateusers_numfreq']); ?>"/>
		<?php
		echo Select::trsfreqlist('deactivateusers_frequency', $classes, $this->wafconfig['deactivateusers_frequency']);

		echo $message;
		?>
	</div>
</div>

<div class="akeeba-form-group">
    <label
           for="consolewarn"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONSOLEWARN'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONSOLEWARN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CONSOLEWARN'); ?>
    </label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'consolewarn', $this->wafconfig['consolewarn']); ?>
</div>

<div class="akeeba-form-group">
	<label
		   for="blockedemaildomains"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BLOCKEDEMAILDOMAINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BLOCKEDEMAILDOMAINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_BLOCKEDEMAILDOMAINS'); ?>
	</label>

    <textarea id="blockedemaildomains" name="blockedemaildomains" rows="5"><?php echo $this->escape($this->wafconfig['blockedemaildomains']); ?></textarea>
</div>
