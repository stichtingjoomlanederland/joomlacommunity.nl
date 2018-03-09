<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Html  $this */

if ($this->componentParams->getValue('ipbl', 0) == 1) return;

?>
<div class="akeeba-block--failure">
    <h3><?php echo \JText::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_NOTENABLED_TITLE'); ?></h3>
    <p>
        <?php echo \JText::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_NOTENABLED_BODY'); ?>
    </p>
</div>
