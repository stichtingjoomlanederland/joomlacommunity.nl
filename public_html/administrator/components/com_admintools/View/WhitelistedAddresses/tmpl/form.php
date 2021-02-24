<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\View\WhitelistedAddresses\Html;
use Joomla\CMS\Language\Text;

/** @var Html $this */

defined('_JEXEC') || die;

?>
<div class="akeeba-block--info">
	<p><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_INTRO') ?></p>
	<ol>
		<li><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT1') ?></li>
		<li><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT2') ?></li>
		<li><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT3') ?></li>
		<li><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT4') ?></li>
		<li><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT5') ?></li>
		<li><?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT6') ?></li>
	</ol>

	<p>
		<?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_YOURIP') ?>
		<code><?php echo $this->escape($this->myIP) ?></code>
	</p>

</div>

<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal akeeba-panel">
	<div class="akeeba-container--50-50">
		<div>
			<div class="akeeba-form-group">
				<label for="ip">
					<?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP'); ?>
				</label>

				<input type="text" name="ip" id="ip" value="<?php echo $this->escape($this->item->ip); ?>" />
			</div>

			<div class="akeeba-form-group">
				<label for="description">
					<?php echo Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_DESCRIPTION'); ?>
				</label>

				<input type="text" name="description" id="description"
					   value="<?php echo $this->escape($this->item->description); ?>" />
			</div>

		</div>
	</div>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" value="com_admintools" />
		<input type="hidden" name="view" value="WhitelistedAddresses" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo (int) $this->item->id; ?>" />
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
	</div>
</form>
