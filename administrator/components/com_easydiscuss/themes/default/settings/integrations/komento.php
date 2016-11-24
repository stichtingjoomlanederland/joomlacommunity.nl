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
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_KOMENTO_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div>
				    <img width="64" align="left" src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/integrations/komento.png" style="margin-left: 20px;margin-right:25px; float: left;">
				    
				    <div class="small" style="overflow:hidden;">
				        <?php echo JText::_('COM_EASYDISCUSS_KOMENTO_INFO');?><br /><br />
				        <a target="_blank" class="btn btn-primary btn-sm t-lg-mb--lg" href="https://stackideas.com/komento"><?php echo JText::_('COM_EASYDISCUSS_LEARN_MORE_ABOUT_KOMENTO'); ?> &rarr;</a>
				    </div>
				</div>

				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-6 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_KOMENTO_DISPLAY_COMMENTS_IN_PROFILE'); ?>
						</div>

						<div class="col-md-6">
							<?php echo $this->html('form.boolean', 'integrations_komento_profile', $this->config->get('integrations_komento_profile')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">	
	</div>
</div>
