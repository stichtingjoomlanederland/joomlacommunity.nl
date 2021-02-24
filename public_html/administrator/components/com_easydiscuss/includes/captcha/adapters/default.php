<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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

	public function showCaptcha()
	{
		$config = ED::config();
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

	public function getReloadScript( $ajax , $captchaId )
	{
		JTable::addIncludePath( DISCUSS_TABLES );

		if( isset( $captchaId ) )
		{
			$ref = ED::table('Captcha');
			$ref->load( $captchaId );
			$ref->delete();
		}

		//return 'eblog.captcha.reload();';
		return;
	}
}