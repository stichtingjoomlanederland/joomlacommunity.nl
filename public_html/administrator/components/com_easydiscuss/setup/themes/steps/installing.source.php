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

// Let's try to detect if there are any files in the /packages/ folder.
$packages = JFolder::files(ED_PACKAGES , '.' , false , false , array('.svn', 'CVS', '.DS_Store', '__MACOSX' ,'index.html'));

$db = JFactory::getDBO();
$tables = $db->getTableList();
$jConfig = JFactory::getConfig();
$table = $jConfig->get('dbprefix') . 'discuss_configs';
$key = '';

if (in_array($table, $tables)) {
	$query = 'SELECT ' . $db->quoteName('params') . ' FROM ' . $db->quoteName('#__discuss_configs');
	$query .= ' WHERE ' . $db->quoteName('name') . '=' . $db->Quote('config');

	$db->setQuery($query);
	$raw = $db->loadResult();

	$registry = new JRegistry($raw);
	$key = $registry->get('main_apikey', '');
}
?>
<script type="text/javascript">
$(document).ready(function() {

	$('[data-source-type]').on('change', function() {
		var type 	= $( this ).val();

		// Show API key form.
		$( '[data-source-' + type + ']' ).show();

		$('[data-source-method]').removeClass('active');
		$(this).parents('[data-source-method]').addClass('active');

		if (type == 'network') {
			$( '[data-source-directory]' ).hide();
		} else {
			$('[data-source-network]').hide();
		}
	});

	$('[data-installation-submit]').on('click', function() {

		var selected = $('input[name=method]:checked').val(),
			loading = $('[data-installation-loading]'),
			submit = $('[data-installation-submit]'),
			apiKey = $('[data-api-key]').val(),
			errorMessage = $('[data-api-errors-message]'),
			error = $('[data-api-errors]'),
			multiple = $('[data-api-multiple]'),
			multipleMessage = $('[data-api-multiple-output]'),
			form = $('[data-installation-form]');

		var licenses = $('[data-licenses]');
		var licensePlaceholder = $('[data-licenses-placeholder]');

		// Check for license key first.
		if (selected == 'network') {

			// Hide submit button
			submit.addClass('hide');

			// Show loading
			loading.removeClass('hide');

			// Validate api key
			$.ajax({
				type: 'POST',
				url: '<?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&ajax=1&controller=license&task=verify',
				data: {
					key: apiKey
				}
			}).done(function(result) {

				if (result.state == 400) {

					// Hide the loading
					loading.addClass('hide');

					// Show the submit
					submit.removeClass('hide');

					// Set the error message
					errorMessage.html(result.message);
					error.removeClass('hide');

					return false;
				}

				if (result.state == 201) {

					// Hide error messages if there are shown
					error.addClass('hide');

					// Display multiple key result
					multiple.removeClass('hide');
					multipleMessage.html(result.html);

					// Display the button again.
					submit.removeClass('hide');

					// Change the submit buttons behavior.
					submit.on('click', function() {
						form.submit();
					});

					// Hide the loading
					loading.addClass('hide');

					return false;
				}

				if (result.state == 200) {

					// Hide the loading
					loading.addClass('hide');
					submit.removeClass('hide');


					// If there are multiple licenses, we need to request them to submit
					if (result.licenses.length > 1) {
						licenses.removeClass('hide');
						licensePlaceholder.append(result.html);

						submit.on('click', function() {
							form.submit();
						});
						return;
					}

					// If the user only has 1 license, just submit this immediately.
					licensePlaceholder.append(result.html);
					form.submit();
				}
			});
		}

		if (selected == 'directory') {

			// lets check if user selected any package or not.
			var package = $('select[name=package]').val();

			if (package == "") {
				alert('Please select a package to proceed.');
				return;
			}

			$('[data-installation-form]').submit();
		}
	});
});
</script>

<form action="index.php" method="post" name="installation" data-installation-form>
	<p class="section-desc">
		<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_DESC');?>
	</p>

	<div class="hide alert alert-danger" data-source-errors data-api-errors>
		<p data-api-errors-message style="margin-bottom: 15px;"><?php echo JText::_( 'COM_EASYDISCUSS_INSTALLATION_METHOD_API_KEY_INVALID', true ); ?></p>
		<a href="https://stackideas.com/forums" class="btn btn-danger" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_CONTACT_SUPPORT');?></a>
	</div>

	<br>

	<div class="installation-methods">
		<?php if (!ED_BETA) { ?>
		<div class="installation-method active" data-source-method>
			<div class="radio">
				<input type="radio" name="method" value="network" id="network" data-source-type checked="checked"/>
				<label for="network">
					<h4><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_VIA_NETWORK');?> <span class="label label-info small"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_RECOMMENDED');?></span></h4>
					<div><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_VIA_NETWORK_DESC');?></div>
					<div data-source-network>
						<?php if (ED_KEY) { ?>
							<input type="text" value="<?php echo ED_KEY;?>" name="apikey" class="input input-xlarge hidden" data-api-key />
						<?php } else { ?>
						<div class="form-inline" style="margin-top: 20px;">
							<div>
								<p>
									<b><?php echo JText::_( 'COM_EASYDISCUSS_INSTALLATION_METHOD_API_KEY' );?></b>
									&nbsp;
									<small><a href="https://stackideas.com/docs/easydiscuss/administrators/welcome/obtaining-api-key" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_RETRIEVE_API_KEY');?></a></small>
								</p>
							</div>
							<div class="row-table">
								<div class="col-cell">
									<div class="input-loader">
										<input type="text" value="<?php echo $key;?>" name="apikey" class="input input-xlarge" data-api-key />
									</div>
								</div>
							</div>
						</div>
						<?php } ?>

						<div class="form-inline hide" data-licenses>
							<hr style="border-color: #ccc;">
							<div>
								<p><b><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_SELECT_LICENSE');?></b></p>
								<p><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_SELECT_LICENSE_INFO');?></p>
								<div data-licenses-placeholder></div>
							</div>
						</div>
					</div>
				</label>
			</div>
		</div>
		<?php } ?>

		<div class="installation-method" data-source-method>
			<div class="radio">
				<input type="radio" name="method" value="directory" id="directory" data-source-type <?php echo ED_BETA ? ' checked="checked"' : '';?>/>
				<label for="directory">
					<h4><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_VIA_DIRECTORY');?></h4>
					<p><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_VIA_DIRECTORY_DESC');?>:</p>
					<div data-source-directory style="<?php echo !ED_BETA ? 'display: none;' : '';?>margin-top: 20px;">

						<div class="installation-directory-path" style="background:#fff;">
							<?php echo ED_PACKAGES; ?>/
						</div>

						<?php if (empty($packages)) { ?>
						<div class="text-error">
							<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_NO_PACKAGES');?>
						</div>
						<?php } else { ?>
						<div class="form-inline row-table">
							<div class="col-cell cell-label">
								<b><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_SELECT_PACKAGE'); ?></b>
							</div>
							<div class="col-cell">
								<select name="package" autocompleted="off">
									<option value="" selected="selected"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_METHOD_SELECT_A_PACKAGE');?></option>
									<?php foreach ($packages as $package) { ?>
									<option value="<?php echo $package; ?>"><?php echo $package; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<?php } ?>
					</div>
				</label>
			</div>
		</div>

	</div>

	<div class="alert alert-warning" data-api-multiple style="display: none;">
		<div><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MULTIPLE_LICENSE_FOUND'); ?></div>
		<div class="mt-10" data-api-multiple-output></div>
	</div>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="update" value="<?php echo $update;?>" />
</form>
