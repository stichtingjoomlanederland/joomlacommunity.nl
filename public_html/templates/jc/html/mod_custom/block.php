<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_custom
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$icon = htmlspecialchars($params->get('header_class'), ENT_COMPAT, 'UTF-8');
?>

<div class="block block-<?php echo $moduleclass_sfx ?>">
	<h3><i class="fa fa-<?php echo $icon; ?> " aria-hidden="true"></i> <?php echo $module->title; ?></h3>
	<?php echo $module->content; ?>
</div>