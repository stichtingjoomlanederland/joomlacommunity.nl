<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureEmailphpexceptions extends AtsystemFeatureAbstract
{
	protected $loadOrder = 3;

	private static $previousExceptionHandler;

	private static $emailAddress;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		static::$emailAddress = $this->cparams->getValue('emailphpexceptions', '');

		return (static::$emailAddress != '');
	}

	public function onAfterInitialise()
	{
		// Set the JError handler for E_ERROR to be the class' handleError method.
		JError::setErrorHandling(E_ERROR, 'callback', array('AtsystemFeatureEmailphpexceptions', 'handleError'));

		// Register the previously defined exception handler so we can forward errors to it
		self::$previousExceptionHandler = set_exception_handler(array('AtsystemFeatureEmailphpexceptions', 'handleException'));
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

	/**
	 * @param	\Exception	$error
	 */
	private static function doErrorHandling($error)
	{
		$code = (int) $error->getCode();

		// Do not handle "Not found" and "Forbidden" exceptions
		if ($code == 403 || $code == 404)
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

			return;
		}

		$type = get_class($error);
		$subject = 'Unhandled exception - '.$type;

		$body = <<<HTML
<p>A PHP Exception occurred on your site. Here you can find the stack trace:</p>
<p>
	Exception Type: <code>$type</code><br/>
	File: {$error->getFile()}<br/>
	Line: {$error->getLine()}<br/>
	Message: {$error->getMessage()} 
</p>
<pre>{$error->getTraceAsString()}</pre>

HTML;

		$config = JFactory::getConfig();
		$mailer = JFactory::getMailer();

		$mailer->sendMail(
			$config->get('mailfrom'),
			$config->get('fromname'),
			static::$emailAddress,
			$subject,
			$body,
			true);

		JErrorPage::render($error);
	}
}
