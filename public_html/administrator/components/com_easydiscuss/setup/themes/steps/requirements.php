<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$gd = function_exists('gd_info');
$curl = is_callable('curl_init');

############################################
## MySQL info
############################################
$db = JFactory::getDBO();
$mysqlVersion = $db->getVersion();

############################################
## PHP info
############################################
$phpVersion = phpversion();
$uploadLimit = ini_get('upload_max_filesize');
$memoryLimit = ini_get('memory_limit');
$postSize = ini_get('post_max_size');
$magicQuotes = get_magic_quotes_gpc() && JVERSION > 3;

if (stripos($memoryLimit, 'G') !== false) {

	list($memoryLimit) = explode('G', $memoryLimit);

	$memoryLimit = $memoryLimit * 1024;
}

// if (stripos('G', $memoryLimit))
$postSize = 4;
$hasErrors = false;

if (!$gd || !$curl || $memoryLimit < 64 || $magicQuotes) {
	$hasErrors 	= true;
}

##########################################
## Paths
##########################################
$files = array();

$files['admin']	= new stdClass();
$files['admin']->path 	= JPATH_ROOT . '/administrator/components';

$files['site'] = new stdClass();
$files['site']->path = JPATH_ROOT . '/components';

$files['tmp'] = new stdClass();
$files['tmp']->path = JPATH_ROOT . '/tmp';

$files['media']	= new stdClass();
$files['media']->path = JPATH_ROOT . '/media';

$files['user'] = new stdClass();
$files['user']->path = JPATH_ROOT . '/plugins/user';

$files['module'] = new stdClass();
$files['module']->path = JPATH_ROOT . '/modules';

##########################################
## Debugging
##########################################
$posixExists = function_exists('posix_getpwuid');

if ($posixExists) {
	$owners = array();
}


##########################################
## Determine states
##########################################
foreach ($files as $file) {

	// The only proper way to test this is to not use is_writable
	$contents = "<body></body>";
	$state = JFile::write($file->path . '/tmp.html', $contents);

	// Initialize this to false by default
	$file->writable = false;

	if ($state) {
		JFile::delete($file->path . '/tmp.html');

		$file->writable 	= true;
	}

	if (!$file->writable) {
		$hasErrors 		= true;
	}

	if ($posixExists) {
		$owner = posix_getpwuid( fileowner( $file->path ) );
		$group = posix_getpwuid( filegroup( $file->path ) );

		$file->owner = $owner[ 'name' ];
		$file->group = $group[ 'name' ];
		$file->permissions = substr( decoct( fileperms( $file->path ) ) , 1 );
	}
}

?>
<script type="text/javascript">
jQuery(document).ready(function(){

	$ = jQuery;

	$('[data-installation-submit]').on('click', function() {

		<?php if ($hasErrors) { ?>
			$('[data-requirements-error]').show();
		<?php } else { ?>
			$('[data-installation-form]').submit();
		<?php } ?>
	});

	// Retry button
	$('[data-installation-reload]').on('click', function() {
		window.location.href = window.location;
	});

	<?php if ($hasErrors) { ?>
		$('[data-installation-submit]')
			.addClass('hide');

		$('[data-installation-retry]')
			.removeClass('hide');

		$('[data-installation-retry]').on('click', function(){

			// Hide the retry button
			$(this).addClass('hide');

			// Show the loading button
			$('[data-installation-loading]')
				.removeClass('hide');

			$('[data-installation-form-nav-active]').val('');
			$('[data-installation-form-nav]').submit();
		});
	<?php } ?>

});
</script>
<form name="installation" method="post" data-installation-form>

<p><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_DESC'); ?></p>

<p><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_DESC_2');?></p>

<?php if( !$hasErrors ){ ?>
<hr />
<p><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_MET');?></p>
<?php } ?>

<div class="alert alert-danger" data-requirements-error style="display: none;">
	<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_NOT_MET' );?>
</div>

<div class="requirements-table" data-system-requirements>
	<table class="table">
		<thead>
			<tr>
				<td width="40%">
					<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_SETTINGS');?>
				</td>
				<td class="text-center" width="30%">
					<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_RECOMMENDED');?>
				</td>
				<td class="text-center" width="30%">
					<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_TECHNICAL_REQUIREMENTS_CURRENT');?>
				</td>
			</tr>
		</thead>

		<tbody>
			<tr class="<?php echo version_compare($phpVersion, '5.3.10') == -1 ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP');?></span> PHP Version
						<i class="ies-help" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP_VERSION_TIPS');?>" data-toggle="tooltip" data-placement="bottom"></i>

						<?php if(version_compare($phpVersion , '5.3.10') == -1 ){ ?>
						<a href="http://docs.stackideas.com/administrators/welcome/getting_started" class="pull-right btn btn-es-danger btn-mini"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_FIX_THIS');?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					5.3.10 +
				</td>
				<td class="text-center text-<?php echo version_compare( $phpVersion , '5.3.10' ) == -1 ? 'error' : 'success';?>">
					<?php echo $phpVersion;?>
				</td>
			</tr>
			<tr class="<?php echo !$gd ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP');?></span> GD Library
						<i class="ies-help" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP_GD_TIPS');?>" data-toggle="tooltip" data-placement="bottom"></i>

						<?php if( !$gd ){ ?>
						<a href="http://docs.stackideas.com/administrators/setup/gd_library" target="_blank" class="pull-right btn btn-es-danger btn-mini"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_FIX_THIS');?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php if( $gd ){ ?>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php } else { ?>
				<td class="text-center text-error">
					<i class="icon-cancel-2"></i>
				</td>
				<?php } ?>
			</tr>

			<tr class="<?php echo !$curl ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP');?></span> CURL Library

						<?php if (!$curl) { ?>
						<a href="http://docs.stackideas.com/administrators/setup/curl" target="_blank" class="btn btn-primary btn-xs"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_FIX_THIS');?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php if( $curl ){ ?>
				<td class="text-center text-success">
					<i class="icon-checkmark"></i>
				</td>
				<?php } else { ?>
				<td class="text-center text-error">
					<i class="icon-cancel-2"></i>
				</td>
				<?php } ?>
			</tr>
			<tr class="<?php echo $magicQuotes ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span class="label label-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP');?></span> Magic Quotes GPC

						<?php if ($magicQuotes) { ?>
						<a href="https://stackideas.com/docs/easydiscuss/administrators/setup/magic_quotes" target="_blank" class="btn btn-primary btn-xs"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_FIX_THIS');?></a>
						<?php } ?>
					</div>
				</td>
				<td class="text-center text-success">
					<?php echo JText::_('Disabled');?>
				</td>
				<td class="text-center text-<?php echo $magicQuotes ? 'error' : 'success';?>">
					<?php if (!$magicQuotes) { ?>
						<?php echo JText::_('COM_EASYDISCUSS_SETUP_DISABLED');?>
					<?php } else { ?>
						<?php echo JText::_('COM_EASYDISCUSS_SETUP_ENABLED');?>
					<?php } ?>
				</td>
			</tr>
			<tr class="<?php echo $memoryLimit < 64 ? 'error' : '';?>">
				<td>
					<span class="label label-info"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PHP');?></span> memory_limit
				</td>
				<td class="text-center text-success">
					64 <?php echo JText::_('M');?>
				</td>
				<td class="text-center text-<?php echo $memoryLimit < 64 ? 'error' : 'success';?>">
					<?php echo $memoryLimit; ?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="label label-inverse"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MYSQL' );?></span> MySQL Version
					<i class="ies-help" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_MYSQL_VERSION_TIPS' );?>" data-toggle="tooltip" data-placement="bottom"></i>
				</td>
				<td class="text-center text-success">
					5.0.4
				</td>
				<td class="text-center text-<?php echo !$mysqlVersion || version_compare( $mysqlVersion , '5.0.4' ) == -1 ? 'error' : 'success'; ?>">
					<?php echo !$mysqlVersion ? 'N/A' : $mysqlVersion;?>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="table table-striped mt-20 stats">
		<thead>
			<tr>
				<td width="75%">
					<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_DIRECTORY'); ?>
				</td>
				<td class="text-center" width="25%">
					<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_STATE'); ?>
				</td>
			</tr>
		</thead>

		<tbody>
			<?php foreach ($files as $file) { ?>
			<tr class="<?php echo !$file->writable ? 'error' : '';?>">
				<td>
					<div class="clearfix">
						<span><?php echo $file->path;?></span>

						<?php if( !$file->writable ){ ?>
						<a href="javascript:void(0);" class="btn btn-es-inverse btn-mini pull-right" data-permissions-info><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_INFO' ); ?></a>
						<a href="javascript:void(0);" class="btn btn-es-danger btn-mini pull-right mr-5"><?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_HOW_TO_FIX' ); ?></a>
						<?php } ?>
					</div>
				</td>

				<?php if ($file->writable) { ?>
				<td class="text-center text-success">
					<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PERMISSIONS_WRITABLE' );?>
				</td>
				<?php } else { ?>
				<td class="text-center text-error">
					<?php echo JText::_('COM_EASYDISCUSS_INSTALLATION_PERMISSIONS_UNWRITABLE' );?>
				</td>
				<?php } ?>
			</tr>
			<?php } ?>

		</tbody>
	</table>
</div>

<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="active" value="<?php echo $active; ?>" />

</form>
