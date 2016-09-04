<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="wbl-theme-default ">

	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('PLG_EDITORS_XTD_WBAMP_ONLY_AMP_LABEL'); ?>
		</div>
		<div class="controls">
			<button class="btn" type="button"
			        onclick="wblib.wbampeditor.insertTag('start_amp_show')"><?php echo JText::_('PLG_EDITORS_XTD_WBAMP_INSERT_START_LABEL'); ?></button>
			<button class="btn btn-danger" type="button"
			        onclick="wblib.wbampeditor.insertTag('end_amp_show')"><?php echo JText::_('PLG_EDITORS_XTD_WBAMP_INSERT_END_LABEL'); ?></button>
		</div>
	</div>


	<div class="control-group">
		<div class="control-label">
			<?php echo JText::_('PLG_EDITORS_XTD_WBAMP_NOT_ON_AMP_LABEL'); ?>
		</div>
		<div class="controls">
			<button class="btn" type="button"
			        onclick="wblib.wbampeditor.insertTag('start_amp_hide')"><?php echo JText::_('PLG_EDITORS_XTD_WBAMP_INSERT_START_LABEL'); ?></button>
			<button class="btn btn-danger" type="button"
			        onclick="wblib.wbampeditor.insertTag('end_amp_hide')"><?php echo JText::_('PLG_EDITORS_XTD_WBAMP_INSERT_END_LABEL'); ?></button>
		</div>
	</div>

</div>
