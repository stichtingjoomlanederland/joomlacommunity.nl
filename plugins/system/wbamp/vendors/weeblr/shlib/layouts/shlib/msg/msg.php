<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2016
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.587
 */

/**
 * This layout displays a single message, obtained from the message manager
 *
 * $displayData elements:
 *
 * @param string $id   An optional unique id for the message container
 * @param array  $msgs The list of messages object
 *
 */

defined('_JEXEC') or die;

$class = empty(ShlMsg_Manager::$displayTypeClasses[$displayData['msg']->display_type]) ?
	ShlMsg_Manager::$displayTypeClasses[ShlMsg_Manager::DISPLAY_TYPE_INFO]
	: ShlMsg_Manager::$displayTypeClasses[$displayData['msg']->display_type];
?>

<!-- wbLib message -->
<div
	class="wbl-container-msg-one wbl-container-msg-one-<?php echo $class; ?>
	<?php if (!empty($displayData['msg']->body))
	{
		echo ' wbl-container-msg-one-toggle ';
	} ?>" id="<?php echo $displayData['msg']->uid; ?>"

	<?php if (!empty($displayData['msg']->body)) : ?>
		data-target="#wbl-msg-body-<?php echo $displayData['msg']->uid; ?>"
	<?php endif; ?>
	>

	<?php if ($displayData['msg']->action != ShlMsg_Manager::ACTION_CANNOT_CLOSE) : ?>
		<button type="button"
		        data-element-id="<?php echo $displayData['msg']->uid; ?>"
		        data-token="<?php echo JSession::getFormToken(); ?>"
		        data-scope="<?php echo $displayData['msg']->scope; ?>"
		        class="wbl-msg-button-close"
		        id="wbl-msg-button-close-<?php echo $displayData['msg']->uid; ?>">
			x
		</button>
	<?php endif; ?>

	<div class="wbl-msg-title">
		<span class="wbl-msg-title"><?php echo $displayData['msg']->title; ?></span>
	</div>
	<?php if (!empty($displayData['msg']->body)) : ?>
		<div class="wbl-msg-body" id="wbl-msg-body-<?php echo $displayData['msg']->uid; ?>">
			<span class="wbl-msg-body"><?php echo $displayData['msg']->body; ?></span>
		</div>
		<button type="button" class="wbl-msg-body-collapse-button wbl-collapse-button-center"></button>
	</button -->
	<?php endif; ?>
</div>
<!-- wbLib message -->
