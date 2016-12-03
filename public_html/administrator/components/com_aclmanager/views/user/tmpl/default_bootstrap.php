<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$id = $this->state->get('filter.user_id');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>

<div id="aclmanager" class="row-fluid bootstrap">
	<form action="<?php echo JRoute::_('index.php?option=com_aclmanager&view=user&id='.$id);?>" method="post" name="adminForm" id="adminForm">
		<?php if(($this->params->get('show_legend',1)) || ($this->params->get('show_info',1)) || ($this->params->get('show_assigned',1))): ?>
		<div class="span9">
		<?php else: ?>
		<div class="span12">
		<?php endif; ?>

		<!-- Begin filter -->
		<?php echo $this->loadTemplate('filter_bootstrap'); ?>
		<!-- End filter -->

		<table id="persistent" class="adminlist aclmanager table table-striped table-bordered">
			<!-- Begin header -->
			<?php echo $this->loadTemplate('table_header'); ?>
			<!-- End header -->
		</table>

		<table id="global-aclmanager" class="adminlist aclmanager table table-striped table-bordered">
			<!-- Begin header -->
			<?php echo $this->loadTemplate('table_header'); ?>
			<!-- End header -->

			<!-- Begin table rows -->
			<?php echo $this->loadTemplate('table_permissions'); ?>
			<!-- End table rows -->
		</table>
		<?php echo $this->pagination->getListFooter(); ?>
		</div>

		<?php if(($this->params->get('show_legend',1)) || ($this->params->get('show_info',1)) || ($this->params->get('show_assigned',1))): ?>
		<div class="span3">
			<?php echo $this->loadTemplate('sidebar_bootstrap'); ?>
		</div>
		<?php endif; ?>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" value="<?php echo($id);?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>

<script type="text/javascript">
	(function($){
		var $win = $(window)
		  , $nav = $('#persistent')
		  , navTop = $('#persistent').length && $('#persistent').offset().top - 110, isFixed = 0
		processScroll()
		$win.on('scroll', processScroll)
		function processScroll()
		{
			var i, scrollTop = $win.scrollTop()
			if (scrollTop >= navTop && !isFixed)
			{
				isFixed = 1
				$nav.addClass('show')
			} else if (scrollTop <= navTop && isFixed)
			{
				isFixed = 0
				$nav.removeClass('show')
			}
		}
	})(jQuery);
</script>

<div class="copyright">
	<p><?php echo JText::_('COM_ACLMANAGER_COPYRIGHT'); ?> &copy; 2011 - <?php echo date('Y');?>. <?php echo JText::_('COM_ACLMANAGER_DEVELOPED_BY');?>. <a href="http://www.aclmanager.net" target="_blank">www.aclmanager.net</a></p>
</div>