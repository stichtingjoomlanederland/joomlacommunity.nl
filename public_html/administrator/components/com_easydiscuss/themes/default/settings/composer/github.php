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
			<?php echo $this->html('panel.head', 'COM_ED_GITHUB_INTEGRATIONS'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_ED_GITHUB_INTEGRATIONS_INFO'); ?>

				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_GITHUB_CLIENT_ID'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'composer_github_client_id', $this->config->get('composer_github_client_id')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_GITHUB_CLIENT_SECRET_KEY'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'composer_github_client_secret', $this->config->get('composer_github_client_secret')); ?>
						</div>
					</div>
					<div class="form-group">

						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_ED_GITHUB_SIGN_IN'); ?>
						</div>

						<div class="col-md-7">
							<?php if ($associated) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=github&return=' . $returnUri);?>" class="btn btn-danger">
								<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
							</a>
							<?php } else { ?>
							<a href="javascript:void(0)" class="btn btn-primary" data-gist-login>
								<i class="fa fa-star"></i>&nbsp; <?php echo JText::_('COM_ED_GITHUB_SIGN_IN_WITH_GITHUB');?>
							</a>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'GitHub Oauth App', 'In this section, you will learn how to create the OAuth app from GitHub that will be used as the app authentication'); ?>

			<div class="panel-body">
				In GitHub V3 Rest API, it is now required to pass in authentication token in order to create new gist from EasyDiscuss. In order to get this token, we will first need to create an OAuth app so that we can retrieve the app Client ID and Client Secret Key that will be used during the app authentication. To do this, please follow the steps below. <br />

				<ul style="list-style-type: decimal;">
					<li>Login to <a href="https://github.com" target="_blank">github.com</a></li>
					<li>From the toolbar, click on your avatar image and click on 'Settings'.</li>
					<li>In the settings page, click on 'Developer Settings' and once you are at the Developer Settings page, click on OAuth Apps tab from the left menu.</li>
					<li>Now, you should see the 'New OAuth App' button. Click on this button to create your new OAuth app.</li>
					<li>
						Fill in all the input fields.<br>
						<b>Application Name:</b> Give a name to your application. <i>E.g. EasyDiscuss Gist</i><br>
						<b>Homepage URL*:</b> Enter your website URL here. <i>E.g. https://yourwebsite.com</i><br>
						<b>Application Description:</b> Give description to your app. <i>E.g. For Gist Usage</i><br>
						<b>Authorization callback URL*:</b> Enter <b>https://yourwebsite.com/index.php?option=com_easydiscuss</b> as your callback url. <br> Remember to replace https://yourwebsite.com with your actual website url.<br> Now, click on 'Register application' button to complete your app creation.<br>
					</li>
					<li>Once you've created your OAuth app, you will be redirect to your newly created app page. Copy the <b>Client ID</b> and <b>Client Secret</b> and insert these details into this form and click 'Save' from EasyDiscuss setting page.</li>
				</ul>

				Once you've saved your Client ID and Client Secret, now click on 'Sign in with Github' button from this form and follow the popup window instructions to complete your authentication process. You are then ready to use Gist in EasyDiscuss at the composer page.
			</div>
		</div>
	</div>
</div>