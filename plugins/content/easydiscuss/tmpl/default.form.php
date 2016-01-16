<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyDiscuss.ready(function(){
	discuss.composer.init("<?php echo $composer->classname ?>");
});
</script>

<div class="discuss-user-reply" >
	<div class="row-fluid">
		<a name="respond" id="respond"></a>

		<legend class="discuss-component-title"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_YOUR_RESPONSE'); ?></legend>
		<hr class="mv-5" style="margin-bottom: 20px;" />

		<?php echo $composer->getComposer(); ?>
	</div>
</div>