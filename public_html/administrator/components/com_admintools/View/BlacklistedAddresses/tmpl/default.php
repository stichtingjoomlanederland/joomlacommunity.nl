<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Form */

defined('_JEXEC') or die;

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/WebApplicationFirewall/plugin_warning');
echo $this->loadAnyTemplate('admin:com_admintools/BlacklistedAddresses/feature_warning');

echo $this->getRenderedForm();