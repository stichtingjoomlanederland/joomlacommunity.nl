<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
?>

<!-- Help screen -->
<div class="pwt-content">

	<!-- Wrapper -->
	<div class="pwt-wrapper">

		<!-- PWT branding -->
		<div class="pwt-section pwt-section--border-bottom">
			<div class="pwt-flag-object pwt-flag-object--spaced">
				<div class="pwt-flag-object__aside">
					<?php echo JHtml::_('image', 'com_pwtimage/PWT-image.png', 'PWT Image', array('width' => 120), true); ?>
				</div>
				<div class="pwt-flag-object__body">
					<p class="pwt-heading"><?php echo JText::_('COM_PWTIMAGE_DASHBOARD_ABOUT_HEADER'); ?></p>
					<p><?php echo Text::_('COM_PWTIMAGE_DASHBOARD_ABOUT_DESC'); ?></p>
					<p><a href="https://extensions.perfectwebteam.com/pwt-image"><?php echo Text::_('COM_PWTIMAGE_ABOUT_PWTIMAGE_WEBSITE'); ?></a></p>
				</div>
			</div>
		</div><!-- .pwt-branding -->

		<!-- FAQ -->
		<div class="pwt-section">
			<p class="pwt-heading"><?php echo Text::_('COM_PWTIMAGE_HOW_TO_UPLOAD_IMAGE_LABEL'); ?></p>
			<p><?php echo Text::_('COM_PWTIMAGE_HOW_TO_UPLOAD_IMAGE_DESCRIPTION'); ?></p>
		</div>

		<div class="pwt-section">
			<p class="pwt-heading"><?php echo Text::_('COM_PWTIMAGE_HOW_TO_RESIZE_IMAGE_LABEL'); ?></p>
			<p><?php echo Text::_('COM_PWTIMAGE_HOW_TO_RESIZE_IMAGE_DESCRIPTION'); ?></p>
		</div>

		<div class="pwt-section">
			<p class="pwt-heading"><?php echo Text::_('COM_PWTIMAGE_HOW_TO_CUSTOMIZE_FILENAME_LABEL'); ?></p>
			<p><?php echo Text::_('COM_PWTIMAGE_HOW_TO_CUSTOMIZE_FILENAME_DESCRIPTION'); ?></p>
		</div>

		<div class="pwt-section pwt-section--border-top">
			<p><a href="https://extensions.perfectwebteam.com/pwt-image/documentation"><?php echo Text::_('COM_PWTIMAGE_DOCUMENTATION_LINK'); ?></a></p>
		</div>

	</div>

</div><!-- .pwt-content -->
