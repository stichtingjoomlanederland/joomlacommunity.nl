<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');
$pendingCount = Komento::getModel( 'comments' )->getCount( 'all', 'all', array( 'published' => 2 ) );
$reportsCount = Komento::getModel( 'reports', true )->getTotal();
?>
<script type="text/javascript">
Komento.ready(function($){

	$('.container-nav').appendTo($('.header'));

	$('[data-sidebar-parent]').click(function() {
	    var parent = $(this).parent();

	    parent.toggleClass('open active');
	});

	$('.nav-sidebar-toggle').click(function(){
    	$('body').toggleClass('show-sidebar');
    });
});

</script>

<div class="sidebar col-cell">
	<ul class="sidebar-menu reset-ul">
		<li class="<?php echo $this->view == '' ? ' active' : '';?>" >
			<a href="<?php echo JRoute::_('index.php?option=com_komento');?>">
				<i class="fa fa-dashboard"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_DASHBOARD' ); ?></b>
			</a>
		</li>
		<li class="dropdown <?php echo $this->view == 'system' ? ' active' : '';?>">
			<a href="javascript:void(0);" class="dropdown-toggle" data-sidebar-parent>
				<i class="fa fa-cog"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_CONFIGURATION' ); ?></b>
			</a>
			<ul class="dropdown-menu">
				<li class="<?php echo $this->layout == 'database' ? ' active' : '';?>">
					<a href="<?php echo JRoute::_('index.php?option=com_komento&view=system&layout=database');?>">
						<b><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_DATABASE' ); ?></b>
					</a>
				</li>

				<li class="<?php echo $this->layout == 'profile' ? ' active' : '';?>">
					<a href="<?php echo JRoute::_('index.php?option=com_komento&view=system&layout=profile');?>">
						<b><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_PROFILE' ); ?></b>
					</a>
				</li>

				<li class="<?php echo $this->layout == 'schema' ? ' active' : '';?>">
					<a href="<?php echo JRoute::_('index.php?option=com_komento&view=system&layout=schema');?>">
						<b><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_SCHEMA' ); ?></b>
					</a>
				</li>

				<li class="<?php echo $this->layout == 'system' ? ' active' : '';?>">
					<a href="<?php echo JRoute::_('index.php?option=com_komento&view=system&layout=system');?>">
						<b><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_SYSTEM' ); ?></b>
					</a>
				</li>

				<?php if ($advance) { ?>
				<li class="<?php echo $this->layout == 'advance' ? ' active' : '';?>">
					<a href="<?php echo JRoute::_('index.php?option=com_komento&view=system&layout=advance');?>">
						<b><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ADVANCE' ); ?></b>
					</a>
				</li>
				<?php } ?>
			</ul>
		</li>

		<li class="<?php echo $this->view == 'integrations' ? ' active' : '';?>" >
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=integrations');?>">
				<i class="fa fa-plug"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_INTEGRATIONS' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'comments' || $this->view == 'comment' ? ' active' : '';?>" >
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=comments');?>">
				<i class="fa fa-comment"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_COMMENTS' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'pending' ? ' active' : '';?>">
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=pending');?>">
				<i class="fa fa-clock-o"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_PENDING' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'reports' ? ' active' : '';?>">
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=reports');?>">
				<i class="fa fa-warning"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_REPORTS' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'subscribers' ? ' active' : '';?>">
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=subscribers');?>">
				<i class="fa fa-leaf"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_SUBSCRIBERS' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'acl' ? ' active' : '';?>">
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=acl');?>">
				<i class="fa fa-users"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_ACL' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'migrators' ? ' active' : '';?>">
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=migrators');?>">
				<i class="fa fa-times"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_MIGRATORS' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'mailq' ? ' active' : '';?>">
			<a href="<?php echo JRoute::_('index.php?option=com_komento&view=mailq');?>">
				<i class="fa fa-envelope"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_MAIL_QUEUE' ); ?></b>
			</a>
		</li>

		<li class="<?php echo $this->view == 'documentation' ? ' active' : '';?>">
			<a href="http://stackideas.com/docs/komento.html">
				<i class="fa fa-life-bouy"></i>
				<b><?php echo JText::_( 'COM_KOMENTO_DOCUMENTATION' ); ?></b>
			</a>
		</li>
	</ul>
</div>


