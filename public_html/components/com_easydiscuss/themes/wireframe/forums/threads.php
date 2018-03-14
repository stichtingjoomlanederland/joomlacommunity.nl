<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if (!empty($threads)) { ?>
	<?php foreach ($threads as $thread) { ?>
	<div class="ed-forum">
		<div class="ed-forum__hd">
			<div class="o-row">
				<div class="o-col-sm o-col--8">
					<div class="ed-forum__hd-title">
						<h2 class="ed-forum-item__title">
							<?php echo strtoupper(JText::_("COM_EASYDISCUSS_FORUMS_TOPICS")); ?>
						</h2>
					</div>
				</div>

				<div class="o-col-sm"></div>

				<div class="o-col-sm ed-forum-item__col-avatar center">
					<div class=""><?php echo JText::_('COM_EASYDISCUSS_FORUMS_POSTED_BY'); ?></div>
				</div>
				<div class="o-col-sm ed-forum-item__col-avatar center">
					<div class=""><?php echo JText::_('COM_EASYDISCUSS_FORUMS_LAST_REPLY'); ?></div>
				</div>
			</div>
		</div>
		<div class="ed-forum__bd">
			<?php echo $this->output('site/forums/item', array('thread' => $thread->posts)); ?>
		</div>
		<div class="ed-forum__ft">
		</div>
	</div>
	<?php } ?>

<?php } else { ?>
	<div class="ed-forum">
		<div class="ed-forum__hd">
			<div class="o-row">
				<?php echo JText::_('COM_EASYDISCUSS_FORUMS_EMPTY_THREAD');?>
			</div>
		</div>
	</div>
<?php } ?>