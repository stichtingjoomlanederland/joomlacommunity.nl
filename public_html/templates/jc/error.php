<?php
/*
 * @package		perfecttemplate
 * @copyright	Copyright (c) Perfect Web Team / perfectwebteam.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

if (($this->error->getCode()) == '404')
{
	header("HTTP/1.0 404 Not Found");
	switch (JFactory::getLanguage()->getTag())
	{
		#case 'en-GB':
		#	echo file_get_contents(JURI::base().'en/404');
		#	break;

		default:
			echo file_get_contents(JURI::base() . '404');
	}
	exit;
}

if (($this->error->getCode()) == '403')
{
	header("HTTP/1.0 403 Forbidden");
	switch (JFactory::getLanguage()->getTag())
	{
		#case 'en-GB':
		#	echo file_get_contents(JURI::base().'en/404');
		#	break;

		default:
			echo file_get_contents(JURI::base() . '404');
	}
	exit;
}