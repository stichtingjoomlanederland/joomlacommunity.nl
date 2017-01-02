<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \FOF30\View\DataView\Form */

defined('_JEXEC') or die;

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/WebApplicationFirewall/plugin_warning');

echo $this->getRenderedForm();