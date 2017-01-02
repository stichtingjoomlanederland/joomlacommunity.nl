<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/**
 * This handles Apache 401 Authorisation required messages. It is required when using .htaccess Maker (or Joomla!'s
 * own .htaccess as shipped in htaccess.txt) and administrator password protection and have also set an INVALID custom
 * ErrorDocument in Apache for HTTP 401 (Authorisation required). Apache will attempt to load the error document before
 * sending the HTTP basic authorisation headers to the browser. If the error document does not exist (it is an invalid
 * internal file path, typically with a .html or .shtml extension) the .htaccess SEF URL rewrwite rules will kick in and
 * ask Joomla! to handle the request. Since Joomla! cannot find a SEF URL of that name it returns an HTTP 404 Not Found
 * response. Apache sees that and freaks out, ending up in showing the 404 error page instead of sending the HTTP Basic
 * Authentication headers to the browser! This trick below detects the missing 401 custom error page redirection and
 * returns a **valid** HTTP 401 message, letting Apache continue its business.
 *
 * FOR CRYING OUT LOUD PEOPLE, FIX YOUR CRAPPY SERVERS!!!
 */
class AtsystemFeatureApache401 extends AtsystemFeatureAbstract
{
	protected $loadOrder = 1;

	public function onAfterInitialise()
	{
		if (!isset($_SERVER['REDIRECT_STATUS']))
		{
			return;
		}

		if ($_SERVER['REDIRECT_STATUS'] != 401)
		{
			return;
		}

			header('HTTP/1.0 401');
			echo <<< HTML
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>401 Authorization Required</title>
</head><body>
<h1>Authorization Required</h1>
<p>This server could not verify that you
are authorized to access the document
requested.  Either you supplied the wrong
credentials (e.g., bad password), or your
browser doesn't understand how to supply
the credentials required.</p>
</body></html>
HTML;

		$this->app->close();
	}
} 