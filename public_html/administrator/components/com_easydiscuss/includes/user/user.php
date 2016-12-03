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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * This class act like a layer to allow caller to call multiple users object.
 * the class actually return DiscussTableProfile object.
 * Brief example of use:
 *
 * <code>
 *
 * // Shorthand loading of current user.
 * $user 	= ED::user();
 *
 * // Loading of a user based on the id.
 * $user	= ED::user(42);
 *
 * // Loading of multiple users based on an array of id's.
 * $users	= ED::user(array(42, 43, 44, 45));
 *
 * </code>
 *
 * @since	5.0
 * @access	public
 * @author	Sam <sam@stackideas.com>
 */
class EasyDiscussUser
{

	/**
	 * Stores the user type.
	 * @var	string
	 */
	public $type = 'joomla';

	/**
	 * Keeps a list of users that are already loaded so we
	 * don't have to always reload the user again.
	 * @var Array
	 */
	static $userInstances	= array();


	/**
	 * Helper object for various cms versions.
	 * @var	object
	 */
	protected $helper 		= null;


	/**
	 * Class Constructor
	 *
	 * @since	1.0
	 * @access	public
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function __construct( $id = null , $debug = false )
	{
		$item	= self::loadUsers($id, $debug);

		return $item;
	}

	/**
	 * Object initialisation for the class to fetch the appropriate user
	 * object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param   $id     int/Array     Optional parameter
	 * @return  SocialUser   The person object.
	 */
	public static function factory($ids = null, $debug = false)
	{
		$items	= self::loadUsers($ids, $debug);

		return $items;
	}

	/**
	 * Initializes the guest user object
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public static function createGuestObject()
	{
		// Set guest property
		if (!isset(EasyDiscussUserStorage::$users[0])) {

			$profile = ED::table('Profile');

			EasyDiscussUserStorage::$users[0] = $profile;
		}
	}

	/**
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int|Array	Either an int or an array of id's in integer.
	 * @return	SocialUser	The user object.
	 *
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public static function loadUsers( $ids = null , $debug = false )
	{
		// Determine if the argument is an array.


		$argumentIsArray	= is_array($ids);

		// If it is null or 0, the caller wants to retrieve the current logged in user.
		if (is_null($ids) || (is_string($ids) && $ids == '')) {
			$ids 	= array(JFactory::getUser()->id);
		}

		// Always create the guest objects first.
		self::createGuestObject();

		// Ensure that id's are always an array
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		// Reset the index of ids so we don't load multiple times from the same user.
		$ids = array_values($ids);

		// Total needs to be computed here before entering iteration as it might be affected by unset.
		$total = count($ids);

		// Placeholder for items that are already loaded.
		$loaded = array();

		// @task: We need to only load user's that aren't loaded yet.
		for( $i = 0; $i < $total; $i++) {

			if (empty($ids)) {
				break;
			}

			if (!isset($ids[$i]) && empty($ids[$i])) {
				continue;
			}

			$id		= $ids[ $i ];

			// If id is null, we know we want the current user.
			if( is_null( $id ) )
			{
				$ids[ $i ] 	= JFactory::getUser()->id;
			}

			// The parsed id's could be an object from the database query.
			if( is_object( $id ) && isset( $id->id ) ) {
				$id			= $id->id;

				// Replace the current value with the proper value.
				$ids[ $i ]	= $id;
			}

			if (isset(EasyDiscussUserStorage::$users[$id])) {
				$loaded[]	= $id;
				unset($ids[$i]);
			}

		}

		// @task: Reset the ids after it was previously unset.
		$ids	= array_values( $ids );

		// Place holder for result items.
		$result	= array();

		foreach ($loaded as $id) {
			$result[]	= EasyDiscussUserStorage::$users[$id];
		}

		if (!empty($ids)) {

			// @task: Now, get the user data.
			$model = ED::model('Users');
			$users = $model->getUsersMeta($ids);

			if ($users) {

				// @task: Iterate through the users list and add them into the static property.
				foreach ($users as $user) {

					$obj = ED::table('Profile');

					if (empty($user->ed_id)) {

						// the load method will create a new records, and assign juser into class property 'user'
						$obj->load($user->id);
					} else {

						$data = array();
						$data['id'] = $user->ed_id;
						$data['nickname'] = $user->nickname;
						$data['avatar'] = $user->avatar;
						$data['description'] = $user->description;
						$data['url'] = $user->url;
						$data['params'] = $user->ed_params;
						$data['alias'] = $user->alias;
						$data['points'] = $user->points;
						$data['latitude'] = $user->latitude;
						$data['longitude'] = $user->longitude;
						$data['location'] = $user->location;
						$data['signature'] = $user->signature;
						$data['site'] = $user->site;
						$data['edited'] = $user->edited;
						$data['posts_read'] = $user->posts_read;
						$data['auth'] = $user->auth;
						
						$obj->bind($data);

						$obj->numPostCreated = $user->numPostCreated;
						$obj->numPostAnswered = $user->numPostAnswered;

						// var_dump($obj);

						// juser binding
						// unset data from EasyDiscuss_users table
						unset($user->ed_id);
						unset($user->nickname);
						unset($user->avatar);
						unset($user->description);
						unset($user->url);
						unset($user->ed_params);
						unset($user->alias);
						unset($user->points);
						unset($user->latitude);
						unset($user->longitude);
						unset($user->location);
						unset($user->signature);
						unset($user->site);
						unset($user->edited);
						unset($user->posts_read);
						unset($user->numPostCreated);
						unset($user->numPostCreated);

						$data = get_object_vars($user);

						// JFactory::getUser() might load extra data that may not used in EasyDiscuss profile object. for now, we will jz use binding to bind the require data only.
						$obj->user = JFactory::getUser($user->id);
					}

					EasyDiscussUserStorage::$users[$user->id]	= $obj;

					$result[]	= EasyDiscussUserStorage::$users[$user->id];

				}
			} else {

				foreach ($ids as $id) {

					// Since there are no such users, we just use the guest object.
					EasyDiscussUserStorage::$users[$id] = EasyDiscussUserStorage::$users[0];

					$result[] = EasyDiscussUserStorage::$users[$id];
				}
			}
		}


		// If the argument passed in is not an array, just return the proper value.
		if (!$argumentIsArray && count( $result ) == 1) {
			return $result[0];
		}

		return $result;
	}

}

/**
 * This class would be used to store all user objects
 *
 */
class EasyDiscussUserStorage
{
	static $users 	= array();
}
