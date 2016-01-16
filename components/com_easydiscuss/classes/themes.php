<?php
/*--------------------------------------------------------------*\
	Description:	HTML template class.
	Author:			Brian Lozier (brian@massassi.net)
	License:		Please read the license.txt file.
	Last Updated:	11/27/2002
\*--------------------------------------------------------------*/

/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
require_once DISCUSS_HELPERS . '/integrate.php';
require_once DISCUSS_HELPERS . '/string.php';
require_once DISCUSS_HELPERS . '/tooltip.php';
require_once DISCUSS_HELPERS . '/date.php';


if( !class_exists( 'DiscussThemes' ) )
{
	class DiscussThemes
	{
		var $vars; /// Holds all the template variables

		static $users	= array();

		/**
		 * Pass theme name from config
		 */
		function DiscussThemes()
		{
			$config 	= DiscussHelper::getConfig();
			$system		= new stdClass();

			if( !isset( $this->vars['system'] ) )
			{
				$my			= JFactory::getUser();

				if( !isset( self::$users[ $my->id ] ) )
				{
					$profile	= DiscussHelper::getTable( 'Profile' );
					$profile->load($my->id);

					self::$users[ $my->id ]	= $profile;
				}

				$system->config			= $config;
				$system->my				= $my;
				$system->profile		= self::$users[ $my->id ];
				$system->acl			= DiscussHelper::getHelper( 'ACL' );
				$this->vars['acl']		= $system->acl;
				$this->vars['system']	= $system;
			}

			$this->_theme = $config->get( 'layout_site_theme' );
		}

		function getNouns( $text , $count , $includeCount = false )
		{
			return DiscussHelper::getHelper( 'String' )->getNoun( $text , $count , $includeCount );
		}

		function chopString( $string , $length )
		{
			return JString::substr( $string , 0 , $length );
		}

		function getUserTooltip( $id , $name )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load($id);

			ob_start();
			?>
			<div>
				<strong><u><?php echo $name;?></u></strong>
				<img src='<?php echo $profile->getAvatar();?>' width='32' />
			</div>
			<p>
				<?php echo JText::sprintf('COM_EASYDISCUSS_TOOLTIPS_USER_INFO', $name , $profile->getDateJoined() ,  $profile->numPostCreated, $profile->numPostAnswered); ?>
			</p>
			<?php
			$content	= ob_get_contents();
			ob_end_clean();

			return $content;
		}

		function formatDate( $format , $dateString )
		{
			$date	= DiscussDateHelper::dateWithOffSet($dateString);


			return DiscussDateHelper::toFormat($date, $format);
		}

		/**
		 * Set a template variable.
		 */
		public function set($name, $value = null )
		{
			$this->vars[$name] = $value;
		}

		public function loadTemplate( $file = null , $customVars = array() )
		{
			return $this->_includeFile( $file , $customVars );
		}

		private function getFilePath( $fileName )
		{
			jimport( 'joomla.filesystem.file' );

			$app 		= JFactory::getApplication();
			$template	= $app->getTemplate();
			$config 	= DiscussHelper::getConfig();
			$theme 		= JRequest::getCmd( 'theme' );

			/**
			 * Ordering:
			 * 1. Check for theme in query string theme=xxx
			 * 2. Check for template overrides in Joomla /templates/TEMPLATE_NAME/html/com_easydiscuss/
			 * 3. Check for current configured template. /components/com_easydiscuss/themes/CONFIGURED_THEME/
			 * 4. Load from /components/com_easydiscuss/themes/simplistic/
			 */
			$file 	= DISCUSS_SITE_THEMES . '/' . strtolower( $theme ) . '/' . $fileName;

			// 1. Check for theme in query string.
			if( JFile::exists( $file ) && !empty( $theme ) )
			{
				return $file;
			}

			// 2. Check for template override.
			$file 	= JPATH_ROOT . '/templates/' . strtolower( $template ) . '/html/com_easydiscuss/' . $fileName;

			if( JFile::exists( $file ) )
			{
				return $file;
			}

			// 3. Check for current configured theme.
			$file 	= DISCUSS_SITE_THEMES . '/' . strtolower( $config->get( 'layout_site_theme' ) ) . '/' . $fileName;

			if( JFile::exists( $file ) )
			{
				return $file;
			}

			// 4. Load simplistic file.
			$file 	= DISCUSS_SITE_THEMES . '/simplistic/' . $fileName;

			return $file;
		}

		private function _includeFile( $file , $customVars = array() )
		{
			jimport( 'joomla.filesystem.file' );

			// Get the file path.
			$file 	= $this->getFilePath( $file );

			if( isset( $customVars ) )
			{
				extract( $customVars );
			}

			if( isset( $this->vars ) )
			{
				if( isset( $customVars ) )
				{
					extract($this->vars , EXTR_SKIP );
				}
				else
				{
					extract( $this->vars );
				}
			}

			$data	= '';
			if( !JFile::exists( $file ) )
			{
				$data	= JText::sprintf( 'COM_EASYDISCUSS_INVALID_TEMPLATE_FILE' , $file );
			}
			else
			{
				ob_start();
				include($file);
				$data	= ob_get_contents();
				ob_end_clean();
			}

			// Test if the js equivalent file exists.
			$jsFile 	= str_ireplace( '.php' , '.js' , $file );

			if( JFile::exists( $jsFile ) )
			{
				ob_start();
				include( $jsFile );
				$jsData 	= ob_get_contents();
				ob_end_clean();

				$data 	.= '<script type="text/javascript">' . $jsData . '</script>';
			}

			return $data;
		}

		/**
		 * Open, parse, and return the template file.
		 *
		 * @param $file string the template file name
		 */
		function fetch( $file , $options = array() )
		{
			if( isset( $options[ 'dialog' ] ) )
			{
				$file 	= 'dialogs/' . $file;
			}

			if( isset( $options[ 'emails' ] ) )
			{
				$file 	= 'emails/' . $file;
			}

			return $this->_includeFile( $file );
		}

		function getUnansweredCount( $tagId = '0' )
		{
			$db		= DiscussHelper::getDBO();

			$query	= 'SELECT COUNT(a.`id`) FROM `#__discuss_posts` AS a';
			$query	.= '  LEFT JOIN `#__discuss_posts` AS b';
			$query	.= '    ON a.`id`=b.`parent_id`';
			$query	.= '    AND b.`published`=' . $db->Quote('1');

			if(! empty($tagId))
			{
				$query	.= ' INNER JOIN `#__discuss_posts_tags` as c';
				$query	.= ' 	ON a.`id` = c.`post_id`';
				$query	.= ' 	AND c.`tag_id` = ' . $db->Quote($tagId);
			}

			$query	.= ' WHERE a.`parent_id` = ' . $db->Quote('0');
			$query	.= ' AND a.`published`=' . $db->Quote('1');
			$query	.= ' AND b.`id` IS NULL';


			$db->setQuery( $query );

			return $db->loadResult();
		}

		/**
		 * @deprecated	Since 3.0 . No longer in use.
		 */
		function getFeaturedCount($tagId = '0')
		{

			$db = DiscussHelper::getDBO();

			$query  = 'SELECT COUNT(1) as `CNT` FROM `#__discuss_posts` AS a';
			if(! empty($tagId)){
				$query  .= ' INNER JOIN `#__discuss_posts_tags` AS b ON a.`id` = b.`post_id`';
				$query  .= ' AND b.`tag_id` = ' . $db->Quote($tagId);
			}

			$query  .= ' WHERE a.`featured` = ' . $db->Quote('1');
			$query  .= ' AND a.`parent_id` = ' . $db->Quote('0');
			$query  .= ' AND a.`published` = ' . $db->Quote('1');

			$db->setQuery($query);

			$result = $db->loadResult();

			return $result;
		}


		public function resolve( $namespace )
		{
			$parts 	= explode( '/' , $namespace );
			$path 	= '';

			return $path;
		}

		function json_encode( $value )
		{
			include_once( DISCUSS_CLASSES . '/json.php' );
			$json	= new Services_JSON();

			return $json->encode( $value );
		}

		function json_decode( $value )
		{
			include_once( DISCUSS_CLASSES . '/json.php' );
			$json	= new Services_JSON();

			return $json->decode( $value );
		}

		private function getFieldContents( $files , $isDiscussion = false , $post = null )
		{
			$contents 	= '';

			if( isset( $this->vars ) )
			{
				extract($this->vars , EXTR_SKIP );
			}

			foreach( $files as $file )
			{
				ob_start();
				include( $file );
				$contents 	.= ob_get_contents();
				ob_end_clean();
			}

			return $contents;
		}

		public function getFieldFiles( $pattern )
		{
			$app 			= JFactory::getApplication();
			$override 		= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss';

			$files			= array();
			$includedFiles	= array();

			if( JFolder::exists( $override ) )
			{
				$extraFiles		= JFolder::files( $override , $pattern );

				if( $extraFiles )
				{
					foreach( $extraFiles as $file )
					{
						if( !in_array( $file , $includedFiles ) )
						{
							$files[]			= $override . '/' . $file;

							$includedFiles[]	= $file;
						}
					}
				}
			}

			$theme 		= JPATH_ROOT . '/components/com_easydiscuss/themes/' . $this->_theme;
			$extraFiles	= JFolder::files( $theme , $pattern );

			if( $extraFiles )
			{
				foreach( $extraFiles as $file )
				{
					if( !in_array( $file , $includedFiles ) )
					{
						$files[]			= $theme . '/' . $file;

						$includedFiles[]	= $file;
					}
				}
			}

			if( $this->_theme != 'simplistic' ) // can we get value from layout_site_theme_base ?
			{
				$theme			= JPATH_ROOT . '/components/com_easydiscuss/themes/simplistic';
				$extraFiles		= JFolder::files( $theme , $pattern );

				if( $extraFiles )
				{
					foreach( $extraFiles as $file )
					{
						if( !in_array( $file , $includedFiles ) )
						{
							$files[]			= $theme . '/' . $file;

							$includedFiles[]	= $file;
						}
					}
				}
			}

			return $files;
		}

		public function getFieldForms( $isDiscussion = false , $postObj = false )
		{
			$app 		= JFactory::getApplication();
			$override 	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss';
			$theme 		= JPATH_ROOT . '/components/com_easydiscuss/themes/' . $this->_theme;
			$pattern 	= 'field.form.(.*).php';

			$files		= $this->getFieldFiles( $pattern );
			$contents 	= '';

			if( $files )
			{
				$contents .= $this->getFieldContents( $files , $isDiscussion , $postObj );
			}

			return $contents;
		}

		public function getFieldHTML( $isDiscussion = false , $postObj = false )
		{
			$app 		= JFactory::getApplication();
			$override 	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss';
			$theme 		= JPATH_ROOT . '/components/com_easydiscuss/themes/' . $this->_theme;
			$pattern 	= 'field\.output\.(.*)\.php';

			$files		= $this->getFieldFiles( $pattern );

			$contents 	= '';

			if( $files )
			{
				$contents .= $this->getFieldContents( $files , $isDiscussion , $postObj );
			}

			return $contents;
		}

		public function getFieldTabs( $isDiscussion = false , $postObj = false )
		{
			$app 		= JFactory::getApplication();
			$override 	= JPATH_ROOT . '/templates/' . $app->getTemplate() . '/html/com_easydiscuss';
			$theme 		= JPATH_ROOT . '/components/com_easydiscuss/themes/' . $this->_theme;
			$pattern 	= 'field.tab.(.*).php';

			$files		= $this->getFieldFiles( $pattern );
			$contents 	= '';

			if( $files )
			{
				$contents .= $this->getFieldContents( $files , $isDiscussion , $postObj );
			}

			return $contents;
		}

		public function getFieldData( $fieldName , $params )
		{
			$data 		= array();
			$fieldName 	= (string) $fieldName;
			$pattern 	= '/params_' . $fieldName . '[0-9]?=(.*)/i';

			if( DiscussHelper::getJoomlaVersion() >= '1.6' )
			{
				$pattern 	= '/params_' . $fieldName . '[0-9]?=["](.*)["]/i';
			}

			preg_match_all( $pattern , $params , $matches );

			if( !empty( $matches[1] ) )
			{
				foreach( $matches[1] as $match )
				{
					$data[]		= $match;
				}

				return $data;
			}

			return false;
		}

		public function escape( $text = '' )
		{
			$stringHelper 	= DiscussHelper::getHelper( 'String' );
			return $stringHelper->escape( $text );
		}
		
		public function getRecaptcha()
		{
			require_once DISCUSS_CLASSES . '/recaptcha.php';

			if( DiscussRecaptcha::isRequired() )
			{
				$config 	= DiscussHelper::getConfig();
				$recaptcha	= getRecaptchaData( $config->get( 'antispam_recaptcha_public' ) , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') );

				return $recaptcha;
			}

			return false;
		}
	}
}
