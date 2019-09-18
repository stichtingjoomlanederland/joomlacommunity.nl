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

class EasyDiscussIntegrate extends EasyDiscuss
{
	/**
	 * Get the profile and avatar link of a user.
	 *
	 * @param	object	$profile	The JUser object, defaults to null.
	 *
	 * @return	array	$field		An array consists of key avatarLink and profileLink
	 */
	public function getField($profile = null, $isThumb = true)
	{
		//@rule: For guest, we use default avatar.
		if (is_null($profile) || !is_object($profile) || !isset($profile->id) || $profile->id == 0) {
			$field['avatarLink'] = DISCUSS_JURIROOT . '/media/com_easydiscuss/images/default_avatar.png';
			$field['profileLink'] = '#';

			return $field;
		}

		static $field;
		$index = $profile->id . (int) $isThumb;

		if (!isset($field[$index])) {
			$config = ED::config();
			$integration = strtolower($config->get('layout_avatarIntegration', 'default'));

			switch ($integration) {
				case 'jomsocial' :
					$socialFields = self::jomsocial( $profile, $isThumb );
					break;
				case 'kunena' :
					$socialFields = self::kunena( $profile );
					break;
				case 'communitybuilder' :
					$socialFields = self::communitybuilder( $profile , $isThumb );
					break;
				case 'gravatar' :
					$socialFields = self::gravatar( $profile );
					break;
				case 'phpbb' :
					$socialFields = self::phpbb( $profile );
					break;
				case 'anahita':
					$socialFields = self::anahita( $profile );
					break;
				case 'easyblog' :
					$socialFields = self::easyblog( $profile );
					break;
				case 'jfbconnect':
					$socialFields = self::jfbconnect($profile);
					break;
				case 'k2':
					$socialFields = self::k2( $profile );
					break;
				case 'easysocial':
					$socialFields = self::easysocial( $profile );
					break;
				case 'jomwall':
					$socialFields = self::jomwall($profile);
					break;
				case 'jsn':
					$socialFields = self::jsn($profile);
					break;
				case 'easydiscuss' :
				default :
					$socialFields = self::easydiscuss( $profile );
					break;
			}

			if (empty($socialFields) || empty($socialFields[0]) || empty($socialFields[1])) {
				$socialFields = self::easydiscuss( $profile );
			}

			$editProfileLink = '';

			if (isset($socialFields[2]) && $socialFields[2]) {
				$editProfileLink = $socialFields[2];
			}

			$avatarData = array('avatarLink' => $socialFields[0], 'profileLink' => $socialFields[1], 'editProfileLink' => $editProfileLink);
			$field[$index] = $avatarData;
		}

		return $field[$index];
	}

	private static function easydiscuss($profile)
	{
		$legacy	= ($profile->avatar == 'default_avatar.png' || $profile->avatar == 'default.png' || $profile->avatar == 'media/com_easydiscuss/images/default.png' || empty($profile->avatar));

		$avatarLink	= $legacy ? 'media/com_easydiscuss/images/default_avatar.png' : ED::image()->getAvatarRelativePath() . '/' . $profile->avatar;

		$avatarLink = rtrim(DISCUSS_JURIROOT, '/') . '/' . $avatarLink;
		$profileLink = EDR::_('index.php?option=com_easydiscuss&view=profile&id='.$profile->id, false);

		return array($avatarLink, $profileLink);
	}

	private static function jsn($profile)
	{

		$file = JPATH_ROOT . '/components/com_jsn/helpers/helper.php';

		if (!JFile::exists($file)) {
			return false;
		}
	
		require_once($file);

		$user = JsnHelper::getUser($profile->id);

		$avatarLink = '/' . $user->avatar;
		$profileLink = EDR::_('index.php?option=com_easydiscuss&view=profile&id='.$profile->id, false);

		return array($avatarLink, $profileLink);
	}
	
	private static function easysocial($profile)
	{
		if (!ED::easysocial()->exists()) {
			return false;
		}

		$config = ED::config();

		$avatarLink = ES::user($profile->id)->getAvatar(SOCIAL_AVATAR_MEDIUM);
		$profileLink = ES::user($profile->id)->getPermalink();

		$editProfileLink = '';

		if ($config->get('integration_easysocial_toolbar_profile')) {
			$editProfileLink = ESR::profile(array('layout' => 'edit'));
		}

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private static function jomwall( $profile, $isThumb = true )
	{
		$file = JPATH_ROOT . '/components/com_awdwall/helpers/user.php';

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		$avatarLink = AwdwallHelperUser::getBigAvatar51($profile->id);
		$Itemid = AwdwallHelperUser::getComItemId();
		$profileLink = AwdwallHelperUser::getUserProfileUrl($profile->id,$Itemid);

		$editProfileLink = JRoute::_('index.php?option=com_awdwall&view=mywall&wuid='. $profile->id .'&Itemid=' . $Itemid, false);
		
		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private static function k2($profile)
	{
		$file1 = JPATH_ROOT . '/components/com_k2/helpers/route.php';
		$file2 = JPATH_ROOT . '/components/com_k2/helpers/utilities.php';

		if (!JFile::exists($file1) || !JFile::exists($file2)) {
			return false;
		}

		require_once($file1);
		require_once($file2);

		$db = ED::db();
		$query = 'SELECT * FROM ' . $db->nameQuote('#__k2_users') . ' '
				. 'WHERE ' . $db->nameQuote('userID') . '=' . $db->Quote($profile->id);

		$db->setQuery($query);
		$result	= $db->loadObject();

		if (!$result || !$result->image) {
			return false;
		}

		$avatarLink = DISCUSS_JURIROOT . '/media/k2/users/' . $result->image;
		$profileLink = K2HelperRoute::getUserRoute($profile->id);

		return array( $avatarLink, $profileLink );
	}

	public static function jfbconnect( $profile )
	{
		$jfbconnect = ED::jfbconnect();

		if (!$jfbconnect->exists()) {
			return false;
		}

		$table = ED::table('profile');

		$jfbcUser = $table->getJfbconnectUserDetails($profile->id);

		if (!$jfbcUser) {
			return false;
		}

		// Get avatar
		$avatar = JFBCFactory::provider($jfbcUser->provider)->profile->getAvatarUrl($jfbcUser->id,false,null);

		// Get profile link
		$params = new JRegistry();
		$params->loadString($jfbcUser->params);
		$profileLink = $params->get('profile_url');

		return array($avatar, $profileLink);
	}

	private static function anahita($profile)
	{
		if (!class_exists('KFactory')) {
			return false;
		}

		$person = KFactory::get('lib.anahita.se.person.helper')->getPerson($profile->id);
		$profileLink = JRoute::_('index.php?option=com_socialengine&view=person&id=' . $profile->id);

		return array($person->getAvatar()->getURL(AnSeAvatar::SIZE_MEDIUM), $profileLink);
	}

	private static function jomsocial($profile, $isThumb = true)
	{
		if (!ED::jomsocial()->exists()) {
			return false;
		}

		$user = CFactory::getUser($profile->id);
		$avatarLink = ($isThumb) ? $user->getThumbAvatar() : $user->getAvatar();

		$profileLink = CRoute::_('index.php?option=com_community&view=profile&userid=' . $profile->id);
		$editProfileLink = CRoute::_('index.php?option=com_community&view=profile&task=edit');

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private static function kunena($profile)
	{
		if (!class_exists('KunenaFactory')) {
			return false;
		}

		$userKNN = KunenaFactory::getUser($profile->id);
		$avatarLink = $userKNN->getAvatarURL('kavatar');

		$profileKNN = KunenaFactory::getProfile($profile->id);
		$profileLink = $profileKNN->getProfileURL($profile->id, '');
		$editProfileLink = $profileKNN->getEditProfileURL($profile->id);

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private static function communitybuilder($profile , $isThumb = true)
	{
		$files = JPATH_ROOT . '/administrator/components/com_comprofiler/plugin.foundation.php';
		if (!JFile::exists($files)) return false;
		require_once( $files );
		cbimport('cb.database');
		cbimport('cb.tables');
		cbimport('cb.tabs');

		global $_CB_framework;

		$user = CBuser::getInstance($profile->id);

		if (!$user) {
			$user = CBuser::getInstance( null );
		}

		// Prevent CB from adding anything to the page.
		ob_start();
		$source = $user->getField('avatar' , null , 'php');
		$reset = ob_get_contents();
		ob_end_clean();
		unset($reset);

		$avatarLink = $source['avatar'];

		if (!$isThumb) {
			$avatarLink = str_ireplace('tn' , '' ,$avatarLink);
		}

		$profileLink = $_CB_framework->userProfileUrl($profile->id);

		$editProfileLink = $_CB_framework->userProfileEditUrl();

		return array($avatarLink, $profileLink, $editProfileLink);
	}

	private static function gravatar($profile)
	{
		$user = JFactory::getUser($profile->id);

		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
			|| isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
			$avatarLink = 'https://secure.gravatar.com/avatar.php?gravatar_id=';
		} else {
			$avatarLink = 'http://www.gravatar.com/avatar.php?gravatar_id=';
		}

		$avatarLink = $avatarLink . md5($user->email) . '?s=160';
		$avatarLink = $avatarLink.'&d=wavatar';

		$profileLink = EDR::_('index.php?option=com_easydiscuss&view=profile&id='.$profile->id, false);

		return array( $avatarLink, $profileLink );
	}

	private static function phpbb($profile)
	{
		$config = ED::config();
		$phpbbpath = $config->get('layout_phpbb_path');
		$phpbburl = $config->get('layout_phpbb_url');

		$phpbbDB = self::_getPhpbbDBO($phpbburl);
		$phpbbConfig = self::_getPhpbbConfig($phpbbDB);
		$phpbbuserid = 0;

		ED::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		if (empty($phpbbConfig)) {
			return false;
		}

		$juser = JFactory::getUser($profile->id);

		$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('user_id').', '.$phpbbDB->{$nameQuote}('username').', '.$phpbbDB->{$nameQuote}('user_avatar').', '.$phpbbDB->{$nameQuote}('user_avatar_type').' '
				. 'FROM '.$phpbbDB->{$nameQuote}('#__users').' WHERE '.$phpbbDB->{$nameQuote}('username').' = '.$phpbbDB->quote($juser->username).' '
				. 'LIMIT 1';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObject();

		$phpbbuserid = empty($result->user_id)? '0' : $result->user_id;

		if (!empty($result->user_avatar)) {
			switch($result->user_avatar_type)
			{
				case '1':
					$subpath	= $phpbbConfig->avatar_upload_path;
					$phpEx 		= JFile::getExt(__FILE__);
					$source		= $phpbburl.'/download/file.'.$phpEx.'?avatar='.$result->user_avatar;
					break;
				case '2':
					$source		= $result->user_avatar;
					break;
				case '3':
					$subpath	= $phpbbConfig->avatar_gallery_path;
					$source		= $phpbburl.'/'.$subpath.'/'.$result->user_avatar;
					break;
				default:
					$subpath 	= '';
					$source		= '';
			}
		}
		else
		{
			$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('theme_name').' '
					. 'FROM '.$phpbbDB->{$nameQuote}('#__styles_theme').' '
					. 'WHERE '.$phpbbDB->{$nameQuote}('theme_id').' = '.$phpbbDB->quote($phpbbConfig->default_style);
			$phpbbDB->setQuery($sql);
			$theme = $phpbbDB->loadObject();

			$defaultPath	= 'styles/'.$theme->theme_name.'/theme/images/no_avatar.gif';
			$source			= $phpbburl.'/'.$defaultPath;
		}

		$avatarLink		= 'http://' . $_SERVER['HTTP_HOST'] . '/' . $source;

		$profileLink	= $phpbburl.'/memberlist.php?mode=viewprofile&u='.$phpbbuserid;

		return array( $avatarLink, $profileLink );
	}

	private static function _getPhpbbDBO( $phpbburl = null )
	{
		static $db 	= null;

		if( is_null( $db ) )
		{
			$files			= JPATH_ROOT . '/' . $phpbburl . '/config.php';

			if (!JFile::exists($files))
			{
				$files	= $phpbburl . '/config.php';
				if (!JFile::exists($files))
				{
					return false;
				}
			}

			require_once( $files );

			$host		= $dbhost;
			$user		= $dbuser;
			$password	= $dbpasswd;
			$database	= $dbname;
			$prefix		= $table_prefix;
			$driver		= $dbms;
			$debug		= 0;

			$options = array ( 'driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix );

			$db 		= JDatabase::getInstance( $options );
		}

		return $db;
	}

	private static function _getPhpbbConfig( $phpbbDB = null )
	{
		DiscussHelper::getJoomlaVersion() >= '3.0' ? $nameQuote = 'quoteName' : $nameQuote = 'nameQuote';

		$sql	= 'SELECT '.$phpbbDB->{$nameQuote}('config_name').', '.$phpbbDB->{$nameQuote}('config_value').' '
				. 'FROM '.$phpbbDB->{$nameQuote}('#__config') . ' '
				. 'WHERE '.$phpbbDB->{$nameQuote}('config_name').' IN ('.$phpbbDB->quote('avatar_gallery_path').', '.$phpbbDB->quote('avatar_path').', '.$phpbbDB->quote('default_style').')';
		$phpbbDB->setQuery($sql);
		$result = $phpbbDB->loadObjectList();

		if(empty($result))
		{
			return false;
		}

		$phpbbConfig = new stdClass();
		$phpbbConfig->avatar_gallery_path	= null;
		$phpbbConfig->avatar_upload_path	= null;
		$phpbbConfig->default_style			= 1;

		foreach($result as $row)
		{
			switch($row->config_name)
			{
				case 'avatar_gallery_path':
					$phpbbConfig->avatar_gallery_path = $row->config_value;
					break;
				case 'avatar_path':
					$phpbbConfig->avatar_upload_path = $row->config_value;
					break;
				case 'default_style':
					$phpbbConfig->default_style = $row->config_value;
					break;
			}
		}

		return $phpbbConfig;
	}

	private static function easyblog( $profile )
	{
		$file	= JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

		jimport('joomla.filesystem.file');

		if (!JFile::exists($file)) {
			return false;
		}

		require_once($file);

		$profileEB = EB::table('Profile');
		$profileEB->load($profile->id);

		$editProfileLink = EB::getEditProfileLink();

		return array($profileEB->getAvatar(), $profileEB->getPermalink(), $editProfileLink);
	}
}
