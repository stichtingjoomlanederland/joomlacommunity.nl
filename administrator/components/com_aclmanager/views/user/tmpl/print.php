<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$id = $this->state->get('filter.user_id');

// Check if user is Super User
$user = JFactory::getUser($id);
$su = $user->authorise('core.admin');
?>

<div id="print">
	<div class="printheader">
		<div class="logo">
			<h1><?php echo JText::_('COM_ACLMANAGER_SUBMENU_USER');?>: <?php if ($id):?><?php echo($user->name);?><?php endif;?></h1>
			<span class="website"><?php echo(preg_replace("/^(http:\/\/|https:\/\/)/", "", JURI::root()));?></span>
		</div>
		<div class="legendinfo">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_LEGEND'); ?></legend>
				<ul>
					<li class="rule">
						<span class="icon allowed"><img src="components/com_aclmanager/assets/images/tick.png"/></span>
						<span class="legend">
							<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_ALLOWED'); ?>
						</span>
					</li>
				</ul>
				<ul>
					<li class="rule">
						<span class="icon denied"><img src="components/com_aclmanager/assets/images/publish_x.png"/></span>
						<span class="legend">
							<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_DENIED'); ?>
						</span>
					</li>
				</ul>
			</fieldset>
		</div>
	</div>
	<div class="width-100 fltrt">
		<table id="global-aclmanager" class="adminlist aclmanager">
			<!-- Begin header -->
			<?php echo $this->loadTemplate('table_header'); ?>
			<!-- End header -->
			<!-- Begin table rows -->
			<?php echo $this->loadTemplate('table_permissions'); ?>
			<!-- End table rows -->
		</table>
	</div>
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. www.aclmanager.net</p>
</div>

<script language="javascript">
	javascript:window.print();
</script>