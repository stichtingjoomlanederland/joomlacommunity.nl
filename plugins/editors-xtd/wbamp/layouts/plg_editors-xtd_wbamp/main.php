<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

// no direct access
defined('_JEXEC') or die;

?>
<div class="wbl-theme-default wbamp-container wbamp-editor-content">
	<div class="wbl-theme-default wbamp-editor-msg-area">
	</div>
	<div class="wbamp-help-frame">
		<div class="wbamp-help-frame-loader-container">
			<div class="wbamp-help-frame-loader wbl-spinner-black" id="wbamp-editor-help-spinner"></div>
		</div>
		<iframe id="wbamp-help-frame" src="" class="wbamp-help-frame hide"></iframe>
	</div>

	<select id="wbamp-option-select" onchange="wblib.wbampeditor.showTab(this.value);">
		<?php
		foreach ($displayData['options'] as $key => $option)
		{
			echo '<option value="' . $key . '">' . $this->escape(JTExt::_($option['title'])) . "</option>\n";
		}
		?>
	</select>

	<div class="wbl-theme-default wbamp-editor-help-text">
		<?php echo JTExt::_('PLG_EDITORS_XTD_WBAMP_WINDOW_SUB_TITLE'); ?>
	</div>

	<div id="wbamp-editor-tabs-container" class="wbamp-editor-tabs-container">
		<?php
		$active = key($displayData['options']);
		foreach ($displayData['options'] as $key => $option)
		{
			echo ShlMvcLayout_Helper::render(
				'plg_editors-xtd_wbamp.tab',
				array(
					'current' => $active,
					'id' => $key,
					'params' => $option,
					'form' => $displayData['forms'][$key]
				),
				WBAMP_EDITOR_LAYOUTS_PATH
			);
		}
		?>
	</div>
</div>
