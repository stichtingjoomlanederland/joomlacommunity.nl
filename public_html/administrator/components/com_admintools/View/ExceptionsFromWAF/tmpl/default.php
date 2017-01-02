<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var $this \Akeeba\AdminTools\Admin\View\ExceptionsFromWAF\Form */

?>

<div id="admintools-whatsthis" class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">×</a>

	<div id="admintools-whatsthis-info">
		<p><?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_WHATSTHIS_LBLA') ?></p>
		<ul>
			<li><?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_WHATSTHIS_LBLB') ?></li>
			<li><?php echo JText::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_WHATSTHIS_LBLC') ?></li>
		</ul>
	</div>
</div>

<?php
// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/WebApplicationFirewall/plugin_warning');

echo $this->getRenderedForm();