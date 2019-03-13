<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

class EasyDiscussAssets extends EasyDiscuss
{
	private $headers = array();

	/**
	 * Get joomla template
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getJoomlaTemplate($client = 'site')
	{
		static $template = array();

		if (!isset($template[$client])) {
			$app = JFactory::getApplication();

			// Try to load the template from joomla cache since some 3rd party plugins can change the templates on the fly.
			// This can also happen if joomla menu is associated with different template than the main templates. #155
			if ($client == 'site' && $app->isSite()) {
				$template[$client] = $app->getTemplate();
			} else {

				$clientId = 1;

				if ($client == 'site') {
					$clientId = 0;
				}

				$db = ED::db();

				$query = 'SELECT `template` FROM `#__template_styles` AS s';
				$query .= ' LEFT JOIN `#__extensions` AS e ON e.`type` = `template` AND e.`element` = s.`template` AND e.`client_id` = s.`client_id`';
				$query .= ' WHERE s.`client_id` = ' . $db->Quote($clientId) . ' AND `home` = 1';

				$db->setQuery($query);

				$result = $db->loadResult();

				// Default fallback template
				if (!$result) {
					$result = 'bluestork';

					if ($client == 'site') {
						$result = 'beez_20';
					}
				}

				$template[$client] = $result;
			}
		}

		return $template[$client];
	}

	public function addHeader( $key , $value=null )
	{
		$header	= "/*<![CDATA[*/ " . (isset($value)) ? "$key" : "var $key = '$value';" . "/*]]>*/ ";

		$document = JFactory::getDocument();
		$document->addScriptDeclaration( $header );

		return $this;
	}

	/**
	 * Retrieves a list of locations
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function locations($uri=false)
	{
		static $locations = array();

		$type = ($uri) ? 'uri' : 'path';

		if (isset($locations[$type])) {
			return $locations[$type];
		}

		$config	= ED::config();
		$URI = ($uri) ? '_URI' : '';
		$DS  = ($uri) ? '/' : DIRECTORY_SEPARATOR;

		$siteThemeUri = JURI::root() . '/components/com_easydiscuss/themes/';
		$adminThemeUri = JURI::root() . '/administrator/components/com_easydiscuss/themes/';
		$rootUri = JURI::root();

		$locations[$type] = array(
			'site' => $siteThemeUri . strtolower($config->get('layout_site_theme')),
			'site_base' => $siteThemeUri . strtolower($config->get('layout_site_theme_base')),
			'admin' => $adminThemeUri . strtolower($config->get('layout_admin_theme')),
			'admin_base' => $adminThemeUri . strtolower($config->get('layout_admin_theme_base')),
			'root' => $rootUri
			// 'site_override' => constant("DISCUSS_JOOMLA_SITE_TEMPLATES" . $URI) . $DS . self::getJoomlaTemplate('site') . $DS . "html" . $DS . "com_easydiscuss",
			// 'admin_override' => constant("DISCUSS_JOOMLA_ADMIN_TEMPLATES" . $URI) . $DS . self::getJoomlaTemplate('admin') . $DS . "html" . $DS . "com_easydiscuss",
			// 'module' => constant("DISCUSS_JOOMLA_MODULES" . $URI),
			// 'module_override' => constant("DISCUSS_JOOMLA_SITE_TEMPLATES" . $URI) . $DS . self::getJoomlaTemplate('site') . $DS . "html",
			// 'media' => constant("DISCUSS_MEDIA" . $URI),
			
		);

		return $locations[$type];
	}

	/**
	 * get path
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function path($location, $type='')
	{
		$locations = $this->locations();

		($path = $locations[$location]) || ($path = '');

		if ($type!=='') {
			$path .= DIRECTORY_SEPARATOR . $type;
		}

		return $path;
	}

	public function uri($location, $type='')
	{
		$locations = $this->locations(true);

		($path = $locations[$location]) || ($path = '');

		if ($type!=='') {
			$path .= '/' . $type;
		}

		return $path;
	}

	public function fileUri($location, $type='')
	{
		return "file://" . $this->path($location, $type);
	}

	public function relativeUri($dest, $root)
	{
		$dest = new JURI($dest);
		$dest = $dest->getPath();

		$root = new JURI($root);
		$root = $root->getPath();

		return $this->relative($dest, $root);
	}

	public function relative($dest, $root='', $dir_sep='/')
	{
		$root = explode($dir_sep, $root);
		$dest = explode($dir_sep, $dest);
		$path = '.';
		$fix = '';

		$diff = 0;
		for ($i = -1; ++$i < max(($rC = count($root)), ($dC = count($dest)));)
		{
			if(isset($root[$i]) and isset($dest[$i]))
			{
				if($diff)
				{
					$path .= $dir_sep. '..';
					$fix .= $dir_sep. $dest[$i];
					continue;
				}

				if($root[$i] != $dest[$i])
				{
					$diff = 1;
					$path .= $dir_sep. '..';
					$fix .= $dir_sep. $dest[$i];
					continue;
				}
			}
			elseif(!isset($root[$i]) and isset($dest[$i]))
			{
				for($j = $i-1; ++$j < $dC;)
				{
					$fix .= $dir_sep. $dest[$j];
				}
				break;
			}
			elseif(isset($root[$i]) and !isset($dest[$i]))
			{
				for($j = $i-1; ++$j < $rC;)
				{
					$fix = $dir_sep. '..'. $fix;
				}
				break;
			}
		}

		//$path = substr($path . $fix, 2);

		return $path . $fix;
	}

	/**
	 * Convert Path to URI
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function toUri($path)
	{
		jimport('joomla.filesystem.path');
		$path = JPath::clean($path);

		if( strpos($path, JPATH_ROOT) === 0 ) {
			$result = substr_replace($path, '', 0, strlen(JPATH_ROOT));
			$result = str_ireplace(DIRECTORY_SEPARATOR, '/', $result);
			$result = ltrim( $result, '/');
		} else {
			$parts = explode(DIRECTORY_SEPARATOR, $path);
			foreach ($parts as $i => $part) {
				if( $part == 'components' ) {
					break;
				}
				unset($parts[$i]);
			}

			$result = implode('/', $parts);
		}

		$result = DISCUSS_JURIROOT . '/' . $result;
		return $result;
	}
}