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

class EasyDiscussThemesHelperEmail
{
	/**
	 * Generates an attachment in the e-mail content
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function attachment($attachment)
	{
		$theme = ED::themes();
		$theme->set('attachment', $attachment);
		$html = $theme->output('site/emails/helpers/attachment');
		
		return $html;
	}

	/**
	 * Generates a button in the e-mail template
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function button($text, $link)
	{
		$theme = ED::themes();
		$theme->set('text', $text);
		$theme->set('link', $link);
		$html = $theme->output('site/emails/helpers/button');

		return $html;
	}

	/**
	 * Generates the divider section of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function divider()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/emails/helpers/divider');
		}

		return $html;
	}
	
	/**
	 * Generates the footer section of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function footer()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/emails/helpers/footer');
		}

		return $html;
	}

	/**
	 * Generates the heading section of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function heading($title, $subtitle = '')
	{
		$theme = ED::themes();
		$theme->set('title', $title);
		$theme->set('subtitle', $subtitle);

		$html = $theme->output('site/emails/helpers/heading');

		return $html;
	}

	/**
	 * Generates the logo section of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function logo($logo)
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$theme->set('logo', $logo);
			$html = $theme->output('site/emails/helpers/logo');
		}

		return $html;
	}

	/**
	 * Generates the preview of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function preview()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/emails/helpers/preview');
		}

		return $html;
	}

	/**
	 * Generates the logo section of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replySeparator($text)
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$theme->set('text', $text);
			$html = $theme->output('site/emails/helpers/reply.separator');
		}

		return $html;
	}

	/**
	 * Generates a section heading in an e-mail
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function sectionHeading($title, $subtitle = '')
	{
		$theme = ED::themes();
		$theme->set('title', $title);
		$theme->set('subtitle', $subtitle);
		$html = $theme->output('site/emails/helpers/section.heading');

		return $html;
	}

	/**
	 * Generates the logo section of an e-mail 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function spacer()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/emails/helpers/spacer');
		}

		return $html;
	}
}