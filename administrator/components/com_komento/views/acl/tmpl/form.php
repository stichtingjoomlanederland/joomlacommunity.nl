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

defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">

<?php if (Komento::joomlaVersion() >= 1.6) : ?>
	Joomla.submitbutton = function(action) {
		submitbutton(action);
	}
<?php endif; ?>

Komento.ready(function($) {

	window.submitbutton = function(action)
	{
		if (action == 'enableall') {
			enableAll();
		} else if (action == 'disableall') {
			disableAll();
		} else {
			submitform(action);
		}
	}

	window.enableAll = function()
	{
		$('.option-enable').trigger('click');
	}

	window.disableAll = function()
	{
		$('.option-disable').trigger('click');
	}
});

function changeComponent(component) {
	document.adminForm.component.value = component;
	document.adminForm.submit();
}
</script>

<form action="index.php?option=com_komento&view=acl" method="post" name="adminForm" id="adminForm">
<?php echo $this->loadTemplate('acl'); ?>
<input type="hidden" name="target_component" value="<?php echo $this->escape($this->component); ?>" />
<input type="hidden" name="target_id" value="<?php echo $this->escape($this->id); ?>" />
<input type="hidden" name="target_type" value="<?php echo $this->escape($this->type); ?>" />
<input type="hidden" name="layout" value="form" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="c" value="acl" />
<input type="hidden" name="task" value="change" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
