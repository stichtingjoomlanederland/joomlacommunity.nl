<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Language\Text;

/** @var Akeeba\AdminTools\Admin\View\ConfigureWAF\Html $this */
defined('_JEXEC') || die;

?>
<div class="akeeba-form-group">
	<label
			for="neverblockips"
			rel="akeeba-sticky-tooltip"
			data-original-title="<?php echo Text::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_NEVERBLOCKIPS'); ?>"
			data-content="<?php echo Text::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_NEVERBLOCKIPS_TIP'); ?>">
		<?php echo Text::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_LBL_NEVERBLOCKIPS'); ?>
	</label>

	<input type="text" size="50" name="neverblockips"
		   value="<?php echo $this->escape($this->wafconfig['neverblockips']); ?>" />
</div>

<div class="akeeba-form-group">
	<label
			for="whitelist_domains"
			rel="akeeba-sticky-tooltip"
			data-original-title="<?php echo Text::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_WHITELIST_DOMAINS'); ?>"
			data-content="<?php echo Text::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_WHITELIST_DOMAINS_TIP'); ?>">
		<?php echo Text::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_OPT_WHITELIST_DOMAINS'); ?>
	</label>

	<input type="text" name="whitelist_domains" id="whitelist_domains"
		   value="<?php echo $this->escape($this->wafconfig['whitelist_domains']); ?>">
</div>
