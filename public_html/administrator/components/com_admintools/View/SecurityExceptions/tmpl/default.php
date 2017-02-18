<?php
/**
 * @package   AdminTools
 * @copyright 2010-2017 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\SecurityExceptions\Form */

defined('_JEXEC') or die;

echo $this->loadAnyTemplate('admin:com_admintools/BlacklistedAddresses/toomanyips_warning');
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/needsipworkarounds', array(
        'returnurl' => base64_encode('index.php?option=com_admintools&view=SecurityExceptions')
));

echo $this->getRenderedForm();