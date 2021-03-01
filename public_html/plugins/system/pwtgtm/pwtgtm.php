<?php
/**
 * @package    PwtGtm
 *
 * @author     Hans Kuijpers - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-gtm
 */

// No direct access.


use Joomla\CMS\Application\WebApplication;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

/**
 * PWT ACL Plugin
 *
 * @since   3.0
 */
class plgSystemPwtgtm extends JPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  3.0
	 */
	protected $app;

	/**
	 * @var    string  base update url, to decide whether to process the event or not
	 *
	 * @since  1.0.0
	 */
	private $baseUrl = 'https://extensions.perfectwebteam.com/pwt-gtm';

	/**
	 * @var    string  Extension title, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extensionTitle = 'PWT GTM';

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 *                            Recognized key values include 'name', 'group', 'params', 'language'
	 *                            (this list is not meant to be comprehensive).
	 *
	 * @since   1.0
	 */
	public function __construct(&$subject, array $config = array())
	{
		parent::__construct($subject, $config);
	}

	/**
	 * onAfterRender trigger
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function onAfterRender()
	{
		// Only for frontend
		if (!$this->app->isClient('site'))
		{
			return;
		}

		// Check if GTM ID is given
		if (!$this->params->get('pwtgtm_id'))
		{
			return;
		}

		$analyticsId = $this->params->get('pwtgtm_id');

		// Google Tag Manager - party loaded in head
		$headScript = "
<script>
  <!-- Google Tag Manager -->
  (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','" . $analyticsId . "');
  <!-- End Google Tag Manager -->
</script>
          ";

		// Google Tag Manager - partly loaded directly after body
		$bodyScript = "<!-- Google Tag Manager -->
<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=" . $analyticsId . "\" height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
<!-- End Google Tag Manager -->
";

		$buffer = $this->app->getBody();
		$buffer = str_replace("</head>", $headScript . "</head>", $buffer);
		$buffer = preg_replace("/<body(\s[^>]*)?>/i", "<body\\1>\n" . $bodyScript, $buffer);
		$this->app->setBody($buffer);

		return;

	}
}
