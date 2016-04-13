<?php
/**
* @package      EasyDiscuss
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
<div class="app-content-head">
	<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIAL_GOOGLE_TITLE' );?></h2>
	<div>
		<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIAL_GOOGLE_DESC' );?>
	</div>
</div>

<div class="app-content-body">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<div class="panel-head">
					<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_TITLE' ); ?></h6>
					<i class="icon-chevron-down"></i>
					</a>
				</div>

				<div id="option01" class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<label>
									<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE' ); ?>
								</label>
							</div>
							<div class="col-md-7"
								rel="ed-popover"
								data-placement="left"
								data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE' ); ?>"
								data-content="<?php echo JText::_('COM_EASYDISCUSS_SETTINGS_SOCIALSHARE_GOOGLE_PLUS_ONE_ENABLE_DESC'); ?>"
							>
								<?php echo $this->renderCheckbox( 'integration_googleone' , $this->config->get( 'integration_googleone' ) );?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

