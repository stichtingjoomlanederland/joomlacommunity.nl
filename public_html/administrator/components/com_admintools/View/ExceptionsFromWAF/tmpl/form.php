<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\View\ExceptionsFromWAF\Html;
use Joomla\CMS\Language\Text;

/** @var $this Html */

defined('_JEXEC') || die;
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form--horizontal akeeba-panel">
	<div class="akeeba-container--66-33">
		<div>
			<div class="akeeba-form-group">
				<label for="foption">
					<?php echo Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION'); ?>
				</label>

				<input type="text" name="foption" id="foption"
					   value="<?php echo $this->escape($this->item->option); ?>" />
				<p>
					<?php echo Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_OPTION_TIP') ?>
				</p>
			</div>

			<div class="akeeba-form-group">
				<label for="fview">
					<?php echo Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW'); ?>
				</label>

				<input type="text" name="fview" id="fview" value="<?php echo $this->escape($this->item->view); ?>" />

				<p>
					<?php echo Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_VIEW_TIP') ?>
				</p>
			</div>

			<div class="akeeba-form-group">
				<label for="fquery">
					<?php echo Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY'); ?>
				</label>

				<input type="text" name="fquery" id="fquery" value="<?php echo $this->escape($this->item->query); ?>" />

				<p>
					<?php echo Text::_('COM_ADMINTOOLS_LBL_EXCEPTIONSFROMWAF_QUERY_TIP') ?>
				</p>
			</div>
		</div>
	</div>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" value="com_admintools" />
		<input type="hidden" name="view" value="ExceptionsFromWAF" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="id" id="id" value="<?php echo (int) $this->item->id; ?>" />
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
	</div>
</form>
