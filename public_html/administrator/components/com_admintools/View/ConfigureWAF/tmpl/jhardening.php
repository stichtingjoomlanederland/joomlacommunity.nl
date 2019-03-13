<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */

use Akeeba\AdminTools\Admin\Helper\Params;
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;

?>
<div class="akeeba-form-group">
    <label
            for="leakedpwd"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LEAKEDPWD'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LEAKEDPWD_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LEAKEDPWD'); ?>
    </label>

	<?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'leakedpwd', $this->wafconfig['leakedpwd']); ?>
</div>

<div class="akeeba-form-group">
    <label
            for="leakedpwd"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LEAKEDPWD_GROUPS'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LEAKEDPWD_GROUPS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LEAKEDPWD_GROUPS'); ?>
    </label>

	<?php echo JHtml::_('access.usergroup', 'leakedpwd_groups[]', $this->wafconfig['leakedpwd_groups'], ['multiple' => true, 'size' => 5], false)?>

</div>

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
    <label for="criticalfiles_global"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CRITICALFILES_GLOBAL'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CRITICALFILES_GLOBAL_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_CRITICALFILES_GLOBAL'); ?>
    </label>

	<textarea id="criticalfiles_global" name="criticalfiles_global" rows="5"><?php echo $this->escape($this->wafconfig['criticalfiles_global'])?></textarea>
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
            for="filteremailregistration"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_FILTER_REGISTRATION'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_FILTER_REGISTRATION_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_FILTER_REGISTRATION'); ?>
    </label>

    <?php
	    $checked_1 = $this->wafconfig['filteremailregistration'] == 'allow' ? '' : 'checked ';
	    $checked_2 = $this->wafconfig['filteremailregistration'] == 'block' ? 'checked ' : '';
    ?>

    <div class="akeeba-toggle">
        <input type="radio" class="radio-allow" name="filteremailregistration" <?php echo $checked_2 ?> id="filteremailregistration-2" value="allow">
        <label for="filteremailregistration-2" class="green"><?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_FILTER_REGISTRATION_ALLOW') ?></label>
        <input type="radio" class="radio-block" name="filteremailregistration" <?php echo $checked_1 ?> id="filteremailregistration-1" value="block">
        <label for="filteremailregistration-1" class="red"><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_FILTER_REGISTRATION_BLOCK') ?></label>
    </div>
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

<h4><?php echo JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_HEAD') ?></h4>

<div class="akeeba-form-group">
    <label
            for="disableobsoleteadmins"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS'); ?>
    </label>

	<?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'disableobsoleteadmins', $this->wafconfig['disableobsoleteadmins']); ?>
</div>

<div class="akeeba-form-group">
    <label
            for="disableobsoleteadmins_freq"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_FREQ'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_FREQ_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_FREQ'); ?>
    </label>

    <input class="akeeba-input-mini" type="text" size="5" name="disableobsoleteadmins_freq"
           value="<?php echo $this->escape($this->wafconfig['disableobsoleteadmins_freq']); ?>"/>
</div>

<div class="akeeba-form-group">
    <label
            for="disableobsoleteadmins_groups"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_GROUPS'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_GROUPS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_GROUPS'); ?>
    </label>

	<?php echo JHtml::_('access.usergroup', 'disableobsoleteadmins_groups[]', $this->wafconfig['disableobsoleteadmins_groups'], [
		'multiple' => true, 'size' => 5,
	], true) ?>
</div>

<div class="akeeba-form-group">
    <label
            for="disableobsoleteadmins_maxdays"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_MAXDAYS'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_MAXDAYS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_MAXDAYS'); ?>
    </label>

    <input class="akeeba-input-mini" type="text" size="5" name="disableobsoleteadmins_maxdays"
           value="<?php echo $this->escape($this->wafconfig['disableobsoleteadmins_maxdays']); ?>"/>
</div>

<div class="akeeba-form-group">
    <label for="disableobsoleteadmins_action"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_ACTION'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_ACTION_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_ACTION'); ?>
    </label>

	<?php echo Select::disableObsoleteAdminsAction('disableobsoleteadmins_action', array(), $this->wafconfig['disableobsoleteadmins_action']); ?>
</div>

<div class="akeeba-form-group">
    <label
            for="disableobsoleteadmins_protected"
            rel="akeeba-sticky-tooltip"
            data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_PROTECTED'); ?>"
            data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_PROTECTED_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_DISABLEOBSOLETEADMINS_PROTECTED'); ?>
    </label>

    <?php echo Select::backendUsers('disableobsoleteadmins_protected[]', [
	    'multiple' => true, 'size' => 10, 'class' => 'advancedSelect'
    ], $this->wafconfig['disableobsoleteadmins_protected']) ?>
</div>
