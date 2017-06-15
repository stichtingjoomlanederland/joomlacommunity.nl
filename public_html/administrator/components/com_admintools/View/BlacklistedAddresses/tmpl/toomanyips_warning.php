<?php
/**
 * @package   AdminTools
 * @copyright 2010-2017 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Form $this */

$db              = $this->getContainer()->db;
$query           = $db->getQuery(true)
                      ->select('COUNT(*)')
                      ->from($db->qn('#__admintools_ipblock'));
$totalBlockedIPs = $db->setQuery($query)->loadResult();

if ($totalBlockedIPs < 50) return;

?>
<div class="alert alert-warning">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<h3>
		<span class="icon icon-warning"></span>
		<?php echo \JText::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_TOOMANY_TITLE'); ?>
	</h3>
	<p>
		<?php echo \JText::sprintf('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_TOOMANY_BODY', 'https://www.akeebabackup.com/documentation/admin-tools/waf-ip-blacklist.html#do-not-overdo-it-with-ip-blacklisting'); ?>
	</p>
</div>