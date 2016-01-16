<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

foreach($deps as $componentName => $component) {

	// Skip foundry
	if ($componentName=='Foundry') continue;

	echo 'FD31.installer("' . $componentName . '", "definitions", function($){' . "\n";

	// 1. Predefine dependencies

	// 1.1 Predefine scripts
	if (!empty($component['script'])) {

		$scripts = $component['script'];

		echo '$.module(' . $this->getNames($scripts) . ');' . "\n";
	}

	// 1.2 Predefine templates
	if (!empty($component['template'])) {

		$templates = $component['template'];

		echo '$.require.template.loader(' . $this->getNames($templates) . ');' . "\n";
	}

	// 1.3 Predefine views
	if (!empty($component['view'])) {

		$views = $component['view'];

		echo '$.require.template.loader(' . $this->getNames($views) . ');' . "\n";
	}

	// 1.4 Predefine languages
	if (!empty($component['language'])) {

		$languages = $component['language'];

		echo '$.require.language.loader(' . $this->getNames($languages) . ');' . "\n";
	}

	// 2. Stylesheets
	if (!empty($component['stylesheet'])) {

		$stylesheets = $component['stylesheet'];

		echo '(function(){' . "\n";
		echo 'var stylesheetNames = ' . $this->getNames($stylesheets) . ';' . "\n";
		echo 'var state = ($.stylesheet(' . $this->getStylesheetData($stylesheets) . ')) ? "resolve" : "reject";' . "\n";
		echo '$.each(stylesheetNames, function(i, stylesheet){ $.require.stylesheet.loader(stylesheet)[state](); });' . "\n";
		echo '})();' . "\n";
	}

	echo '});' . "\n";
}

foreach($deps as $componentName => $component) {

	// Skip foundry
	if ($componentName=='Foundry') continue;	

	echo 'FD31.installer("' . $componentName . '", "scripts", function($){' . "\n";

	// 3. Scripts
	if (!empty($scripts)) {

		echo $this->getData($scripts);
	}

	echo '});' . "\n";
}
