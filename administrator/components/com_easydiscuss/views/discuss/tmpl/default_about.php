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
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<div class="extra">
	<div class="extra-wrap">
		<h3 class="extra-title"><?php echo JText::_( 'COM_EASYDISCUSS_ABOUT_EASYDISCUSS' );?></h3>
		<div class="user-guide" style="display: block;">
			<div class="guide-wrap">
				<p><?php echo JText::_('COM_EASYDISCUSS_ABOUT_DESC');?></p>

				<hr />
				<h3 class="extra-title"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_SUPPORT');?></h3>
				<p><?php echo JText::_('COM_EASYDISCUSS_ABOUT_SUPPORT_DESC');?></p>
				<ul class="support-desc">
					<li style="background: none;border: 0;">
						<a href="http://stackideas.com/docs/easydiscuss" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_DOCS_SUPPORT');?></a>
					</li>
					<li style="background: none;border: 0;">
						<a href="http://stackideas.com/forums" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_FORUM_SUPPORT');?></a>
					</li>
					<li style="background: none;border: 0;">
						<a href="https://crm.stackideas.com" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_HELPDESK_SUPPORT');?></a>
					</li>
				</ul>

				<hr />
				<h3 class="extra-title"><?php echo JText::_('COM_EASYDISCUSS_ABOUT_PROFESSIONAL_SERVICES');?></h3>

				<p><?php echo JText::_('COM_EASYDISCUSS_ABOUT_PROFESSIONAL_SERVICES_DESC');?></p>

				<hr />

				<h3 class="extra-title"><?php echo JText::_( 'COM_EASYDISCUSS_LIKE_US_ON_FACEBOOK' );?></h3>

				<div class="fb-like" data-href="http://facebook.com/StackIdeas" data-send="false" data-layout="button_count" data-width="450" data-show-faces="false"></div>
				<hr />

				<h3 class="extra-title"><?php echo JText::_( 'COM_EASYDISCUSS_FOLLOW_US_ON_TWITTER' );?></h3>
				<a href="https://twitter.com/stackideas" class="twitter-follow-button" data-show-count="false">Follow @stackideas</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
		</div>
	</div>
</div>
