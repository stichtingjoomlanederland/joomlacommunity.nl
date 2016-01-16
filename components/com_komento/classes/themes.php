<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

Komento::import( 'helper', 'date' );
//Komento::import( 'helper', 'tooltip' );
Komento::import( 'helper', 'string' );
Komento::import( 'helper', 'comment' );
//Komento::import( 'helper', 'router' );

class KomentoThemes
{
	// Holds all the template variables
	public $vars			= null;
	private $_system			= null;
	protected $_json		= null;

	// User selected theme
	protected $_theme		= null;
	protected $_direction	= null;
	protected $_themeInfo	= array();

	/**
	 * Pass theme name from config
	 */
	public function __construct( $theme = 'kuro' )
	{
		$this->_theme	= $theme;

		if(!defined('KOMENTO_CLI'))
		{
			$this->init();
		}
	}

	public function init()
	{
		$obj			= new stdClass();
		$obj->config	= Komento::getConfig();
		$obj->konfig	= Komento::getKonfig();
		$obj->my		= Komento::getProfile();
		$obj->acl		= Komento::getHelper( 'acl' );
		$obj->session	= JFactory::getSession();

		$this->_system	= $obj;
	}

	public function getDirection()
	{
		if ($this->_direction === null)
		{
			$document	= JFactory::getDocument();
			$this->_direction	= $document->getDirection();
		}

		return $this->_direction;
	}

	public function getNouns( $text , $count , $includeCount = false )
	{
		return KomentoStringHelper::getNoun( $text , $count , $includeCount );
	}

	public function chopString( $string , $length )
	{
		return JString::substr( $string , 0 , $length );
	}

	public function formatDate( $format , $dateString )
	{
		$date	= KomentoDateHelper::dateWithOffSet($dateString);
		return $date->toFormat( $format );
	}

	/**
	 * Set a template variable.
	 */
	public function set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function getName()
	{
		return $this->_theme;
	}

	/**
	 * Open, parse, and return the template file.
	 *
	 * @param $file string the template file name
	 */
	public function fetch( $file )
	{
		static $tpl = array();

		if (empty($tpl[$file]))
		{
			$tpl[$file] = $this->resolve( $file );
		}

		$system = $this->_system;

		if( isset( $this->vars ) )
		{
			extract($this->vars);
		}

		ob_start();

		if( !JFile::exists( $tpl[$file] ) )
		{
			echo JText::sprintf( 'Invalid template file %1s' , $tpl[$file] );
		}
		else
		{
			include($tpl[$file]);
		}

		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function resolve( $file )
	{
		if(defined('KOMENTO_CLI'))
		{
			$defaultPath	= KOMENTO_THEMES . DIRECTORY_SEPARATOR . KOMENTO_THEME_BASE . DIRECTORY_SEPARATOR . $file;

			return $defaultPath;
		}

		$path = '';

		$mainframe		= JFactory::getApplication();

		$config			= Komento::getConfig();

		// load the file based on the theme's config.ini
		$info 			= $this->getThemeInfo( $this->_theme );

		/**
		 * Precedence in order.
		 * 1. Template override
		 * 2. Component override
		 * 3. Selected theme
		 * 4. Parent theme
		 * 5. Default system theme
		 */

		$overridePath	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . $file;
		$componentPath	= Komento::loadApplication()->getComponentThemePath() . DIRECTORY_SEPARATOR . $file;
		$selectedPath	= KOMENTO_THEMES . DIRECTORY_SEPARATOR . $this->_theme . DIRECTORY_SEPARATOR . $file;
		$parentPath		= KOMENTO_THEMES . DIRECTORY_SEPARATOR . $info->get( 'parent' ) . DIRECTORY_SEPARATOR . $file;
		$defaultPath	= KOMENTO_THEMES . DIRECTORY_SEPARATOR . KOMENTO_THEME_BASE . DIRECTORY_SEPARATOR . $file;

		// 1. Template override
		if( $config->get( 'layout_template_override', true ) && JFile::exists( $overridePath ) )
		{
			$path	= $overridePath;
		}
		// 2. Component override
		elseif( $config->get( 'layout_component_override', true ) && JFile::exists( $componentPath ) )
		{
			$path	= $componentPath;
		}
		// 3. Selected themes
		elseif( JFile::exists( $selectedPath ) )
		{
			$path	= $selectedPath;
		}
		// 4. Parent themes
		elseif( JFile::exists( $parentPath ) )
		{
			$path	= $parentPath;
		}
		// 5. Default system theme
		else
		{
			$path	= $defaultPath;
		}

		return $path;
	}

	/**
	 * Renders a nice checkbox switch.
	 *
	 * @param	string	$option		Name attribute for the checkbox.
	 * @param	string	$sate		State of the checkbox, checked or not.
	 * @return	string	HTML output.
	 */
	public function renderCheckbox( $option , $state )
	{
		ob_start();
	?>
		<div class="si-optiontap">
			<label class="option-enable<?php echo $state == 1 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_NO_SWITCH' );?></span></label>
			<label class="option-disable<?php echo $state == 0 ? ' selected' : '';?>"><span><?php echo JText::_( 'COM_KOMENTO_YES_SWITCH' ); ?></span></label>
			<input name="<?php echo $option; ?>" value="<?php echo $state;?>" type="radio" class="radiobox" checked="checked" style="display: none;" />
		</div>
	<?php
		$html	= ob_get_contents();
		ob_end_clean();

		return $html;
	}

	public function json_encode( $value )
	{
		if ($this->_json === null)
		{
			include_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
			$this->_json	= new KomentoJson();
		}

		return $this->_json->encode( $value );
	}

	public function json_decode( $value )
	{
		if ($this->_json === null)
		{
			include_once( KOMENTO_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );
			$this->_json	= new KomentoJson();
		}

		return $this->_json->decode( $value );
	}

	public function escape( $val )
	{
		return KomentoStringHelper::escape( $val );
	}

	public function getThemeInfo( $name )
	{
		if (empty($this->_themeInfo[$name]))
		{
			$mainframe	= JFactory::getApplication();
			$file		= '';

			// We need to specify if the template override folder also have config.ini file
			if ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'config.ini' ) )
			{
				$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'config.ini';
			}

			// then check the current theme folder
			elseif ( JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento'. DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini' ) )
			{
				$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_komento' . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.ini';
			}

			if( !empty( $file ) )
			{
				$this->_themeInfo[$name] = Komento::getRegistry( JFile::read( $file ) );
			}
			else{
				$this->_themeInfo[$name] = Komento::getRegistry( '' );
			}
		}

		return $this->_themeInfo[$name];
	}
}
