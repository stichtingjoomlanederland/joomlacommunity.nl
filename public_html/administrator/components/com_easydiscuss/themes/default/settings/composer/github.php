<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// github integration.
$oauth = ED::Table('Oauth');
$oauth->load(array('type' => 'github'));
$associated = $oauth->id && $oauth->access_token ? true : false;

$returnUri = JRoute::_('index.php?option=com_easydiscuss&view=settings&layout=composer&active=github', false);
$returnUri = base64_encode($returnUri);
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_ED_GITHUB_INTEGRATIONS', '', '/docs/easydiscuss/administrators/integrations/github-gist-integration'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_ED_GITHUB_INTEGRATIONS_INFO'); ?>

				<div class="o-form-horizontal">
					<?php echo $this->html('settings.textbox', 'composer_github_client_id', 'COM_ED_GITHUB_CLIENT_ID'); ?>
					<?php echo $this->html('settings.textbox', 'composer_github_client_secret', 'COM_ED_GITHUB_CLIENT_SECRET_KEY'); ?>

					<div class="o-form-group">

						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_ED_GITHUB_SIGN_IN'); ?>
						</div>

						<div class="col-md-7">
							<?php if ($associated) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=github&return=' . $returnUri);?>" class="btn btn-danger">
								<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
							</a>
							<?php } else { ?>
							<a href="javascript:void(0)" class="o-btn o-btn--default-o" data-gist-login>
								<i class="fab fa-github"></i>&nbsp; <?php echo JText::_('COM_ED_GITHUB_SIGN_IN_WITH_GITHUB');?>
							</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>