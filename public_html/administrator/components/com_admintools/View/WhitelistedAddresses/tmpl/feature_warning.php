<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\WhitelistedAddresses\Form  $this */

if ($this->componentParams->getValue('ipwl', 0) == 1) return;

?>
<div class="akeeba-block--failure">
    <h3><?php echo \JText::_('COM_ADMINTOOLS_WHITELISTEDADDRESSES_ERR_NOTENABLED_TITLE'); ?></h3>
    <p>
        <?php echo \JText::_('COM_ADMINTOOLS_WHITELISTEDADDRESSES_ERR_NOTENABLED_BODY'); ?>
    </p>
</div>
