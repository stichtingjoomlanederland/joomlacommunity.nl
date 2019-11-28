<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Check if category is Bedrijvengids then set layout manually
if ((int) $this->item->catid === 374)
{
	$layout = 'bedrijf';
}
else
{
	// Set article layout
	$layout = 'article';
}

echo $this->loadTemplate($layout);