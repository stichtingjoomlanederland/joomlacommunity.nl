<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
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

	public static function getJoomlaTemplate($client = 'site')
	{
		static $template = array();

		if( !array_key_exists($client, $template) ) {
			$clientId = ($client == 'site') ? 0 : 1;

			$db		= DiscussHelper::getDbo();

			if( DiscussHelper::isJoomla15() ) {
				$query	= 'SELECT template FROM `#__templates_menu`'
						. ' WHERE client_id = ' . $db->quote($clientId) . ' AND menuid = 0';
			} else {
				$query	= 'SELECT template FROM `#__template_styles` AS s'
						. ' LEFT JOIN `#__extensions` AS e ON e.type = `template` AND e.element=s.template AND e.client_id=s.client_id'
						. ' WHERE s.client_id = ' . $db->quote($clientId) . ' AND home = 1';
			}
			$db->setQuery( $query );

			// Fallback template
			if( !$result = $db->loadResult() ) {
				$result = ($client == 'site') ? 'beez_20' : 'bluestork';
			}

			$template[$client] = $result;
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
	 * @param	string
	 * @return	
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
	 * 
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
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
	 * Convert path to URI
	 *
	 * Convert /var/public_html/components/theme/simplistic/styles/blabla.less
	 * to http://mysite.com/components/theme/simplistic/styles/blabla.less
	 *
	 * @param	string	$path
	 *
	 * @return	string	Full path URI
	 */
	public function toUri( $path )
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
