<?php
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

require_once DISCUSS_HELPERS . '/date.php';
require_once DISCUSS_HELPERS . '/input.php';

class EasyDiscussControllerSettings extends EasyDiscussController
{
	public function apply()
	{
		JRequest::checkToken('request') or jexit( 'Invalid Token' );

		$app 		= JFactory::getApplication();

		$result		= $this->_store();
		$layout		= JRequest::getString( 'layout' , '' );
		$child		= JRequest::getString( 'child' , '' );

		DiscussHelper::setMessageQueue( $result[ 'message' ] , $result[ 'type' ] );

		$app->redirect( 'index.php?option=com_easydiscuss&view=settings&layout=' . $layout . '&child=' . $child );
		$app->close();
	}

	private function _store()
	{
		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'success';

		if( JRequest::getMethod() == 'POST' )
		{
			$model		= $this->getModel( 'Settings' );

			$postArray	= JRequest::get( 'post' );
			$saveData	= array();

			// Unset unecessary data.
			unset( $postArray['controller'] );
			unset( $postArray['active'] );
			unset( $postArray['child'] );
			unset( $postArray['layout'] );
			unset( $postArray['task'] );
			unset( $postArray['option'] );
			unset( $postArray['c'] );

			$token = DiscussHelper::getToken();
			unset( $postArray[$token] );

			foreach( $postArray as $index => $value )
			{
				// Filter out the dummy checkbox_display_xxx entry
				if( substr($index, 0, 17) == 'checkbox_display_' )
				{
					continue;
				}

				if( $index == 'integration_google_adsense_code' )
				{
					$value	= str_ireplace( ';"' , ';' , $value );
				}

				if( $index != 'task' );
				{
					$saveData[ $index ]	= $value;
				}

				if( is_array( $value ) )
				{
					$saveData[ $index ]	= implode( ',' , $value );
				}
			}

			// reset the setting 'main_allowdelete' to use from configuration.ini
			$saveData['main_allowdelete'] = DiscussHelper::getDefaultConfigValue( 'main_allowdelete', '' );

			// reset the setting 'layout_featuredpost_style' to always use from configuration.ini
			$saveData['layout_featuredpost_style'] = DiscussHelper::getDefaultConfigValue( 'layout_featuredpost_style', '0' );

			if( $model->save( $saveData ) )
			{
				$message	= JText::_( 'COM_EASYDISCUSS_CONFIGURATION_SAVED' );
			}
			else
			{
				$message	= JText::_( 'COM_EASYDISCUSS_CONFIGURATION_SAVE_ERROR' );
				$type		= 'error';
			}
		}
		else
		{
			$message	= JText::_('COM_EASYDISCUSS_INVALID_FORM_METHOD');
			$type		= 'error';
		}

		return array( 'message' => $message , 'type' => $type);
	}

	/**
	* Save the Email Template.
	*/
	function saveEmailTemplate()
	{
		$mainframe 	= JFactory::getApplication();
		$file 		= JRequest::getVar('file', '', 'POST' );
		$filepath	= DISCUSS_SITE_THEMES . '/simplistic/emails/' . $file;
		$content	= JRequest::getVar( 'content' , '' , 'POST' , '' , JREQUEST_ALLOWRAW );
		$msg		= '';
		$msgType	= '';

		$status 	= JFile::write($filepath, $content);

		if(!empty($status))
		{
			$msg = JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_SUCCESS');
			$msgType = 'success';
		}
		else
		{
			$msg = JText::_('COM_EASYDISCUSS_SETTINGS_NOTIFICATIONS_EMAIL_TEMPLATES_SAVE_FAIL');
			$msgType = 'error';
		}

		DiscussHelper::setMessageQueue( $msg , $msgType );
		$mainframe->redirect('index.php?option=com_easydiscuss&view=settings&layout=editEmailTemplate&file='.$file.'&msg='.$msg.'&msgtype='.$msgType.'&tmpl=component&browse=1');
	}
}
