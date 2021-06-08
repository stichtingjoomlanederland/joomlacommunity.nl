<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

if (!Factory::getUser()->authorise('core.manage', 'com_jdidealgateway'))
{
	throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 404);
}

// Get the input object
$input = Factory::getApplication()->input;

HTMLHelper::stylesheet(
	'com_jdidealgateway/jdidealgateway.css',
	[
		'version' => 'auto',
		'relative' => true
	]
);

JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES);

if (class_exists(Gateway::class) === false)
{
	JLoader::registerNamespace('Jdideal', JPATH_LIBRARIES . '/Jdideal');
}

JLoader::register('JdidealgatewayHelper', JPATH_ADMINISTRATOR . '/components/com_jdidealgateway/helpers/jdidealgateway.php');

try
{
	$controller = BaseController::getInstance('jdidealgateway');
	$controller->execute($input->get('task'));
	$controller->redirect();

	$format = $input->getCmd('format', $input->getCmd('tmpl'));

	if (0 === strlen($format))
	{
		?>
		<div class="row-fluid ro-container">
			<div class="span-12 center item">
				<a href="https://rolandd.com/products/ro-payments" target="_blank">
					RO Payments
				</a> 6.4.1 | Copyright (C) 2009 -
				<?php echo date('Y'); ?>
				<a href="https://rolandd.com/" target="_blank">RolandD Cyber Produksi</a>
			</div>
		</div>
		<?php
	}
}
catch (Exception $exception)
{
	$app = Factory::getApplication();
	$app->enqueueMessage($exception->getMessage(), 'error');
	$app->redirect('index.php?option=com_jdidealgateway&view=jdidealgateway');
}
