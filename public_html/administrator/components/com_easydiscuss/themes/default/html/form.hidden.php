<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $this->html('form.token'); ?>
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="<?php echo $controller;?>" />
<input type="hidden" name="view" value="<?php echo $view;?>" />
<input type="hidden" name="task" value="<?php echo $task;?>" data-ed-form-task />
<input type="hidden" name="boxchecked" value="0" data-ed-table-boxchecked />