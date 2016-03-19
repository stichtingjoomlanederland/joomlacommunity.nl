<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  AkeebaViewUpdates  $this */

$script = <<< JS

	;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
	// due to missing trailing semicolon and/or newline in their code.

	akeeba.jQuery(document).ready(function($){
		document.forms.updateForm.submit();
	});
JS;

$app      = JFactory::getApplication();
$document = method_exists($app, 'getDocument') ? $app->getDocument() : JFactory::getDocument();
$document->addScriptDeclaration($script);

?>

<div class="hero-unit">
	<h2><?php echo JText::_('COM_AKEEBA_UPDATE_LBL_INSTALLING'); ?></h2>
	<div class="progress progress-striped active">
		<div class="bar" style="width: 75%;"></div>
	</div>
</div>

<form name="updateForm" action="index.php" method="post" class="form form-horizontal">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="update" />
	<input type="hidden" name="task" value="install" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />
</form>