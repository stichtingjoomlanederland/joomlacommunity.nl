<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access'); ?>

<div id="config-document">
	<div id="page-database" class="tab">
		<?php echo $this->loadTemplate('database_joomla');?>
	</div>
	<div id="page-profile" class="tab">
		<?php echo $this->loadTemplate('profile_joomla');?>
	</div>
	<div id="page-schema" class="tab">
		<?php echo $this->loadTemplate('schema_joomla');?>
	</div>
	<div id="page-system" class="tab">
		<?php echo $this->loadTemplate('system_joomla');?>
	</div>
	<div id="page-advance" class="tab">
		<?php echo $this->loadTemplate('advance_joomla');?>
	</div>
</div>
<div class="clr"></div>
