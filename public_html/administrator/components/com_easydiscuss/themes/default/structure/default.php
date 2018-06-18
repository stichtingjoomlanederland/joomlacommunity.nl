<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-backend" data-ed-wrapper>
	<?php if (!$browse) { ?>
		<div class="app-alert o-alert o-alert--danger outdated-version">
			<div class="row-table">
				<div class="col-cell cell-tight">
					<i class="app-alert__icon fa fa-bolt"></i>
				</div>
				<div class="col-cell alert-message">
					<?php echo JText::_('COM_EASYDISCUSS_OUTDATED_VERSION');?>
				</div>
				<div class="col-cell cell-tight">
					<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=system&task=upgrade');?>" class="btn btn-danger"><?php echo JText::_('COM_EASYDISCUSS_UPDATE_NOW_BUTTON');?></a>
				</div>
			</div>
		</div>
	<?php } ?>

	<div class="app-master">
		<?php if (!$browse) { ?>
			<?php echo $sidebar; ?>
		<?php } ?>

		<div class="app-content front">
			<div class="wrapper clearfix clear accordion">

				<?php if (!$browse) { ?>
					<?php if ($title) { ?>
					<div class="app-content-head">
						<h2><?php echo $title;?></h2>
						<p><?php echo $desc;?></p>
					</div>
					<?php } ?>
				<?php } ?>

				<?php if ($message) { ?>
				<div class="discussNotice app-content__alert o-alert o-alert--<?php echo $message->type;?>">
					<?php echo $message->message;?>
				</div>
				<?php } ?>

				<div class="app-content-body">


					<?php echo $contents; ?>
				</div>

				<input type="hidden" class="easydiscuss-token" value="<?php echo ED::getToken();?>" data-ed-token />

				<input type="hidden" data-ed-ajax-url value="<?php echo $ajaxUrl;?>" />
			</div>
		</div>
	</div>
</div>
