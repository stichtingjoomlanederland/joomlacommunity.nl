<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureSecretword extends AtsystemFeatureAbstract
{
	protected $loadOrder = 60;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isBackend())
		{
			return false;
		}

		$password = $this->cparams->getValue('adminpw', '');

		return !empty($password);
	}

	public function onAfterInitialise()
	{
		if ($this->isAdminAccessAttempt())
		{
			$this->checkSecretWord();

			return;
		}

		// If there is an administrator secret word set, upon logout redirect to the site's home page
		$password = $this->cparams->getValue('adminpw', '');

		if (!empty($password))
		{
			$input  = $this->input;
			$option = $input->getCmd('option', '');
			$task   = $input->getCmd('task', '');
			$uid    = $input->getInt('uid', 0);

			$loggingMeOut = true;

			if (!empty($uid))
			{
				$myUID = JFactory::getUser()->id;
				$loggingMeOut = ($myUID == $uid);
			}

			if (($option == 'com_login') && ($task == 'logout') && $loggingMeOut)
			{
				$input = $this->app->input;
				$method = $input->getMethod();

				$input->$method->set('return', base64_encode('index.php?' . urlencode($password)));
			}
		}
	}

	/**
	 * Checks if the secret word is set in the URL query, or redirects the user
	 * back to the home page.
	 */
	protected function checkSecretWord()
	{
		$password = $this->cparams->getValue('adminpw', '');

		$myURI = JUri::getInstance();

		// If the "password" query param is not defined, the default value
		// "thisisnotgood" is returned. If it is defined, it will return null or
		// the value after the equal sign.
		$check = $myURI->getVar($password, 'thisisnotgood');

		if ($check == 'thisisnotgood')
		{
			// Uh oh... Unauthorized access! Let's redirect the intruder back to the site's home page.
			if (!$this->exceptionsHandler->logAndAutoban('adminpw'))
			{
				return;
			}

			$this->redirectAdminToHome();
		}
	}
}