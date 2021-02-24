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
<style type="text/css">
#ed.si-theme--light {
	--si-toolbar-bg: #ffffff;
	--si-toolbar-text: #292929;
}
#ed.si-theme--dark {
	--si-toolbar-bg: #292929;
	--si-toolbar-text: #ffffff;
}
#ed .ed-toolbar .o-nav__item .ed-toolbar__link {
	
}
/*#ed .ed-toolbar { background-color: <?php echo $this->config->get('layout_toolbarcolor', '#333333');?>;}*/
#xed .ed-toolbar,
#xed .ed-toolbar__item--search,
#xed .ed-toolbar__item--search select {border-color: <?php echo $this->config->get('layout_toolbarbordercolor', '#333333');?>; }
#xed .ed-toolbar .o-nav__item .ed-toolbar__link { color: <?php echo $this->config->get('layout_toolbartextcolor', '#FFFFFF')?>; }
#xed .ed-toolbar .o-nav__item.is-active .ed-toolbar__link:not(.no-active-state),
#xed .ed-toolbar .o-nav__item .ed-toolbar__link:not(.no-active-state):hover, 
#xed .ed-toolbar .o-nav__item .ed-toolbar__link:not(.no-active-state):focus,
#xed .ed-toolbar .o-nav__item .ed-toolbar__link:not(.no-active-state):active { background-color: <?php echo $this->config->get('layout_toolbaractivecolor', '#5c5c5c')?>; }
#xed .ed-toolbar__link.has-composer {background-color: <?php echo $this->config->get('layout_toolbarcomposerbackgroundcolor', '#428bca')?>; }
</style>

