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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo t('INSTALLER_INSTALLATION_TITLE'); ?> - <?php echo t('INSTALLER_INSTALLATION_STEP_TITLE');?> <?php echo $active; ?></title>
	
	<link type="image/vnd.microsoft.icon" href="<?php echo SI_SETUP_URL;?>/assets/images/logo.png" rel="shortcut icon" />
	<link type="text/css" href="//fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic,500italic,500,300italic,300" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.5.9/lottie.min.js"></script>	
	<link type="text/css" href="<?php echo SI_SETUP_URL;?>/assets/styles/theme.css?<?php echo SI_HASH; ?>" rel="stylesheet" />

	<?php if (JVERSION < 4.0 ) { ?>
			<script src="<?php echo JURI::root(true);?>/media/jui/js/jquery.min.js" type="text/javascript"></script>
			<script src="<?php echo JURI::root(true);?>/media/jui/js/bootstrap.min.js" type="text/javascript"></script>
	<?php } else { ?>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
			<script src="<?php echo JURI::root(true);?>/media/vendor/jquery/js/jquery.min.js"></script>
			<script src="<?php echo JURI::root(true);?>/media/vendor/bootstrap/js/bootstrap.min.js"></script>
			<script src="<?php echo JURI::root(true);?>/media/system/js/toolbar.min.js"></script>
	<?php } ?>

	<script>
		<?php require(SI_SETUP . '/assets/scripts/script.js'); ?>
	</script>
</head>

<body class="install-step<?php echo $active;?>">

<div class="si-installer">
	<div class="si-installer__left">
		<div id="svg__ani-sidebarbg" class="si-installer__left-ani-bg"></div>
		<div class="si-installer__left-bd">
			<div class="si-installer__brand">
				<img src="<?php echo SI_SETUP_URL;?>/assets/images/logo-easydiscuss.svg">
				<p>The best discussion and collaboration extension built for Joomla!</p>
			</div>
		</div>
		<div class="si-installer__left-ft">
			<div class="si-installer-links-group">
				<div class="si-installer-links-group__item">
					<a href="https://facebook.com/StackIdeas" target="_blank">
						<img src="<?php echo SI_SETUP_URL;?>/assets/images/icon-facebook.svg" alt="StackIdeas Facebook"> <span>Stack Ideas</span>
					</a>
				</div>
				<div class="si-installer-links-group__item">
					<a href="https://twitter.com/StackIdeas" target="_blank">
						<img src="<?php echo SI_SETUP_URL;?>/assets/images/icon-twitter.svg" alt="StackIdeas Twitter"> <span>Stack Ideas</span>
					</a>
				</div>
				<div class="si-installer-links-group__item">
					<a href="https://stackideas.com/forums" target="_blank">
						<img src="<?php echo SI_SETUP_URL;?>/assets/images/icon-help.svg" alt="StackIdeas Helpdesk"> <span>Need Help?</span>
					</a>
				</div>
			</div>
		</div>
	</div>

	<div action="" class="si-installer__right">
		<div class="si-installer__right-bd">
			<div class="si-installer-content">
				<div class="si-installer-content__hd">
					<div class="si-installer-content__title">
						<b><?php echo JText::_($activeStep->title);?></b>
					</div>

					<?php if ($activeStep->template != 'complete') { ?>
					<div class="si-installer-content__sub-title">
						<?php echo JText::_($activeStep->desc);?>
					</div>
					<?php } ?>
				</div>
				<div class="si-installer-content__bd">
					<?php include(__DIR__ . '/steps/' . $activeStep->template . '.php'); ?>
				</div>
			</div>
		</div>

		<?php if ($active != 'complete') { ?>
		<div class="si-installer__right-ft">
			<?php include(__DIR__ . '/actions.php'); ?>
		</div>
		<?php } ?>
	</div>
</div>

<script>
var animationRemoteBg = bodymovin.loadAnimation({
	container: document.getElementById('svg__ani-sidebarbg'),
	path: '<?php echo SI_SETUP_URL; ?>/assets/images/background.json',
	autoplay: true,
	renderer: 'svg',
	loop: true
});
</script>
</body>