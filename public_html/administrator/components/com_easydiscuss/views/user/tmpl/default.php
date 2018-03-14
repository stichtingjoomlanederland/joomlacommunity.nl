<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyDiscuss.ready(function($){
	$.Joomla( 'submitbutton' , function( action ){

		if( action == 'cancel' )
		{
			window.location.href	= 'index.php?option=com_easydiscuss&view=users';
			return;
		}

		$.Joomla( 'submitform' , [action] );
	});
});
</script>
<form name="adminForm" id="adminForm" action="index.php?option=com_easydiscuss&controller=user" method="post" enctype="multipart/form-data">

	<ul class="nav nav-tabs">
		<li class="active">
			<a href="#si-account" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_ACCOUNT' ); ?></a>
		</li>
		<li>
			<a href="#si-social" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_SOCIAL' ); ?></a>
		</li>
		<li>
			<a href="#si-location" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_LOCATION' ); ?></a>
		</li>
		<li>
			<a href="#si-badges" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_BADGES' ); ?></a>
		</li>
		<li>
			<a href="#si-history" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_HISTORY' ); ?></a>
		</li>
		<li>
			<a href="#si-site" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_USER_TAB_SITE' ); ?></a>
		</li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="si-account">
			<?php echo $this->loadTemplate( 'account' ); ?>
		</div>
		<div class="tab-pane" id="si-social">
			<?php echo $this->loadTemplate( 'social' ); ?>
		</div>
		<div class="tab-pane" id="si-location">
			<?php echo $this->loadTemplate( 'location' ); ?>
		</div>
		<div class="tab-pane" id="si-badges">
			<?php echo $this->loadTemplate( 'badges' ); ?>
		</div>
		<div class="tab-pane" id="si-history">
			<?php echo $this->loadTemplate( 'history' ); ?>
		</div>
		<div class="tab-pane" id="si-site">
			<?php echo $this->loadTemplate( 'site' ); ?>
		</div>
	</div>

<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="user" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="id" value="<?php echo $this->user->id;?>" />
</form>
