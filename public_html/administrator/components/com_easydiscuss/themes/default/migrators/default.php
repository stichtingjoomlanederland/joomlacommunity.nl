<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="<?php echo JRoute::_('index.php');?>" method="post" name="adminForm" id="adminForm" data-ed-migrator-form>

	<?php echo $this->output('admin/migrators/' . $type); ?>

	<?php echo $this->html('form.token'); ?>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="migrators" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="layout" value="<?php echo $type; ?>" />

</form>
