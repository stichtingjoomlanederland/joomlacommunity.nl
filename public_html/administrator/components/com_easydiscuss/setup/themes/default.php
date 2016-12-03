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
<!DOCTYPE html>
<html class="demo-mobile-horizontal" lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo JText::_('COM_EASYDISCUSS_INSTALLER_INSTALLATION_TITLE'); ?> - <?php echo JText::_('COM_EASYDISCUSS_INSTALLER_INSTALLATION_STEP_TITLE');?> <?php echo $active; ?></title>
	<link href="<?php echo ED_SETUP_URL;?>/assets/images/logo.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>

	<link type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic,500italic,500,300italic,300" rel="stylesheet">
	<link type="text/css" href="<?php echo ED_SETUP_URL;?>/assets/icons/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link type="text/css" href="<?php echo ED_SETUP_URL;?>/assets/icons/ionicons/css/ionicons.min.css" rel="stylesheet">
	<link type="text/css" href="<?php echo ED_SETUP_URL;?>/assets/styles/theme.css" rel="stylesheet" />
	
	<script src="<?php echo ED_SETUP_URL;?>/assets/scripts/jquery.js" type="text/javascript"></script>
	<script src="<?php echo ED_SETUP_URL;?>/assets/scripts/bootstrap.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	<?php require(JPATH_ROOT . '/administrator/components/com_easydiscuss/setup/assets/scripts/script.js'); ?>
	</script>
</head>

<body class="step<?php echo $active;?>">
	<div class="header text-center">
		<div class="container">
			<div class="col-cell" style="padding-right: 20px;">
				<img src="<?php echo ED_SETUP_URL;?>/assets/images/logo.png" height="64" />
			</div>
			<div class="col-cell text-left">
				<h2 style="color: #fff; font-weight: 400; margin: 0 0 8px; font-size: 24px;">
					<?php echo JText::_('EasyDiscuss Installer');?>
				</h2>

				<div>
					<?php echo JText::_('Elegant forum extension that works out of the box');?>
				</div>
			</div>
			

			<div class="steps row-table">
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
</html>
