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

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once DISCUSS_CLASSES . '/facebook/consumer.php';

class DiscussFacebook extends Facebook
{
	var $callback		= '';
	var $_access_token	= '';

	public function __construct( $key , $secret , $callback )
	{
		$this->callback	= $callback;

		parent::__construct( array( 'appId' 	=> $key ,
									'secret'	=> $secret,
									'cookie'	=> true
							)
		);
	}

	/**
	 * Facebook does not need the request tokens
	 *
	 **/
	public function getRequestToken()
	{
		$obj		= new stdClass();
		$obj->token		= 'facebook';
		$obj->secret	= 'facebook';

		return $obj;
	}

	/**
	 * Returns the verifier option. Since Facebook does not have oauth_verifier,
	 * The only way to validate this is through the 'code' query
	 *
	 * @return string	$verifier	Any string representation that we can verify it isn't empty.
	 **/
	public function getVerifier()
	{
		$verifier	= JRequest::getVar( 'code' , '' );
		return $verifier;
	}

	/**
	 * Returns the authorization url.
	 *
	 * @return string	$url	A link to Facebook's login URL.
	 **/
	public function getAuthorizationURL()
	{
		$scope	= array(
							'publish_actions',
							'user_likes',
							//'offline_access', since the Facebook API 2.0 already removed this user permission
							'publish_pages', //since Facebook API 2.0 already added this new user permission required when the user want to autopost to FB page
							'manage_pages',
							'user_status'
						);
		$url	= 'http://facebook.com/dialog/oauth?scope=' . implode( ',' , $scope ) . '&client_id=' . parent::getAppId() . '&redirect_uri=' . urlencode( $this->callback ) . '&response_type=code';

		return $url;
	}

	public function getAccess( $token , $secret , $verifier )
	{
		$code		= JRequest::getVar( 'code' );
		$params		= array( 'client_id' 	=> parent::getAppId() ,
							 'redirect_uri'	=> $this->callback,
							 'client_secret'=> parent::getApiSecret(),
							 'code'			=> $code
							);

		$token		= parent::_oauthRequest( parent::getUrl('graph', '/oauth/access_token' ) , $params );
		$token		= str_ireplace( 'access_token=' , '' , $token );
		$obj		= new stdClass();
		$obj->token	= $token;
		$obj->secret= 'facebook';
		$obj->params= '';

		return $obj;
	}

	/**
	 * Shares a new content on Facebook
	 **/
	public function share( $post )
	{
		$config		= DiscussHelper::getConfig();
		$content	= $post->content;
		$content	= EasyDiscussParser::bbcode( $content );

		JFactory::getLanguage()->load( 'com_easydiscuss' , JPATH_ROOT );

		$editor = DiscussHelper::getEditorType( 'question' );

		if( $editor == 'html' )
		{
			// @rule: Match images from content
			$pattern	= '/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i';
		}
		else
		{
			$pattern 	= '/\[img\](.*?)\[\/img\]/ims';
		}

		preg_match( $pattern , $content , $matches );

		$image		= '';

		if( $matches )
		{
			$image		= isset( $matches[1] ) ? $matches[1] : '';

			if( JString::stristr( $matches[1], 'https://' ) === false && JString::stristr( $matches[1], 'http://' ) === false && !empty( $image ) )
			{
				$image	= DISCUSS_JURIROOT . '/' . ltrim( $image, '/');
			}
		}

		$text		= strip_tags(html_entity_decode($content));

		// @TODO: Configurable content length.
		$maxLength	= 200;
		$text		= ( JString::strlen( $text ) > $maxLength ) ? JString::substr( $text, 0, $maxLength) . '...' : $text;
		$url		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $post->id , false, true );

		$this->_access_token 	= preg_replace( '/&expires=.*/i' , '' , $this->_access_token );

		$jConfig	= DiscussHelper::getJConfig();
		$params		= array(
							'link' 			=>  $url,
							'name'			=> $post->title,
							'actions'		=> '{"name": "' . JText::_( 'COM_EASYDISCUSS_AUTOPOST_FB_VIEWON_BUTTON' ) . '", "link" : "' . $url . '"}',
							'description' 	=> $text,
							'message' 		=> JString::substr( strip_tags( $text ), 0, 30 ) . '...',
							'access_token' 	=> $this->_access_token
							);

		if( !empty( $image ) )
		{
			// Since Facebook does not allow https images we need to replace them here.
			$params[ 'picture' ] 	= str_ireplace( 'https://' , 'http://' , $image );
		}
		else
		{
			$params['picture' ]		= DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_facebook.png';
			$params['source' ]		= rtrim( JURI::root() , '/' ) . '/media/com_easydiscuss/images/default_facebook.png';
		}

		// @rule: See if we need to post this to a Facebook page instead.
		$pageId			= $config->get( 'main_autopost_facebook_page_id' );

		if( !empty( $pageId ) )
		{
			$pages	= JString::trim( $pageId );
			$pages	= explode( ',' , $pages );
			$total	= count( $pages );

			// @rule: Test if there are any pages at all the user can access
			$accounts	= parent::api( '/me/accounts' , array( 'access_token' => $this->_access_token ) );

			if( is_array( $accounts ) && isset( $accounts[ 'data' ] ) )
			{
				for( $i = 0; $i < $total; $i++ )
				{
					foreach( $accounts[ 'data' ] as $page )
					{
						if( $page[ 'id' ] == $pages[ $i ] )
						{
							$params['access_token']	= $page[ 'access_token' ];
							$query	= parent::api( '/' . $page[ 'id' ] . '/feed' , 'post' , $params );
						}
					}
				}
			}

		}
		else
		{
			// @rule: If this is just a normal posting, just post it on their page.
			$query		= parent::api( '/me/feed' , 'post' , $params );
		}

		$success	= isset( $query['id'] ) ? true : false;

		return $success;
	}

	function findItem($needle, $haystack, $partial_matches = false, $search_keys = false)
	{
		if(!is_array($haystack)) return false;
		foreach($haystack as $key=>$value) {
			$what = ($search_keys) ? $key : $value;
			if($needle===$what) return $key;
			else if($partial_matches && @strpos($what, $needle)!==false) return $key;
			else if(is_array($value) && self::findItem($needle, $value, $partial_matches, $search_keys)!==false) return $key;
		}
		return false;
	}

	public function setAccess( $access )
	{
		$access	= DiscussHelper::getRegistry( $access );

		$this->_access_token	= $access->get( 'token' );
		return true;
	}

	public function revokeApp()
	{
		return true;
	}
}
