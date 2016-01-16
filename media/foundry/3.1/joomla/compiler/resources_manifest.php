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

$_manifest = array();

foreach($deps as $componentName => $component) {

	// Skip foundry
	if ($componentName=='Foundry') continue;

	$_deps = array('adapter' => $componentName);

	$types = array('template', 'view', 'language');

	foreach($types as $type) {

		if (empty($component[$type])) continue;

		$_deps[$type] = array();

		$modules = $component[$type];

		foreach ($modules as $module) {

			$name = $module->name;

 			if ($type=="view") {
				$name = str_replace(strtolower($componentName) . '/', '', $name);
			}

			$_deps[$type][] = $name;
		}
	}

	$_manifest[] = $_deps;
}
echo json_encode($_manifest);
