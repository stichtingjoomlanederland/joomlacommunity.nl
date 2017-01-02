<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\AdminTools\Admin\View\WhitelistedAddresses\Form $this */

defined('_JEXEC') or die;

$ip = htmlspecialchars($_SERVER['REMOTE_ADDR']);
if ((strpos($ip, '::') === 0) && (strstr($ip, '.') !== false))
{
	$ip = substr($ip, strrpos($ip, ':') + 1);
}
?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">×</a>

	<p><?php echo JText::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_INTRO') ?></p>
	<ol>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT1') ?></li>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT2') ?></li>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT3') ?></li>
		<li><?php echo JText::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP_OPT4') ?></li>
	</ol>

	<p>
		<?php echo JText::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_YOURIP') ?>
		<code><?php echo $this->escape($this->myIP) ?></code>
	</p>

</div>

<?php echo $this->getRenderedForm()?>
