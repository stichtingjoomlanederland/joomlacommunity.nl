<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\View\WhitelistedAddresses\Form;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var  Form $this */

if ($this->componentParams->getValue('ipwl', 0) == 1)
{
	return;
}

?>
<div class="akeeba-block--failure">
	<h3><?php echo Text::_('COM_ADMINTOOLS_WHITELISTEDADDRESSES_ERR_NOTENABLED_TITLE'); ?></h3>
	<p>
		<?php echo Text::_('COM_ADMINTOOLS_WHITELISTEDADDRESSES_ERR_NOTENABLED_BODY'); ?>
	</p>
</div>
