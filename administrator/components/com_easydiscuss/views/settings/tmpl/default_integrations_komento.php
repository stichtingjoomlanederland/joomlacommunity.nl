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
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_KOMENTO_INTEGRATIONS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_KOMENTO_INTEGRATIONS_DESC' );?>
		</p>
		<a href="http://stackideas.com/komento.html" class="btn btn-success"><?php echo JText::_( 'COM_EASYDISCUSS_LEARN_MORE_ABOUT_KOMENTO' ); ?></a>
	</div>
</div>

<div class="row-fluid">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#komento-general">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_KOMENTO_INTEGRATIONS_GENERAL' ); ?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="komento-general" class="accordion-body in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span6 form-row-label">
							<label><?php echo JText::_( 'COM_EASYDISCUSS_KOMENTO_DISPLAY_COMMENTS_IN_PROFILE' );?></label>
						</div>

						<div class="span6" rel="ed-popover" data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_KOMENTO_DISPLAY_COMMENTS_IN_PROFILE' , true );?>"
							data-original-content="<?php echo JText::_( 'COM_EASYDISCUSS_KOMENTO_DISPLAY_COMMENTS_IN_PROFILE_DESC' , true );?>">
							<?php echo $this->renderCheckbox( 'integrations_komento_profile' , $this->config->get( 'integrations_komento_profile' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
	</div>
</div>
