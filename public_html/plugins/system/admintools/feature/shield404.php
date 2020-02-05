<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureShield404 extends AtsystemFeatureAbstract
{
	protected $loadOrder = 2;

	private static $previousExceptionHandler;

	private static $blockedUrls;

	/** @var  AtsystemUtilExceptionshandler */
	private static $exceptionHandler;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		// Assign those values to our static variables so we can reference to them in the static context
		static::$blockedUrls = $this->cparams->getValue('404shield', "wp-admin.php\nwp-login.php\nwp-content/*\nwp-admin/*");
		static::$exceptionHandler = $this->exceptionsHandler;

		return ($this->cparams->getValue('404shield_enable', 1));
	}

	public function onAfterInitialise()
	{
		// Joomla 3: Set the JError handler for E_ERROR to be the class' handleError method.
		if (class_exists('JError'))
		{
			JError::setErrorHandling(E_ERROR, 'callback', array('AtsystemFeatureShield404', 'handleError'));
		}

		// Register the previously defined exception handler so we can forward errors to it
		self::$previousExceptionHandler = set_exception_handler(array('AtsystemFeatureShield404', 'handleException'));
	}

	public static function handleError($error)
	{
		static::doErrorHandling($error);
	}

	public static function handleException($exception)
	{
		// If this isn't a Throwable then bail out
		if (!($exception instanceof Throwable) && !($exception instanceof Exception))
		{
			throw new InvalidArgumentException(
				sprintf('The error handler requires an Exception or Throwable object, a "%s" object was given instead.', get_class($exception))
			);
		}

		static::doErrorHandling($exception);
	}

	private static function doErrorHandling($error)
	{
		$app = JFactory::getApplication();
		$isAdmin = false;

		if (method_exists($app, 'isClient'))
		{
			$isAdmin = $app->isClient('administrator');
		}
		elseif(method_exists($app, 'isAdmin'))
		{
			$isAdmin = $app->isAdmin();
		}

		if ($isAdmin || ((int) $error->getCode() !== 404))
		{
			// Proxy to the previous exception handler if available, otherwise just render the error page
			if (self::$previousExceptionHandler)
			{
				call_user_func_array(self::$previousExceptionHandler, array($error));
			}
			else
			{
				JErrorPage::render($error);
			}
		}

		$rows 		 = explode("\n", static::$blockedUrls);
		$blockedURLs = array_map('trim', $rows);
		$root 		 = JUri::root();
		$currentURL	 = JUri::getInstance();
		$currentPath = $currentURL->toString(array('scheme', 'host', 'port', 'path'));

		// Remove the root from the current path so we can work with relative URLs
		$currentPath = str_replace($root, '', $currentPath);
		$currentPath = static::removeLanguageTag($currentPath);
		$currentPath = trim($currentPath, '/');

		$block = false;

		foreach ($blockedURLs as $blockPattern)
		{
			$shouldNegate = false;

			// If the pattern starts with a !, we're going to negate the assumption
			if (substr($blockPattern, 0, 1) == '!')
			{
				$blockPattern = substr($blockPattern, 1);
				$shouldNegate = true;
			}

			$blockPattern = trim($blockPattern, '/');

			$match = fnmatch($blockPattern, $currentPath);

			// Should I invert the result?
			if ($shouldNegate)
			{
				$match = !$match;
			}

			$block = $match;

			// No need to continue if we have to block the request
			if ($block)
			{
				break;
			}
		}

		if ($block)
		{
			static::$exceptionHandler->logAndAutoban('404shield');
		}

		// Proxy to the previous exception handler if available, otherwise just render the error page
		if (self::$previousExceptionHandler)
		{
			call_user_func_array(self::$previousExceptionHandler, array($error));
		}
		else
		{
			JErrorPage::render($error);
		}
	}

	/**
	 * Removes the language tag from the URLs generated by multilanguage sites.
	 *
	 * @param $pathURL
	 *
	 * @return string
	 */
	private static function removeLanguageTag($pathURL)
	{
		/** @var \Joomla\CMS\Application\SiteApplication $app */
		$app               = \JFactory::getApplication();
		$hasLanguageFilter = false;

		if (method_exists($app, 'getLanguageFilter'))
		{
			$hasLanguageFilter = $app->getLanguageFilter();
		}

		if (!$hasLanguageFilter)
		{
			return $pathURL;
		}

		$db = JFactory::getDbo();

		// Let's get the list of SEF code used in the URLs of the published languages
		$query = $db->getQuery(true)
					->select($db->qn('sef'))
					->from($db->qn('#__languages'))
					->where($db->qn('published').' = '.$db->q('1'));
		$languages = $db->setQuery($query)->loadColumn();

		foreach ($languages as $lang)
		{
			$lang .= '/';

			// Replace only the starting string
			if (strpos($pathURL, $lang) !== 0)
			{
				continue;
			}

			$pathURL = substr($pathURL, strlen($lang));

			// There can be only one language tag in the URL, so if we get here it means that there's nothing left to do
			break;
		}

		return $pathURL;
	}
}
