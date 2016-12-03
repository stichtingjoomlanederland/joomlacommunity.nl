<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2012 StackIdeas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

static $loaded	= false;

if (!$loaded) {

	$app = JFactory::getApplication();
	$doc = JFactory::getDocument();

	$version = "2.1";

	$environment = JRequest::getString( 'foundry_environment', '', 'GET' );

	if (empty($environment)) {

		$environment = 'production';

		if (isset($foundry_environment)) {

			$environment = $foundry_environment;
		}
	}

	$foundryPath = rtrim(JURI::root(), '/') . '/media/foundry/' . $version . '/';

	switch ($environment) {

		case 'production':

			$scriptPath = $foundryPath . 'scripts/';

			$scripts = array(
				'foundry'
			);

			break;

		case 'development':

			$scriptPath = $foundryPath . 'scripts_/';

			$scripts = array(
				'dispatch',
				'abstractComponent',
				'jquery',
				'underscore',
				'utils',
				'uri',
				'joomla',
				'module',
				'script',
				'stylesheet',
				'language',
				'template',
				'require',
				'iframe-transport',
				'component'
			);

			break;
	}

	foreach ($scripts as $i=>$script) {

		$doc->addScript($scriptPath . $script . '.js');
	}

	ob_start();
?>

dispatch
	.to("Foundry/2.1 Bootstrap")
	.at(function($, manifest) {

		<?php if ($environment=="development"): ?>
		window.F = $;
		<?php endif; ?>

		$.rootPath    = '<?php echo JURI::root(); ?>';
		$.indexUrl    = '<?php echo JURI::root() . (($app->isAdmin()) ? 'administrator/index.php' : 'index.php') ?>';
		$.path        = '<?php echo $foundryPath; ?>';
		$.scriptPath  = '<?php echo $scriptPath; ?>';
		$.environment = '<?php echo $environment; ?>';
		$.joomlaVersion = <?php echo floatval(JVERSION); ?>;
		$.locale = {
			lang: '<?php echo JFactory::getLanguage()->getTag(); ?>'
		};

		// Make sure core plugins are installed first
		dispatch("Foundry/2.1")
			.containing($)
			.onlyTo("Foundry/2.1 Core Plugins");
	});

<?php
	$contents = ob_get_contents();
	ob_end_clean();

	$doc->addScriptDeclaration($contents);
}
?>
