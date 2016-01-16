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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_MIGRATORS_VBULLETIN_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>


	<div class="row-fluid ">
		<div class="span6">
			<div class="widget accordion-group">
				<div class="whead accordion-heading">
					<a href="javascript:void(0);">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_DETAILS' ); ?></h6>
					<!-- <i class="icon-chevron-down"></i> -->
					</a>
				</div>
				<form name="vBulletin" action="index.php" method="post">
<!-- 					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<fieldset>
									<div class="span5 form-row-label">
										<label>
											<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_DRIVER' ); ?>
										</label>
									</div>
									<div class="span7"
										rel="ed-popover"
										data-placement="left"
										data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_DRIVER' ); ?>"
										data-content="<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_DRIVER_DESC'); ?>"
									>
										<input type="text" name="migrator_vBulletin_driver" style="width: 50px;" value="" />

									</div>
							</fieldset>
						</div>
					</div>
					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<fieldset>
							<div class="span5 form-row-label">
								<label>
									<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_HOST' ); ?>
								</label>
							</div>
							<div class="span7"
								rel="ed-popover"
								data-placement="left"
								data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_HOST' ); ?>"
								data-content="<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_HOST_DESC'); ?>"
							>
								<input type="text" name="migrator_vBulletin_host" style="width: 150px;" value="" />

							</div>
						</fieldset>
						</div>
					</div>

					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<fieldset>
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_USER' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_USER' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_USER_DESC'); ?>"
								>
									<input type="text" name="migrator_vBulletin_user" style="width: 150px;" value="" />

								</div>
							</fieldset>
						</div>
					</div>

					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<fieldset>
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_PASSWORD' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_PASSWORD' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_PASSWORD_DESC'); ?>"
								>
									<input type="password" name="migrator_vBulletin_password" style="width: 150px;" value="" />

								</div>
							</fieldset>
						</div>
					</div>
					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<fieldset>
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_NAME' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_NAME' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_NAME_DESC'); ?>"
								>
									<input type="text" name="migrator_vBulletin_name" style="width: 150px;" value="" />

								</div>

							</fieldset>
						</div>
					</div> -->
					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<fieldset>
								<div class="span5 form-row-label">
									<label>
										<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_PREFIX' ); ?>
									</label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETN_DB_PREFIX' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_VBULLETN_DB_PREFIX_DESC'); ?>"
								>
									<input type="text" name="migrator_vBulletin_prefix" style="width: 50px;" value="" />

								</div>
							</fieldset>
						</div>
					</div>

				<div class="si-form-row">
				<input type="submit" class="btn btn-success social facebook pull-right" value="<?php echo JText::_( 'COM_EASYDISCUSS_VBULLETIN_NEXT_STEP' );?>" />
				<input type="hidden" name="task" value="save" />
				<input type="hidden" name="controller" value="vbulletin" />
				<input type="hidden" name="option" value="com_easydiscuss" />
				</div>
				</form>
			</div>
		</div>
	</div>




