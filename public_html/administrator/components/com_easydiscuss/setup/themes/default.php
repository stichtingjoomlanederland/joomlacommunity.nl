<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo JText::_('COM_EASYDISCUSS_INSTALLER_INSTALLATION_TITLE'); ?> - <?php echo JText::_('COM_EASYDISCUSS_INSTALLER_INSTALLATION_STEP_TITLE');?> <?php echo $active; ?></title>
	<link href="<?php echo ED_SETUP_URL;?>/assets/images/logo.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>

	<?php if (JVERSION < 4.0 ) { ?>
			<link type="text/css" href="<?php echo JURI::root(true);?>/media/jui/css/bootstrap.min.css" rel="stylesheet" />
			<link type="text/css" href="<?php echo JURI::root(true);?>/media/jui/css/icomoon.css" rel="stylesheet" />
	<?php } else { ?>
			<link type="text/css" href="<?php echo JURI::root(true);?>/media/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
			<link type="text/css" href="<?php echo JURI::root(true);?>/media/system/scss/_icomoon.scss" rel="stylesheet" />
	<?php } ?>

	<link type="text/css" href="//fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic,500italic,500,300italic,300" rel="stylesheet">
	<link type="text/css" href="<?php echo ED_SETUP_URL;?>/assets/styles/theme.css?<?php echo ED_HASH; ?>" rel="stylesheet" />

	<?php if (JVERSION < 4.0 ) { ?>
			<script src="<?php echo JURI::root(true);?>/media/jui/js/jquery.min.js" type="text/javascript"></script>
			<script src="<?php echo JURI::root(true);?>/media/jui/js/bootstrap.min.js" type="text/javascript"></script>
	<?php } else { ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
			<script src="<?php echo JURI::root(true);?>/media/vendor/jquery/js/jquery.min.js"></script>
			<script src="<?php echo JURI::root(true);?>/media/vendor/bootstrap/js/bootstrap.min.js"></script>
			<script src="<?php echo JURI::root(true);?>/media/system/js/toolbar.min.js"></script>
	<?php } ?>

	<script type="text/javascript">
		<?php require(JPATH_ROOT . '/administrator/components/' . ED_IDENTIFIER . '/setup/assets/scripts/script.js'); ?>
	</script>
</head>

<body class="step<?php echo $active;?>">
	<div class="header text-center">
		<div class="container">
			<div class="top-bar row-table">
				<div class="col-cell">
					<div class="row-table">
						<div class="col-cell">
							<img src="<?php echo ED_SETUP_URL;?>/assets/images/logo.png" height="48" width="48" />
						</div>
						<div class="col-cell text-left">
							<h3>EasyDiscuss</h3>
							<span>Q&amp;A &amp; Forum extension for Joomla!</span>
						</div>
					</div>
				</div>
				<div class="col-cell">
					<a class="btn btn-support" href="https://stackideas.com/forums" target="_blank">
						<img src="<?php echo ED_SETUP_URL;?>/assets/images/support-color.svg" width="24" height="24" alt="Support" />
						&nbsp;<span>Need help?</span>
					</a>
				</div>
			</div>
		</div>
		<div class="container text-center">
			<div class="row-table">
				<h2>
					<?php echo JText::_($activeStep->title);?>
				</h2>

				<?php if ($activeStep->template != 'complete') { ?>
					<div>
						<?php echo JText::_($activeStep->desc);?>
					</div>
				<?php } ?>
			</div>
			

			<div class="steps row-table" data-installation-steps>
				<?php include(__DIR__ . '/default.steps.php'); ?>
			</div>
		</div>
	</div>
	
	<div class="content">
		<div class="container">
			<?php include(__DIR__ . '/steps/' . $activeStep->template . '.php'); ?>
		</div>
	</div>

	<div class="footer">
		<?php include(__DIR__ . '/default.footer.php'); ?>
	</div>
</body>