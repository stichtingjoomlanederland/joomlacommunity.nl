<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<ul id="rsepro-categories-module" class="rsepro_categories<?php echo $suffix; ?>">
	<?php require JModuleHelper::getLayoutPath('mod_rseventspro_categories', $params->get('layout', 'default').($columns == 1 ? '_items' : '_columns')); ?>
</ul>
<div class="clearfix"></div>