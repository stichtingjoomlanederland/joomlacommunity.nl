<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<ul class="rsepro_categories<?php echo $suffix; ?>">
<?php require JModuleHelper::getLayoutPath('mod_rseventspro_categories', $params->get('layout', 'default').'_items'); ?>
</ul>