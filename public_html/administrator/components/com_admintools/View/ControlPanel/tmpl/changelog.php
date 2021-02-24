<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\View\ControlPanel\Html;

/** @var  Html $this For type hinting in the IDE */

// Protect from unauthorized access
defined('_JEXEC') || die;

echo $this->changeLog;
