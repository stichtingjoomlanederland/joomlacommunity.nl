<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Html  $this */

if ($this->componentParams->getValue('ipbl', 0) == 1) return;

?>
<div class="akeeba-block--failure">
	<h3><?php echo Text::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_NOTENABLED_TITLE'); ?></h3>
	<p>
		<?php echo Text::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_NOTENABLED_BODY'); ?>
	</p>
</div>
