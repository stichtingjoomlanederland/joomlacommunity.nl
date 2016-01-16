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

/*
	Static compilation
	------------------
	[component.static.js]
	1. Foundry (foundry.js)
	2. Templates
	3. Stylesheets
	4. Predefine Scripts
	5. Scripts
	6. Continue to "Optimized compilation".

	Optimized compilation
	---------------------
	1. Predefine ALL component dependencies
	   * Scripts
	   * Templates (incl. views)
	   * Languages
	2. Stylesheets
	3. Scripts

	Extras
	------
	[component.extras.js]
	This is the failsafe extras file.
	1. Templates
	2. Views
	3. Languages

	[component.extras.json --> component.extras.%hash%.js]
	This is so we can quickly construct a "template x language" hash.
	1. Manifest for component templates & languages.
*/

// FOUNDRY

if ($compileMode=='static') {

	// 1. Foundry (foundry.js)
	echo $this->getFoundry();

	echo 'FD31.plugin("static", function($){' . "\n";

	if (!empty($deps['Foundry'])) {

		$foundry = $deps['Foundry'];

		// 2. Templates
		if (!empty($foundry['template'])) {

			$templates = $deps['Foundry']['template'];

			echo '$.require.template.loader(' . $this->getJSONData($templates) . ');' . "\n";
		}

		// 3. Stylesheets
		if (!empty($foundry['stylesheet'])) {

			$stylesheets = $foundry['stylesheet'];

			echo '(function(){' . "\n";
			echo 'var stylesheetNames = ' . $this->getNames($stylesheets) . ';' . "\n";
			echo 'var state = ($.stylesheet(' . $this->getStylesheetData($stylesheets, $minify) . ')) ? "resolve" : "reject";' . "\n";
			echo '$.each(stylesheetNames, function(i, stylesheet){ $.require.stylesheet.loader(stylesheet)[state](); });' . "\n";
			echo '})();' . "\n";
		}

		// 4. Predefine scripts
		if (!empty($foundry['script'])) {

			$scripts = $foundry['script'];

			echo '$.module(' . $this->getNames($scripts) . ');' . "\n";

			// 5. Scripts
			echo $this->getData($scripts);
		}
	}

	echo '});' . "\n";
}

include(FD31_FOUNDRY_PATH . '/joomla/compiler/' . 'optimized.php');
