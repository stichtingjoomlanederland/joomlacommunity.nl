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
<div id="ed" class="ed-mod ed-mod-toolbar <?php echo $lib->getModuleWrapperClass();?>" data-ed-wrapper>
<?php echo $toolbar->render($modToolbar); ?>
</div>

<?php if (!$edPageExist) { ?>
	<div><?php echo ED::scripts()->getScripts(); ?></div>
<?php } ?>