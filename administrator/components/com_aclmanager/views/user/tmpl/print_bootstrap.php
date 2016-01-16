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
	<div class="printheader row-fluid">
		<div class="span4">
			<h1><?php echo JText::_('COM_ACLMANAGER_SUBMENU_USER');?>: <?php if ($id):?><?php echo($user->name);?><?php endif;?></h1>
			<span class="website"><?php echo(preg_replace("/^(http:\/\/|https:\/\/)/", "", JURI::root()));?></span>
		</div>
		<div class="span8">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_ACLMANAGER_SIDEBAR_LEGEND'); ?></legend>
				<ul class="unstyled">
					<li class="rule">
						<span class="icon allowed"><img src="components/com_aclmanager/assets/images/tick.png"/></span>
						<span class="legend">
							<?php echo JText::_('COM_ACLMANAGER_SIDEBAR_ALLOWED'); ?>
						</span>
					</li>
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
	<div class="row-fluid bootstrap">
		<div class="span12">
			<table id="global-aclmanager" class="adminlist aclmanager table table-striped table-bordered">
				<!-- Begin header -->
				<?php echo $this->loadTemplate('table_header'); ?>
				<!-- End header -->
				<!-- Begin table rows -->
				<?php echo $this->loadTemplate('table_permissions'); ?>
				<!-- End table rows -->
			</table>
		</div>
	</div>
</div>
<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. www.aclmanager.net</p>
</div>

<script language="javascript">
	javascript:window.print();
</script>