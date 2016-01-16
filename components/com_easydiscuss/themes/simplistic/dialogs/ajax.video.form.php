<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<p><?php echo JText::_( 'COM_EASYDISCUSS_BBCODE_INSERT_VIDEO_DESC' );?></p>
<ul class="discuss-video-providers">
	<li class="video-youtube"><?php echo JText::_( 'COM_EASYDISCUSS_YOUTUBE' );?></li>
	<li class="video-vimeo"><?php echo JText::_('COM_EASYDISCUSS_VIMEO' );?></li>
	<li class="video-dailymotion"><?php echo JText::_('COM_EASYDISCUSS_DAILYMOTION' );?></li>
	<li class="video-google"><?php echo JText::_('COM_EASYDISCUSS_GOOGLE' );?></li>
	<li class="video-liveleak"><?php echo JText::_( 'COM_EASYDISCUSS_LIVELEAK' );?></li>
	<li class="video-metacafe"><?php echo JText::_( 'COM_EASYDISCUSS_METACAFE' );?></li>
	<li class="video-nicovideo"><?php echo JText::_( 'COM_EASYDISCUSS_NICOVIDEO' );?></li>
	<li class="video-yahoo"><?php echo JText::_( 'COM_EASYDISCUSS_YAHOO' );?></li>
</ul>
<form id="frmVideo" name="frmVideo" class="si_form">
	<label for="videoURL"><strong><?php echo JText::_( 'COM_EASYDISCUSS_VIDEO_URL' );?>:</strong></label>
	<input type="text" id="videoURL" value="" class="full-width" />
</form>
