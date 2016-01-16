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
<script type="text/javascript">
Komento.require().script('migrator.progress', 'migrator.actions', 'migrator.common').done(function($) {
	$(document).ready(function() {
		$('.migratorProgress').implement('Komento.Controller.Migrator.Progress');
		$('.tab').implement('Komento.Controller.Migrator.Actions');
		$('.migratorTable').implement('Komento.Controller.Migrator.Common');
	});
});
</script>
<div id="config-document">
	<div id="page-easyblog" class="tab">
		<?php echo $this->loadTemplate('easyblog_joomla'); ?>
	</div>
	<div id="page-zoo" class="tab">
		<?php echo $this->loadTemplate('zoo_joomla'); ?>
	</div>
	<div id="page-k2" class="tab">
		<?php echo $this->loadTemplate('k2_joomla'); ?>
	</div>
	<div id="page-slicomments" class="tab">
		<?php echo $this->loadTemplate('slicomments_joomla'); ?>
	</div>
	<div id="page-jcomments" class="tab">
		<?php echo $this->loadTemplate('jcomments_joomla'); ?>
	</div>
	<div id="page-jacomment" class="tab">
		<?php echo $this->loadTemplate('jacomment_joomla'); ?>
	</div>
	<div id="page-cjcomment" class="tab">
		<?php echo $this->loadTemplate('cjcomment_joomla'); ?>
	</div>
	<div id="page-rscomments" class="tab">
		<?php echo $this->loadTemplate('rscomments_joomla'); ?>
	</div>
	<div id="page-custom" class="tab">
		<?php echo $this->loadTemplate('custom_joomla'); ?>
	</div>
</div>
