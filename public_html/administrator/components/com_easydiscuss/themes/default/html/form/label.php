<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<label data-uid="<?php echo $uniqueId;?>" for="<?php echo $id;?>">
	<?php echo JText::_($label);?>
</label>
<i class="fa fa-question-circle o-form-label__help-icon" 
	data-ed-provide="popover"
	data-title="<?php echo JText::_($label);?>" 
	data-content="<?php echo JText::_($desc);?>"
	data-placement="bottom"
	data-html="true"
	>
</i>