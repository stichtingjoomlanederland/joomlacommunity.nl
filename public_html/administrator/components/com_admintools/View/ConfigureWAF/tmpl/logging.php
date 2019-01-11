<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
use Akeeba\AdminTools\Admin\Helper\Select;

defined('_JEXEC') or die;
?>
<div class="akeeba-form-group">
    <label
           for="emailphpexceptions"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILPHPEXCEPTIONS'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILPHPEXCEPTIONS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILPHPEXCEPTIONS'); ?>
    </label>

    <input type="text" size="20" name="emailphpexceptions" value="<?php echo $this->escape($this->wafconfig['emailphpexceptions']); ?>">
</div>

<div class="akeeba-form-group">
	<label
		   for="saveusersignupip"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SAVEUSERSIGNUPIP'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SAVEUSERSIGNUPIP_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_SAVEUSERSIGNUPIP'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'saveusersignupip', $this->wafconfig['saveusersignupip']); ?>
</div>

<div class="akeeba-form-group">
	<label for="logbreaches"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LOGBREACHES'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LOGBREACHES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_LOGBREACHES'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'logbreaches', $this->wafconfig['logbreaches']); ?>
</div>

<div class="akeeba-form-group">
    <label for="logfile"
           rel="akeeba-sticky-tooltip"
           data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_LOGFILE'); ?>"
           data-content="<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_LOGFILE_TIP'); ?>"
    >
		<?php echo \JText::_('COM_ADMINTOOLS_CONFIGUREWAF_OPT_LOGFILE'); ?>
    </label>

	<?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'logfile', $this->wafconfig['logfile']); ?>
</div>

<div class="akeeba-form-group">
	<label for="iplookup"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_LABEL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_DESC'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_LABEL'); ?>
	</label>

	<div>
		<?php echo Select::httpschemes('iplookupscheme', array('class' => 'input-small'), $this->wafconfig['iplookupscheme']); ?>

		<input type="text" size="50" name="iplookup" value="<?php echo $this->escape($this->wafconfig['iplookup']); ?>"
			   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_IPLOOKUP_DESC'); ?>"/>
	</div>
</div>

<div class="akeeba-form-group">
	<label for="emailbreaches"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILBREACHES'); ?>
	</label>

    <input type="text" size="20" name="emailbreaches" value="<?php echo $this->escape($this->wafconfig['emailbreaches']); ?>">
</div>

<div class="akeeba-form-group">
	<label for="emailonadminlogin"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINLOGIN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINLOGIN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINLOGIN'); ?>
	</label>

		<input type="text" size="20" name="emailonadminlogin" value="<?php echo $this->escape($this->wafconfig['emailonadminlogin']); ?>" >
</div>

<div class="akeeba-form-group">
	<label for="emailonfailedadminlogin"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINFAILEDLOGIN'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINFAILEDLOGIN_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILADMINFAILEDLOGIN'); ?>
	</label>

    <input type="text" size="20" name="emailonfailedadminlogin" value="<?php echo $this->escape($this->wafconfig['emailonfailedadminlogin']); ?>">
</div>

<div class="akeeba-form-group">
	<label
		   for="reasons_nolog"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOLOG'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOLOG_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOLOG'); ?>
	</label>

    <?php
    echo Select::reasons('reasons_nolog[]', $this->wafconfig['reasons_nolog'], array(
                    'class'     => 'advancedSelect input-large',
                    'multiple'  => 'multiple',
                    'size'      => 5,
                    'hideEmpty' => true
            )
    )
    ?>
</div>

<div class="akeeba-form-group">
	<label
		   for="reasons_noemail"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOEMAIL'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOEMAIL_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_REASONS_NOEMAIL'); ?>
	</label>

    <?php
    echo Select::reasons('reasons_noemail[]', $this->wafconfig['reasons_noemail'], array(
                    'class'     => 'advancedSelect input-large',
                    'multiple'  => 'multiple',
                    'size'      => 5,
                    'hideEmpty' => true
            )
    )
    ?>
</div>

<div class="akeeba-form-group">
	<label
		   for="email_throttle"
		   rel="akeeba-sticky-tooltip"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILTHROTTLE'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILTHROTTLE_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_EMAILTHROTTLE'); ?>
	</label>

    <?php echo \JHtml::_('FEFHelper.select.booleanswitch', 'email_throttle', $this->wafconfig['email_throttle']); ?>
</div>
