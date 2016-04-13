<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="parser" class="tab-pane">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
	                        <div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_SWITCH'); ?>
	                        </div>
	                        <div class="col-md-7">
	                        	<?php echo $this->html('form.boolean', 'cat_email_parser_switch', $category->getParam('cat_email_parser_switch', false));?>
							</div>
						</div>
						<div class="form-group">
	                        <div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_ADDRESS'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" value="<?php echo $category->getParam( 'cat_email_parser' );?>" name="cat_email_parser" class="form-control"/>
							</div>
						</div>
						<div class="form-group">
	                        <div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_CATEGORY_EMAIL_PARSER_PASSWORD'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input name="cat_email_parser_password" value="<?php echo $category->getParam( 'cat_email_parser_password' );?>" type="password" autocomplete="off" class="form-control"/>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>