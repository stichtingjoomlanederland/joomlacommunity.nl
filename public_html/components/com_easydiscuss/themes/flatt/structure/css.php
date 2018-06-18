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
<style type="text/css">
#ed .ed-toolbar { background-color: <?php echo $this->config->get('layout_toolbarcolor', '#475a64');?>;}
#ed .ed-toolbar,
#ed .ed-toolbar__item--search,
#ed .ed-toolbar__item--search select {border-color: <?php echo $this->config->get('layout_toolbarbordercolor', '#475a64');?>; }
#ed .ed-toolbar .o-nav__item .ed-toolbar__link { color: <?php echo $this->config->get('layout_toolbartextcolor', '#ffffff')?>; }
#ed .ed-toolbar .o-nav__item.is-active .ed-toolbar__link:not(.no-active-state),
#ed .ed-toolbar .o-nav__item .ed-toolbar__link:not(.no-active-state):hover, 
#ed .ed-toolbar .o-nav__item .ed-toolbar__link:not(.no-active-state):focus,
#ed .ed-toolbar .o-nav__item .ed-toolbar__link:not(.no-active-state):active { background-color: <?php echo $this->config->get('layout_toolbaractivecolor', '#596c78')?>; }
#ed .ed-toolbar__link.has-composer {background-color: <?php echo $this->config->get('layout_toolbarcomposerbackgroundcolor', '#428bca')?>; }
</style>

