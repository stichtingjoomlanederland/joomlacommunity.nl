<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureCsrfshield extends AtsystemFeatureAbstract
{
	protected $loadOrder = 340;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isFrontend())
		{
			return false;
		}

		if ($this->skipFiltering)
		{
			return false;
		}

		return ($this->cparams->getValue('csrfshield', 0) != 0);
	}

	public function onAfterInitialise()
	{
		$shieldSetting = $this->cparams->getValue('csrfshield', 0);

		if ($shieldSetting == 1)
		{
			$this->CSRFShield_BASIC();

			return;
		}

		$this->CSRFShield_ADVANCED();
	}

	public function onAfterRender()
	{
		if ($this->cparams->getValue('csrfshield', 0) != 2)
		{
			return;
		}

		$this->CSRFShield_PROCESS();
	}

	private function CSRFShield_BASIC()
	{
		// Do not activate on GET, HEAD and TRACE requests
		$method = strtoupper($_SERVER['REQUEST_METHOD']);

		if (in_array($method, array('GET', 'HEAD', 'TRACE')))
		{
			return;
		}

		// Check the referer, if available
		$valid = true;

		$referer = array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '';

		if (!empty($referer))
		{
			$jRefURI = JUri::getInstance($referer);
			$refererURI = $jRefURI->toString(array('host', 'port'));

			$jSiteURI = JUri::getInstance();
			$siteURI = $jSiteURI->toString(array('host', 'port'));

			$valid = ($siteURI == $refererURI);
		}

		if (!$valid)
		{
			$this->exceptionsHandler->blockRequest('csrfshield');
		}
	}

	/**
	 * Applies basic HTTP referer filtering to POST, PUT, DELETE etc HTTP requests,
	 * usually associated with form submission.
	 */
	private function CSRFShield_GetFieldName()
	{
		static $fieldName = null;

		if (empty($fieldName))
		{
			$config = JFactory::getConfig();

			$sitename = $config->get('sitename');
			$secret = $config->get('secret');

			$fieldName = md5($sitename . $secret);
		}

		return $fieldName;
	}

	/**
	 * Applies advanced reverse CAPTCHA checks to POST, PUT, DELETE etc HTTP
	 * requests, usually associated with form submission.
	 */
	private function CSRFShield_ADVANCED()
	{
		// Do not activate on GET, HEAD and TRACE requests
		$method = strtoupper($_SERVER['REQUEST_METHOD']);

		if (in_array($method, array('GET', 'HEAD', 'TRACE')))
		{
			return;
		}

		// Check for the existence of a hidden field
		$valid  = true;
		$hashes = array('get', 'post');

		$hiddenFieldName = $this->CSRFShield_GetFieldName();

		foreach ($hashes as $hash)
		{
			$input = $this->input->$hash;
			$ref = new ReflectionProperty($input, 'data');
			$ref->setAccessible(true);
			$allVars = $ref->getValue($input);

			if (!array_key_exists($hiddenFieldName, $allVars))
			{
				continue;
			}

			if (!empty($allVars[$hiddenFieldName]))
			{
				$this->exceptionsHandler->blockRequest('csrfshield');
			}
		}
	}

	/**
	 * Processes all forms on the page, adding a reverse CAPTCHA field
	 * for advanced filtering
	 */
	private function CSRFShield_PROCESS()
	{
		$hiddenFieldName = $this->CSRFShield_GetFieldName();

		if (method_exists($this->app, 'getBody'))
		{
			$buffer = $this->app->getBody();
		}
		else
		{
			$buffer = JResponse::getBody();
		}

		$buffer = preg_replace('#<[\s]*/[\s]*form[\s]*>#iU', '<input type="text" name="' . $hiddenFieldName . '" value="" style="float: left; position: absolute; z-index: 1000000; left: -10000px; top: -10000px;" /></form>', $buffer);

		if (method_exists($this->app, 'setBody'))
		{
			$this->app->setBody($buffer);
		}
		else
		{
			JResponse::setBody($buffer);
		}
	}
} 