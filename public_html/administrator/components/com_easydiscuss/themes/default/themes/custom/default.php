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
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<div class="row">
		<div class="col-lg-12">
			<?php echo $editor->display('contents', $contents, '100%', '450px', 80, 30, false, null, null, null, array('syntax' => 'css', 'filter' => 'raw')); ?>
		</div>
	</div>
		
	<?php echo $this->html('form.action', 'themes', '', 'saveCustomCss'); ?>
</form>