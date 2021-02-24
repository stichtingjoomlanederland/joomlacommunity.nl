<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\Console\AkeebaBackup\Extension\AkeebaBackup;

return new class implements ServiceProviderInterface {
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   4.0.0
	 */
	public function register(Container $container)
	{
		// Make sure that Joomla has registered the namespace for the plugin
		if (!class_exists('\Joomla\Plugin\Console\AkeebaBackup\Extension\AkeebaBackup'))
		{
			JLoader::registerNamespace('\Joomla\Plugin\Console\AkeebaBackup', realpath(__DIR__ . '/../src'));
		}

		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$plugin  = \Joomla\CMS\Plugin\PluginHelper::getPlugin('console', 'akeebabackup');
				$subject = $container->get(DispatcherInterface::class);

				return new AkeebaBackup($subject, (array) $plugin);
			}
		);
	}
};
