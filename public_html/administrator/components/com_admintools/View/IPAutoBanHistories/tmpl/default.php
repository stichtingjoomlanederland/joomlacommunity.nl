<?php
/**
 * @package   AdminTools
 * @copyright 2010-2017 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\IPAutoBanHistories\Form */

defined('_JEXEC') or die;

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/plugin_warning');

echo $this->getRenderedForm();
