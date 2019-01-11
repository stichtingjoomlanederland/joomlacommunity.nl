<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>600</width>
	<height>250</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",

		"{form}" : "[data-ed-video-form]",
		"{videoUrl}": "[data-ed-video-url]",
		"{message}": "[data-ed-message]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			var url = this.videoUrl().val();
			var contents = this.message().val();

			// Insert the video
			window.insertVideoCode(url, "<?php echo $caretPosition;?>", "<?php echo $element;?>", contents, "<?php echo $dialogRecipient; ?>");

			// Close the dialog
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_BBCODE_INSERT_VIDEO'); ?></title>
	<content>
		<p class="t-lg-mb--xl">
			<?php echo JText::_( 'COM_EASYDISCUSS_BBCODE_INSERT_VIDEO_DESC');?>
		</p>

		<ul class="ed-video-providers">
			<li class="video-youtube"><?php echo JText::_( 'COM_EASYDISCUSS_YOUTUBE' );?></li>
			<li class="video-vimeo"><?php echo JText::_('COM_EASYDISCUSS_VIMEO' );?></li>
			<li class="video-dailymotion"><?php echo JText::_('COM_EASYDISCUSS_DAILYMOTION' );?></li>
			<li class="video-google"><?php echo JText::_('COM_EASYDISCUSS_GOOGLE' );?></li>
			<li class="video-liveleak"><?php echo JText::_( 'COM_EASYDISCUSS_LIVELEAK' );?></li>
			<li class="video-metacafe"><?php echo JText::_( 'COM_EASYDISCUSS_METACAFE' );?></li>
			<li class="video-nicovideo"><?php echo JText::_( 'COM_EASYDISCUSS_NICOVIDEO' );?></li>
			<li class="video-smule"><?php echo JText::_('COM_EASYDISCUSS_SMULE');?></li>
		</ul>
		<form data-ed-video-form>
			<label for="videoURL">
				<strong><?php echo JText::_('COM_EASYDISCUSS_VIDEO_URL');?>:</strong>
			</label>
			<input type="text" id="videoURL" value="" class="form-control" data-ed-video-url />
			<input type="hidden" id="message" value="<?php echo ED::string()->escape($contents); ?>" data-ed-message />
		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_INSERT'); ?></button>
	</buttons>
</dialog>
