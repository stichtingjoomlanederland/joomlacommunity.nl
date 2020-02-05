<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

?>
<section class="akeeba-panel">
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal">
		<div>
			<div class="akeeba-form-group">
				<label for="comment">
					<?php echo JText::_('COM_ADMINTOOLS_SCANS_EDIT_COMMENT'); ?>
				</label>

				<?php echo JEditor::getInstance($this->container->platform->getConfig()->get('editor', 'tinymce'))->display('comment',  $this->item->comment , '100%', '350', '50', '20', false); ?>
			</div>

		</div>

		<div class="akeeba-hidden-fields-container">
			<input type="hidden" name="option" value="com_admintools" />
			<input type="hidden" name="view" value="Scans" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" id="id" value="<?php echo (int)$this->item->id; ?>" />
			<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />

		</div>
	</form>
</section>
