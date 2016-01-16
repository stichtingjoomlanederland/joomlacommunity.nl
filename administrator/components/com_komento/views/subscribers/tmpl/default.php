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
Komento.ready(function($) {
	$.Joomla('submitbutton', function(action) {
		$.Joomla('submitform', [action]);
	});
});
</script>
<style>
.comment-cell p {margin:0;}
</style>
<form action="index.php?option=com_komento&view=reports" method="post" name="adminForm" id="adminForm">
<?php echo $this->loadTemplate('subscribers'); ?>
<?php echo JHTML::_( 'form.token' ); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_komento" />
<input type="hidden" name="view" value="subscribers" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="subscribers" />
<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->order); ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
