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
<script type="text/javascript">
EasyDiscuss( function($){

	window.showPageForm		= function(){
		$( '#page-form' ).toggle();
	}
});
</script>
<div class="row-fluid">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a data-target="#facebook" data-foundry-toggle="collapse" href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_FACEEBOOK');?> - <?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_STEP_2'); ?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>
				<div class="accordion-body collapse in" id="facebook">
					<div class="wbody">
					<form name="facebook" action="index.php" method="post">
						<ul class="list-instruction unstyled pa-15">
							<li>
								<?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_STEP_2_SIGN_IN_WITH_FB'); ?>
								<?php if( $this->associated ){ ?>
								<div style="margin-top:5px">
									<?php echo JText::_( 'Completed' );?>
								</div>
								<?php } else { ?>
								<div style="margin-top:5px">
									<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=facebook');?>"><img src="<?php echo JURI::root();?>media/com_easydiscuss/images/facebook_signon.png" /></a>
								</div>
								<?php } ?>
							</li>
							<li>
								<div class="clearfix">
									<input type="checkbox" value="1" id="post-as-page" name="main_autopost_facebook_page" onchange="showPageForm();" />
									<label for="post-as-page"><?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_STEP_2_POST_AS_PAGE' );?></label>
								</div>
								<div id="page-form" class="mini-form" style="display:none;">
									<label for="page-id"><?php echo JText::_( 'COM_EASYDISCUSS_FB_AUTOPOST_STEP_2_PAGE_ID' );?>:</label>
									<input type="text" id="page-id" name="main_autopost_facebook_page_id" value="<?php echo $this->config->get( 'main_autopost_facebook_page_id' );?>" class="input" style="width:200px;margin-right:10px" />
									<span class="small"><?php echo JText::_( 'Separate each page id with a comma. E.g: 123456789,123456780' );?></span>
								</div>
							</li>
						</ul>
						<div class="si-form-row">
						<input type="submit" class="btn btn-success social facebook pull-right" value="<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_COMPLETE' );?>" />
						<input type="hidden" name="step" value="2" />
						<input type="hidden" name="task" value="save" />
						<input type="hidden" name="layout" value="facebook" />
						<input type="hidden" name="controller" value="autoposting" />
						<input type="hidden" name="option" value="com_easydiscuss" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
