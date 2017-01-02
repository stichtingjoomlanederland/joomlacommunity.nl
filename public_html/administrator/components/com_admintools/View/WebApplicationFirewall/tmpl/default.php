<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this  \Akeeba\AdminTools\Admin\View\WebApplicationFirewall\Html */

// Protect from unauthorized access
defined('_JEXEC') or die;

$baseUri = JUri::base();

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/WebApplicationFirewall/plugin_warning');

?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<?php echo \JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_HTACCESSTIP'); ?>
</div>

<div class="akeeba-cpanel">

	<a href="index.php?option=com_admintools&view=ConfigureWAF" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/wafconfig-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFCONFIG'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFCONFIG'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=ExceptionsFromWAFs" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/wafexceptions-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFEXCEPTIONS'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFEXCEPTIONS'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=WAFBlacklistedRequests" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/ipbl-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFBLACKLISTS'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFBLACKLISTS'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=WhitelistedAddresses" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/ipwl-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPWL'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPWL'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=BlacklistedAddresses" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/ipbl-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPBL'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPBL'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=BadWords" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/badwords-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_BADWORDS'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_BADWORDS'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=GeographicBlocking" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/geoblock-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_GEOBLOCK'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_GEOBLOCK'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=SecurityExceptions" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/log-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_LOG'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_LOG'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=AutoBannedAddresses" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/ipautoban-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPAUTOBAN'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPAUTOBAN'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=IPAutoBanHistories" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/ipautoban-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPAUTOBANHISTORY'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_IPAUTOBANHISTORY'); ?><br/>
			</span>
	</a>

	<a href="index.php?option=com_admintools&view=WAFEmailTemplates" class="btn cpanel-icon">
		<img
				src="<?php echo $this->escape($baseUri); ?>components/com_admintools/media/images/waftemplates-32.png"
				class="at-icon" alt="<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFEMAILTEMPLATES'); ?>"/>
			<span class="title">
				<?php echo \JText::_('COM_ADMINTOOLS_TITLE_WAFEMAILTEMPLATES'); ?><br/>
			</span>
	</a>
</div>