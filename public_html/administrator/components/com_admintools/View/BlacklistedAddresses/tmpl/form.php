<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Html $this */

defined('_JEXEC') or die;

?>
<div class="akeeba-block--info">
	<p><?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP_INTRO') ?></p>
	<ol>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP_OPT1') ?></li>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP_OPT2') ?></li>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP_OPT3') ?></li>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP_OPT4') ?></li>
	</ol>

	<p>
		<?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_YOURIP') ?>
		<code><?php echo $this->myIP ?></code>
	</p>

</div>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal akeeba-panel">
    <div class="akeeba-container--50-50">
        <div>
            <div class="akeeba-form-group">
                <label for="ip">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP'); ?>
                </label>

                <input type="text" name="ip" id="ip" value="<?php echo $this->escape($this->item->ip); ?>" />
            </div>

            <div class="akeeba-form-group">
                <label for="description">
					<?php echo JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_DESCRIPTION'); ?>
                </label>

                <input type="text" name="description" id="description" value="<?php echo $this->escape($this->item->description); ?>" />
            </div>

        </div>
    </div>

    <div class="akeeba-hidden-fields-container">
        <input type="hidden" name="option" value="com_admintools" />
        <input type="hidden" name="view" value="BlacklistedAddresses" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="id" id="id" value="<?php echo (int)$this->item->id; ?>" />
        <input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
    </div>
</form>
