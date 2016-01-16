<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div id="config-document">
	<div id="page-main" class="tab">
		<?php echo $this->loadTemplate('main_joomla');?>
	</div>
	<div id="page-antispam" class="tab">
		<?php echo $this->loadTemplate('antispam_joomla');?>
	</div>
	<div id="page-layout" class="tab">
		<?php echo $this->loadTemplate('layout_joomla');?>
	</div>
	<div id="page-notifications" class="tab">
		<?php echo $this->loadTemplate('notifications_joomla');?>
	</div>
	<div id="page-activities" class="tab">
		<?php echo $this->loadTemplate('activities_joomla');?>
	</div>
	<!-- START: Pro Version Only -->
	<div id="page-attachment" class="tab">
		<?php echo $this->loadTemplate('attachment_joomla');?>
	</div>
	<!-- END: Pro Version Only -->
	<div id="page-advance" class="tab">
		<?php echo $this->loadTemplate('advance_joomla');?>
	</div>
</div>
<div class="clr"></div>
