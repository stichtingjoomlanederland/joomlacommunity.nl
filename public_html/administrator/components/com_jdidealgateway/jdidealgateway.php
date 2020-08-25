<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_jdidealgateway'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Get the input object
$input = Factory::getApplication()->input;

// Add stylesheet
HTMLHelper::stylesheet('com_jdidealgateway/jdidealgateway.css', false, true);

// Register our namespace
JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/jdidealgateway.php';

// Create the controller
try
{
	$controller = BaseController::getInstance('jdidealgateway');
	$controller->execute($input->get('task'));
	$controller->redirect();

	// Show the footer
	$format = $input->getCmd('format', $input->getCmd('tmpl', null));

	if (0 === strlen($format))
	{
		?>
        <div class="row-fluid">
            <div class="span-12 center">
                <a href="https://rolandd.com/products/ro-payments" target="_blank">RO Payments</a> 6.0.2 | Copyright (C) 2009 - <?php echo date('Y'); ?>
                <a href="https://rolandd.com/" target="_blank">RolandD Cyber Produksi</a>
            </div>
        </div>
		<?php
	}
}
catch (Exception $exception)
{
	Factory::getApplication()->redirect('index.php?option=com_jdidealgateway', $exception->getMessage(), 'error');
}
