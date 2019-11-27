<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

// Load the scripts
HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'com_pwtimage/pwtimage.min.js', array('relative' => true, 'version' => 'auto'));
HTMLHelper::_('stylesheet', 'com_pwtimage/pwtimage.min.css', array('relative' => true, 'version' => 'auto'));

// Load the language files
$language = Factory::getLanguage();
$language->load('com_pwtimage', JPATH_SITE . '/components/com_pwtimage', 'en-GB');
$language->load('com_pwtimage', JPATH_SITE . '/components/com_pwtimage');

// Make sure the iFrame sets no base otherwise Tabby cries
Factory::getDocument()->base = '';

// Get the function to call
$input = Factory::getApplication()->input;

// If we are going through here, we are not a modal, so render plain layout
$this->form->setFieldAttribute('image', 'label', '');
$this->form->setFieldAttribute('image', 'description', '');

Factory::getDocument()->setMetaData('viewport', 'width=device-width, initial-scale=1.0');
?>

<form class="pwt-image-extension js-image-form" action="index.php?option=com_pwtimage&view=image&layout=close" enctype="multipart/form-data" method="post">
	<?php echo $this->form->getInput('image'); ?>
	<input type="hidden" id="formPath" name="path" value="" />
	<input type="hidden" id="alt" name="alt" value="" />
	<input type="hidden" id="caption" name="caption" value="" />
</form>
