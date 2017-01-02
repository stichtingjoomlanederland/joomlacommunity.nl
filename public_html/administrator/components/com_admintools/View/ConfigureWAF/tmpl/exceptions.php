<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html    $this */
defined('_JEXEC') or die;

?>
<div class="control-group">
	<label class="control-label"
		   for="neverblockips"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_NEVERBLOCKIPS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_NEVERBLOCKIPS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_NEVERBLOCKIPS'); ?>
	</label>

	<div class="controls">
		<input class="input-xxlarge" type="text" size="50" name="neverblockips"
			   value="<?php echo $this->escape($this->wafconfig['neverblockips']); ?>"/>
	</div>
</div>

<div class="control-group">
	<label class="control-label"
		   for="whitelist_domains"
		   rel="popover"
		   data-original-title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_WHITELIST_DOMAINS'); ?>"
		   data-content="<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_WHITELIST_DOMAINS_TIP'); ?>">
		<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_WHITELIST_DOMAINS'); ?>
	</label>

	<div class="controls">
		<input type="text" class="input-large" name="whitelist_domains" id="whitelist_domains"
			   value="<?php echo $this->escape($this->wafconfig['whitelist_domains']); ?>">
	</div>
</div>
