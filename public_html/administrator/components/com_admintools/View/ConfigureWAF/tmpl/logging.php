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
	<label class="control-label"
		   for="saveusersignupip"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SAVEUSERSIGNUPIP'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SAVEUSERSIGNUPIP_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SAVEUSERSIGNUPIP'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('saveusersignupip', array(), $this->wafconfig['saveusersignupip']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="logbreaches"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LOGBREACHES'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LOGBREACHES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LOGBREACHES'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('logbreaches', array(), $this->wafconfig['logbreaches']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label" for="iplookup"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_LABEL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_DESC'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_LABEL'); ?>
	</label>

	<div class="controls">
		<?php echo Select::httpschemes('iplookupscheme', array('class' => 'input-small'), $this->wafconfig['iplookupscheme']); ?>

		<input type="text" size="50" name="iplookup" value="<?php echo $this->escape($this->wafconfig['iplookup']); ?>"
			   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_DESC'); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="emailbreaches"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>
	</label>

	<div class="controls">
		<input type="text" size="20" name="emailbreaches" value="<?php echo $this->escape($this->wafconfig['emailbreaches']); ?>">
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="emailonadminlogin"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINLOGIN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINLOGIN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINLOGIN'); ?>
	</label>

	<div class="controls">
		<input type="text" size="20" name="emailonadminlogin"
			   value="<?php echo $this->escape($this->wafconfig['emailonadminlogin']); ?>" >
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="emailonfailedadminlogin"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINFAILEDLOGIN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINFAILEDLOGIN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINFAILEDLOGIN'); ?>
	</label>

	<div class="controls">
		<input type="text" size="20" name="emailonfailedadminlogin"
			   value="<?php echo $this->escape($this->wafconfig['emailonfailedadminlogin']); ?>">
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="showpwonloginfailure"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SHOWPWONLOGINFAILURE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SHOWPWONLOGINFAILURE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SHOWPWONLOGINFAILURE'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('showpwonloginfailure', array(), $this->wafconfig['showpwonloginfailure']); ?>

	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="reasons_nolog"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOLOG'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOLOG_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOLOG'); ?>
	</label>

	<div class="controls">
		<?php
		echo Select::reasons($this->wafconfig['reasons_nolog'], 'reasons_nolog[]', array(
						'class'     => 'advancedSelect input-large',
						'multiple'  => 'multiple',
						'size'      => 5,
						'hideEmpty' => true
				)
		)
		?>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="reasons_noemail"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOEMAIL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOEMAIL_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOEMAIL'); ?>
	</label>

	<div class="controls">
		<?php
		echo Select::reasons($this->wafconfig['reasons_noemail'], 'reasons_noemail[]', array(
						'class'     => 'advancedSelect input-large',
						'multiple'  => 'multiple',
						'size'      => 5,
						'hideEmpty' => true
				)
		)
		?>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="email_throttle"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILTHROTTLE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILTHROTTLE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILTHROTTLE'); ?>
	</label>

	<div class="controls">
		<?php echo Select::booleanlist('email_throttle', array(), $this->wafconfig['email_throttle']); ?>

	</div>
</div>
