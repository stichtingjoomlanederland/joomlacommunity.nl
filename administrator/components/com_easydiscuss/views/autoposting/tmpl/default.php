<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_DESC' );?>
		</p>
	</div>
</div>
<div class="row-fluid">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a data-target="#facebook" data-foundry-toggle="collapse" href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEEBOOK');?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div class="accordion-body collapse in" id="facebook">
				<div class="wbody">
					<div class="row-fluid">
						<div class="span3">
							<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/facebook_setup.png" class="mt-10" />
						</div>
						<div class="span9">
							<h3 class="head-3"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEEBOOK');?></h3>
							<p>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEBOOK_DESC' );?>
							</p>

							<div class="mt-20">
							<?php if( !$this->facebookSetup ){ ?>
								<a href="index.php?option=com_easydiscuss&view=autoposting&layout=facebook&step=1" class="btn btn-success social facebook pull-right"><?php echo JText::_( 'COM_EASYDISCUSS_SETUP' );?></a>
							<?php } else { ?>
								<a href="index.php?option=com_easydiscuss&view=autoposting&layout=form&type=facebook" class="btn btn-info social facebook pull-right"><?php echo JText::_( 'COM_EASYDISCUSS_CONFIGURE' );?></a>
							<?php } ?>
							</div>
						</div>
					</div>

					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a data-target="#twitter" data-foundry-toggle="collapse" href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER');?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div class="accordion-body collapse in" id="twitter">
				<div class="wbody">
					<div class="row-fluid">
						<div class="span3">
							<img src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/twitter_setup.png" style="mt-10" />
						</div>
						<div class="span9">
							<h3 class="head-3"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER');?></h3>
							<p>
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_DESC' );?>
							</p>
							<div class="mt-20">
							<?php if( !$this->twitterSetup ){ ?>
								<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=twitter&step=1' );?>" class="btn btn-success social twitter pull-right"><?php echo JText::_( 'COM_EASYDISCUSS_SETUP' );?></a>
							<?php } else { ?>
								<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&view=autoposting&layout=form&type=twitter' );?>" class="btn btn-info social twitter pull-right"><?php echo JText::_( 'COM_EASYDISCUSS_CONFIGURE' );?></a>
							<?php } ?>
							</div>
						</div>
					</div>


					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="task" value="" />
</form>
