<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/**
 * Monitors com_config changes and emails the user
 */
class AtsystemFeatureConfigmonitor extends AtsystemFeatureAbstract
{
	/** @var   int  The load order of each feature */
	protected $loadOrder = 220;

	/**
	 * Should we monitor changes to Global Configuration?
	 *
	 * @var   bool
	 */
	private $enabledGlobal = false;

	/**
	 * Should we monitor changes to Component Configuration?
	 *
	 * @var   bool
	 */
	private $enabledComponents = false;

	/**
	 * Which action should I take when a change is detected? 'email' for sending a warning email, 'block' for treating
	 * the request as a security exception.
	 *
	 * @var   string
	 */
	private $action = 'email';

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$this->enabledGlobal     = $this->cparams->getValue('configmonitor_global', 0) == 1;
		$this->enabledComponents = $this->cparams->getValue('configmonitor_components', 0) == 1;
		$this->action            = $this->cparams->getValue('configmonitor_action', 'email');

		return $this->enabledGlobal || $this->enabledComponents;
	}

	/**
	 * Disables creating new admins or updating new ones
	 */
	public function onAfterInitialise()
	{
		$input  = $this->input;
		$option = $input->getCmd('option', '');
		$task   = $input->getCmd('task', '');

		if ($option != 'com_config')
		{
			return;
		}

		$block = false;

		if ($this->enabledGlobal)
		{
			$block |= in_array($task, ['config.save.application.apply', 'config.save.application.save']);
		}

		if ($this->enabledComponents)
		{
			$block |= in_array($task, ['config.save.component.apply', 'config.save.component.save']);
		}

		if (!$block)
		{
			return;
		}

		// Get the correct reason (is this Global Configuration or component configuration)?
		$id            = $input->getInt('id', 0);
		$component     = $input->getCmd('component', '');
		$componentName = $this->getComponentName($id, $component);

		// Default reason for blocking / reporting: Global Configuration
		$jlang = JFactory::getLanguage();
		$jlang->load('com_cpanel', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_cpanel', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_cpanel', JPATH_ADMINISTRATOR, null, true);
		$extraInfo = JText::_('COM_CPANEL_LINK_GLOBAL_CONFIG');

		// If, however, there is a component we need to report extension configuration monitor as the reason
		if (!empty($componentName))
		{
			$jlang = JFactory::getLanguage();
			$jlang->load($componentName . '.sys', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load($componentName . '.sys', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load($componentName . '.sys', JPATH_ADMINISTRATOR, null, true);

			// Now set the extra information
			$extraInfo = JText::_($componentName);
		}

		// If we are set to block requests hook into Admin Tools' log and block system
		if ($this->action == 'block')
		{
			$this->exceptionsHandler->blockRequest('configmonitor', null, null, $extraInfo);

			return;
		}

		// Otherwise we need to send an email
		$this->sendEmail($extraInfo);
	}

	/**
	 * Get the component name based either on the extension ID or (preferably) the component name from the request.
	 *
	 * @param   int     $id         An extension ID passed in the request. Must belong to a component.
	 * @param   string  $component  A component name passed in the request.
	 *
	 * @return  string  The component name, or an empty string if there is no corresponding component.
	 */
	private function getComponentName($id, $component)
	{
		$component = trim(strtolower($component));

		// We have a component name
		if (!empty($component))
		{
			return $component;
		}

		// We don't have a component name or ID. Nothing to do
		if (empty($id))
		{
			return '';
		}

		// We have an ID. Try to get the component name from the #__extensions table.
		$db = $this->container->db;
		$query = $db->getQuery(true)
			->select($db->qn('element'))
			->from($db->qn('#__extensions'))
			->where($db->qn('extension_id') . ' = ' . $db->q((int) $id))
			->where($db->qn('type') . ' = ' . $db->q('component'));
		$componentName = $db->setQuery($query)->loadResult();

		if (empty($componentName))
		{
			return '';
		}

		return $componentName;
	}

	/**
	 * Sends a warning email to the addresses set up to receive security exception emails
	 *
	 * @param   string  $configArea  The human readable name of the configuration area being edited
	 */
	private function sendEmail($configArea)
	{
		// Load the component's administrator translation files
		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		// Construct the replacement table
		$substitutions = $this->exceptionsHandler->getEmailVariables(JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATE_REASON_ADMINLOGINFAIL'), [
			'[AREA]'      => $configArea,
		]);

		// Let's get the most suitable email template
		$template = $this->exceptionsHandler->getEmailTemplate('configmonitor', true);

		// Got no template, the user didn't published any email template, or the template doesn't want us to
		// send a notification email. Anyway, let's stop here.
		if (!$template)
		{
			return true;
		}
		else
		{
			$subject = $template[0];
			$body = $template[1];
		}

		foreach ($substitutions as $k => $v)
		{
			$subject = str_replace($k, $v, $subject);
			$body    = str_replace($k, $v, $body);
		}

		try
		{
			$config = $this->container->platform->getConfig();
			$mailer = JFactory::getMailer();

			$mailfrom = $config->get('mailfrom');
			$fromname = $config->get('fromname');

			$recipients = explode(',', $this->cparams->getValue('emailbreaches', ''));
			$recipients = array_map('trim', $recipients);

			foreach ($recipients as $recipient)
			{
				if (empty($recipient))
				{
					continue;
				}

				// This line is required because SpamAssassin is BROKEN
				$mailer->Priority = 3;

				$mailer->isHtml(true);
				$mailer->setSender(array($mailfrom, $fromname));

				// Resets the recipients, otherwise they will pile up
				$mailer->clearAllRecipients();

				if ($mailer->addRecipient($recipient) === false)
				{
					// Failed to add a recipient?
					continue;
				}

				$mailer->setSubject($subject);
				$mailer->setBody($body);
				$mailer->Send();
			}
		}
		catch (\Exception $e)
		{
			// Joomla! 3.5 and later throw an exception when crap happens instead of suppressing it and returning false
		}
	}
}
