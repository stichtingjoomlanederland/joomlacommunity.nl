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

/** @var AkeebaModelUpdates $model */
$model = $this->getModel();
$message1 = $model->getState('message');
$message2 = $model->getState('extmessage');

$token = JFactory::getSession()->getFormToken();

$script = <<< JS

	;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
	// due to missing trailing semicolon and/or newline in their code.

	function akeeba_cleanup_complete()
	{
		(function($) {
			$('#akeeba_cleanup').hide();
		})(akeeba.jQuery)
	}

	akeeba.jQuery(document).ready(function($){
		// TODO: Cleanup
	});
JS;

$app      = JFactory::getApplication();
$document = method_exists($app, 'getDocument') ? $app->getDocument() : JFactory::getDocument();
$document->addScriptDeclaration($script);

?>

<div class="hero-unit" id="akeeba_cleanup">
	<h2><?php echo JText::_('COM_AKEEBA_UPDATE_LBL_CLEANUP'); ?></h2>
	<div class="progress progress-striped active">
		<div class="bar" style="width: 100%;"></div>
	</div>
</div>

<div id="akeeba_message">
<?php if ($message1): ?>
	<div id="akeeba_message_installer">
		<?php echo JText::_($message1); ?>
	</div>
<?php endif; ?>
<?php if ($message2): ?>
	<div id="akeeba_message_extension">
		<?php echo $message2; ?>
	</div>
<?php endif; ?>
</div>
<iframe style="width: 0px; height: 0px; border: none;" frameborder="0" marginheight="0" marginwidth="0" height="0"
		width="0" onload="akeeba_cleanup_complete();"
		src="index.php?option=com_akeeba&view=update&task=cleanup&format=raw<?php echo $token ?>=1"></iframe>
