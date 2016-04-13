<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/abstract.php');

class EasyDiscussCaptchaDefault extends EasyDiscussCaptchaAbstract
{
	/**
	 * Validates the response
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function validate($data = array())
	{
		// Data is always passed in the form of $data["captcha-id"] and $data["captcha-response"]
		$id = isset($data["captcha-id"]) ? $data["captcha-id"] : "";
		$response = isset($data["captcha-response"]) ? $data["captcha-response"] : "";

		// Ensure that we have the necessary data to validate
		if (!$id || !$response) {
			return false;
		}

		$table = ED::table('Captcha');
		$table->load($id);

		// Verify the response
		if (!$table->response || $table->response != $response) {
			return false;
		}

		// Once the captcha is verified, delete it now.
		$table->delete();
		
		return true;
	}

	/**
	 * Loads an existing captcha table
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function load($id)
	{
		$this->table = ED::table('Captcha');
		$state = $this->table->load($id);

		return $state;
	}

	/**
	 * Reloads the captcha
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function reload($previousCaptchaId = null)
	{
		// Delete the previous captcha that was generated on the page
		if ($previousCaptchaId) {
			$previous = ED::table('Captcha');
			$exists = $previous->load($previousCaptchaId);

			if ($exists) {
				$previous->delete();
			}
		}

		// Generate a new captcha now
		$this->table = ED::table('Captcha');
		$this->table->created = ED::date()->toSql();
		$this->table->store();

		return $this->table;
	}

	/**
	 * Retrieves the image source
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getImageSource()
	{
		$source = JURI::root() . 'index.php?option=com_easydiscuss&controller=captcha&task=generate&id=' . $this->table->id . '&no_html=1&tmpl=component';

		return $source;
	}

	/**
	 * Generates the html code for captcha
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function html()
	{
		$this->table = ED::table('Captcha');
		$this->table->created = ED::date()->toSql();
		$this->table->store();

		$theme = ED::themes();
		$theme->set('table', $this->table);
		$theme->set('source', $this->getImageSource());

		$output = $theme->output('site/captcha/default');

		return $output;
	}

	/**
	 * Clear expired captcha keys
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function clearExpired()
	{
		$model = ED::model('Captcha');

		return $model->clearExpired();
	}

	/**
	 * Generates a new hash for the current captcha record
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function generateHash()
	{
		// Generate a very random integer and take only 5 chars max.
		$hash = substr(md5(rand(0, 9999)), 0, 5);

	    $this->table->response = $hash;
		
		return $this->table->store();
	}

	/**
	 * Draws an image and returns the resource
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function drawImage($width = 100, $height = 20)
	{
		// Get the hash
		$hash = $this->table->response;

		// Create a blank canvas first
	    $image = imagecreate($width, $height);

	    // Color definitions
	    $white = imagecolorallocate($image, 255, 255, 255);
	    $black = imagecolorallocate($image, 0, 0, 0);
	    $gray = imagecolorallocate($image, 204, 204, 204);

	    imagefill($image , 0 , 0 , $white );
		imagestring($image, 5, 30, 3, $hash, $black);
		imagerectangle($image, 0 , 0 , $width - 1 , $height - 1 , $gray);
		imageline($image, 0 , $height / 2 , $width , $height / 2 , $gray);
		imageline($image, $width / 2 , 0 , $width / 2, $height, $gray);

		return $image;
	}
	
	/**
	 * Performs validation of captcha
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public static function validateCaptcha($data)
	{
		require_once(DISCUSS_CLASSES . '/recaptcha.php');
		require_once(DISCUSS_CLASSES . '/captcha.php');

		$config 	= DiscussHelper::getConfig();

		if ($config->get('antispam_easydiscuss_captcha')) {

			// If captcha is not enforced, we should allow this to bypass
			if (!DiscussHelper::getHelper('Captcha')->showCaptcha()) {
				return true;
			}

			$discussCaptcha	= new stdClass();
			$discussCaptcha->captchaResponse	= JRequest::getVar('captcha-response');;
			$discussCaptcha->captchaId			= JRequest::getInt('captcha-id');

			$state = DiscussHelper::getHelper( 'Captcha' )->verify( $discussCaptcha );

			return $state;
		}

		if ( !DiscussRecaptcha::isRequired()) {
			return true;
		}

		$obj = DiscussRecaptcha::recaptcha_check_answer( $config->get( 'antispam_recaptcha_private' ) , $_SERVER['REMOTE_ADDR'] , $data['recaptcha_challenge_field'] , $data['recaptcha_response_field'] );

		if ($obj->is_valid) {
			return true;
		}

		return false;
	}

	// /**
	//  * Reload the captcha image.
	//  * @param	Ejax	$ejax	Ejax object
	//  * @return	string	The javascript action to reload the image.
	//  **/
	// public function reload( $ajax , $captchaId )
	// {
	// 	$config		= DiscussHelper::getConfig();

	// 	// If no captcha is enabled, ignore it.
	// 	if( !$config->get('antispam_easydiscuss_captcha_registered') || !$config->get( 'antispam_easydiscuss_captcha' ) )
	// 	{
	// 		return true;
	// 	}

	// 	// @task: If recaptcha is not enabled, we assume that the built in captcha is used.
	// 	// Generate a new captcha 
	// 	if( isset( $captchaId ) )
	// 	{
	// 		$ref	= DiscussHelper::getTable( 'Captcha' );
	// 		$ref->load( $captchaId );
	// 		$ref->delete();
	// 	}

	// 	require_once DISCUSS_CLASSES . DIRECTORY_SEPARATOR . 'captcha.php';
	// 	$ajax->script( DiscussCaptchaClasses::getReloadScript( $ajax , $captchaId ) );
	// 	return true;
	// }

	public function showCaptcha()
	{
		$config = ED::getConfig();
		$my = JFactory::getUser();
		$runCaptcha = false;

		if ($config->get('antispam_easydiscuss_captcha')) {

			// Check to see if user is guest or registered
			if (empty($my->id)) {

				// If is guest
				$runCaptcha = true;

			} else {
				
				//If not guest, check the settings
				if ($config->get( 'antispam_easydiscuss_captcha_registered')) {
					$runCaptcha = true;
				}
			}
		}

		return $runCaptcha;
	}


	// public function getError( $ajax , $post )
	// {
	// 	$ajax->script( DiscussCaptcha::getReloadScript( $ajax, $post ) );
	// 	// $ajax->script( 'eblog.comment.displayInlineMsg( "error" , "'.JText::_('COM_EASYBLOG_CAPTCHA_INVALID_RESPONSE').'");' );
	// 	// $ajax->script( 'eblog.spinner.hide();' );
	// 	// $ajax->script( "eblog.loader.doneLoading();" );
	// 	return $ajax->send();
	// }

	public function getReloadScript( $ajax , $captchaId )
	{
		JTable::addIncludePath( DISCUSS_TABLES );

		if( isset( $captchaId ) )
		{
			$ref	= DiscussHelper::getTable( 'Captcha' );
			$ref->load( $captchaId );
			$ref->delete();
		}

		//return 'eblog.captcha.reload();';
		return;
	}

}