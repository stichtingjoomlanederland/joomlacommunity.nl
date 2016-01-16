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

define( 'DISCUSS_JURIROOT'					, rtrim( JURI::root(), '/') );

define( 'DISCUSS_JOOMLA_ROOT'               , JPATH_ROOT);
define( 'DISCUSS_JOOMLA_ROOT_URI'           , rtrim(JURI::root(), '/'));

define( 'DISCUSS_JOOMLA_SITE_TEMPLATES'     , JPATH_ROOT . '/templates' );
define( 'DISCUSS_JOOMLA_SITE_TEMPLATES_URI' , DISCUSS_JURIROOT . '/templates' );
define( 'DISCUSS_JOOMLA_ADMIN_TEMPLATES'    , JPATH_ADMINISTRATOR . '/templates' );
define( 'DISCUSS_JOOMLA_ADMIN_TEMPLATES_URI', DISCUSS_JURIROOT . '/administrator/templates' );
define( 'DISCUSS_JOOMLA_MODULES'            , JPATH_ROOT . '/modules' );
define( 'DISCUSS_JOOMLA_MODULES_URI'        , DISCUSS_JURIROOT . '/modules' );

define( 'DISCUSS_ROOT'						, JPATH_ROOT . '/components/com_easydiscuss' );
define( 'DISCUSS_ROOT_URI'					, DISCUSS_JURIROOT . '/components/com_easydiscuss' );
define( 'DISCUSS_ADMIN_ROOT'				, JPATH_ADMINISTRATOR . '/components/com_easydiscuss' );
define( 'DISCUSS_ADMIN_ROOT_URI'			, DISCUSS_JURIROOT . '/administrator/components/com_easydiscuss' );

define( 'DISCUSS_CONTROLLERS'				, DISCUSS_ROOT . '/controllers' );
define( 'DISCUSS_MODELS'					, DISCUSS_ROOT . '/models' );
define( 'DISCUSS_CLASSES'					, DISCUSS_ROOT . '/classes' );
define( 'DISCUSS_HELPERS'					, DISCUSS_ROOT . '/helpers' );
define( 'DISCUSS_TABLES'					, DISCUSS_ADMIN_ROOT . '/tables' );;

define( 'DISCUSS_ASSETS'					, DISCUSS_ROOT . '/assets' );
define( 'DISCUSS_ASSETS_URI'				, DISCUSS_ROOT_URI . '/assets' );
define( 'DISCUSS_ADMIN_ASSETS'				, DISCUSS_ADMIN_ROOT . '/assets' );
define( 'DISCUSS_ADMIN_ASSETS_URI'			, DISCUSS_ADMIN_ROOT_URI . '/assets' );

define( 'DISCUSS_FOUNDRY_VERSION'			, '3.1' );
define( 'DISCUSS_FOUNDRY'					, JPATH_ROOT . '/media/foundry/' . DISCUSS_FOUNDRY_VERSION );
define( 'DISCUSS_FOUNDRY_URI'				, DISCUSS_JURIROOT . '/media/foundry/' . DISCUSS_FOUNDRY_VERSION );
define( 'DISCUSS_FOUNDRY_CONFIGURATION'		, DISCUSS_FOUNDRY . '/joomla/configuration.php' );

define( 'DISCUSS_MEDIA'						, JPATH_ROOT . '/media/com_easydiscuss' );
define( 'DISCUSS_MEDIA_URI'					, DISCUSS_JURIROOT . '/media/com_easydiscuss' );

define( 'DISCUSS_SITE_THEMES'				, DISCUSS_ROOT . '/themes' );
define( 'DISCUSS_SITE_THEMES_URI'			, DISCUSS_ROOT_URI . '/themes' );
define( 'DISCUSS_ADMIN_THEMES'				, DISCUSS_ADMIN_ROOT . '/themes' );
define( 'DISCUSS_ADMIN_THEMES_URI'			, DISCUSS_ADMIN_ROOT_URI . '/themes' );

define( 'DISCUSS_SPINNER'					, DISCUSS_MEDIA_URI . '/images/loading.gif' );

define( 'DISCUSS_UPDATES_SERVER'	, 'stackideas.com' );
define( 'DISCUSS_POWERED_BY'		, '');

// Privacy
define( 'DISCUSS_PRIVACY_PUBLIC'	, '0' );
define( 'DISCUSS_PRIVACY_PRIVATE'	, '1' );
define( 'DISCUSS_PRIVACY_ACL'		, '2' );

// Filters
define( 'DISCUSS_FILTER_ALL'		, 'all' );
define( 'DISCUSS_FILTER_PUBLISHED'	, 'published' );
define( 'DISCUSS_FILTER_UNPUBLISHED', 'unpublished' );

// Featured posts
define( 'DISCUSS_MAX_FEATURED_POST'	, '3' );

// Discussion types
define( 'DISCUSS_QUESTION_TYPE'		, 'questions' );
define( 'DISCUSS_REPLY_TYPE'		, 'replies' );
define( 'DISCUSS_USERQUESTIONS_TYPE', 'userquestions' );
define( 'DISCUSS_TAGS_TYPE'			, 'tags' );
define( 'DISCUSS_SEARCH_TYPE'		, 'search' );

// Post resolve status
define( 'DISCUSS_ENTRY_RESOLVED'	, 1 );
define( 'DISCUSS_ENTRY_UNRESOLVED'	, 0 );

// Notification queue types
define( 'DISCUSS_QUEUE_SUCCESS'		, 'success' );
define( 'DISCUSS_QUEUE_ERROR'		, 'error' );
define( 'DISCUSS_QUEUE_WARNING'		, 'warning' );

// Post status ID
define( 'DISCUSS_ID_UNPUBLISHED'	, 0 );
define( 'DISCUSS_ID_PUBLISHED'		, 1 );
define( 'DISCUSS_ID_SCHEDULED'		, 2 );
define( 'DISCUSS_ID_DRAFT'			, 3 );
define( 'DISCUSS_ID_PENDING'		, 4 );

// Avatar sizes
define( 'DISCUSS_AVATAR_LARGE_WIDTH'	, 160 );
define( 'DISCUSS_AVATAR_LARGE_HEIGHT'	, 160 );
define( 'DISCUSS_AVATAR_THUMB_WIDTH'	, 60 );
define( 'DISCUSS_AVATAR_THUMB_HEIGHT'	, 60 );

// Category
define( 'DISCUSS_CATEGORY_PARENT'				, 0 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_SELECT'	, 1 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_VIEW'		, 2 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_REPLY'		, 3 );
define( 'DISCUSS_CATEGORY_ACL_ACTION_VIEWREPLY'	, 4 );
define( 'DISCUSS_CATEGORY_ACL_MODERATOR'		, 5 );

// Notifications constants
define( 'DISCUSS_NOTIFICATIONS_MENTIONED'		, 'mention' );
define( 'DISCUSS_NOTIFICATIONS_REPLY'			, 'reply' );
define( 'DISCUSS_NOTIFICATIONS_RESOLVED'		, 'resolved' );
define( 'DISCUSS_NOTIFICATIONS_ACCEPTED'		, 'accepted' );
define( 'DISCUSS_NOTIFICATIONS_FEATURED'		, 'featured' );
define( 'DISCUSS_NOTIFICATIONS_COMMENT'			, 'comment' );
define( 'DISCUSS_NOTIFICATIONS_PROFILE'			, 'profile' );
define( 'DISCUSS_NOTIFICATIONS_BADGE'			, 'badge' );
define( 'DISCUSS_NOTIFICATIONS_LOCKED'			, 'locked' );
define( 'DISCUSS_NOTIFICATIONS_UNLOCKED'		, 'unlocked' );
define( 'DISCUSS_NOTIFICATIONS_LIKES_DISCUSSION', 'likes-discussion' );
define( 'DISCUSS_NOTIFICATIONS_LIKES_REPLIES'	, 'likes-replies' );
define( 'DISCUSS_NOTIFICATION_READ'				, 0 );
define( 'DISCUSS_NOTIFICATION_NEW'				, 1 );
define( 'DISCUSS_NOTIFICATIONS_ON_HOLD'			, 'onHold' );
define( 'DISCUSS_NOTIFICATIONS_WORKING_ON'		, 'workingOn' );
define( 'DISCUSS_NOTIFICATIONS_REJECTED'		, 'reject' );
define( 'DISCUSS_NOTIFICATIONS_NO_STATUS'		, 'unhold' );
define( 'DISCUSS_NOTIFICATIONS_VOTE_UP_REPLY'				, 'vote-up-reply' );
define( 'DISCUSS_NOTIFICATIONS_VOTE_DOWN_REPLY'				, 'vote-down-reply' );
define( 'DISCUSS_NOTIFICATIONS_VOTE_UP_DISCUSSION'			, 'vote-up-discussion' );
define( 'DISCUSS_NOTIFICATIONS_VOTE_DOWN_DISCUSSION'		, 'vote-down-discussion' );

// Point systems
define( 'DISCUSS_POINTS_NEW_DISCUSSION'			, 'discussion.new' );
define( 'DISCUSS_POINTS_DELETE_DISCUSSION'		, 'discussion.delete' );
define( 'DISCUSS_POINTS_VIEW_DISCUSSION'		, 'discussion.view' );
define( 'DISCUSS_POINTS_NEW_AVATAR'				, 'avatar.new' );
define( 'DISCUSS_POINTS_UPDATE_AVATAR'			, 'avatar.update' );
define( 'DISCUSS_POINTS_NEW_REPLY'				, 'reply.new' );
define( 'DISCUSS_POINTS_DELETE_REPLY'			, 'reply.delete' );
define( 'DISCUSS_POINTS_NEW_COMMENT'			, 'comment.new' );
define( 'DISCUSS_POINTS_DELETE_COMMENT'			, 'comment.delete' );
define( 'DISCUSS_POINTS_ACCEPT_REPLY'			, 'accept.reply' );
define( 'DISCUSS_POINTS_ANSWER_VOTE_UP'			, 'answer.voteup' );
define( 'DISCUSS_POINTS_ANSWER_VOTE_DOWN'		, 'answer.votedown' );
define( 'DISCUSS_POINTS_QUESTION_VOTE_UP'		, 'question.voteup' );
define( 'DISCUSS_POINTS_QUESTION_VOTE_DOWN'		, 'question.votedown' );

// Badges
define( 'DISCUSS_BADGES_PATH'			, JPATH_ROOT . '/media/com_easydiscuss/badges' );
define( 'DISCUSS_BADGES_URI'			, DISCUSS_JURIROOT . '/media/com_easydiscuss/badges' );
define( 'DISCUSS_BADGES_DEFAULT'		, DISCUSS_BADGES_PATH . '/default' );
define( 'DISCUSS_BADGES_UPLOADED'		, DISCUSS_BADGES_PATH . '/uploaded' );
define( 'DISCUSS_BADGES_FAVICON_WIDTH'	, 16 );
define( 'DISCUSS_BADGES_FAVICON_HEIGHT'	, 16 );

//
define( 'DISCUSS_HISTORY_BADGES'		, 'badges' );
define( 'DISCUSS_HISTORY_POINTS'		, 'points' );

//
define( 'DISCUSS_POSTER_GUEST'			, 'guest' );
define( 'DISCUSS_POSTER_MEMBER'			, 'member' );

// Like type
define( 'DISCUSS_ENTITY_TYPE_POST'		, 'post' );

// Access types for category
define( 'DISCUSS_CATEGORY_ACCESS_VIEW'	, 'view' );
define( 'DISCUSS_CATEGORY_ACCESS_WRITE'	, 'write' );

// Voting types
define( 'DISCUSS_VOTE_UP'				, 1 );
define( 'DISCUSS_VOTE_DOWN'				, -1 );
define( 'DISCUSS_VOTE_UP_STRING'		, 'up' );
define( 'DISCUSS_VOTE_DOWN_STRING'		, 'down' );
define( 'DISCUSS_CUSTOMFIELDS_ACL_VIEW'	, 1 );
define( 'DISCUSS_CUSTOMFIELDS_ACL_INPUT', 2 );

// Conversation
define( 'DISCUSS_CONVERSATION_UNREAD'	, 0 );
define( 'DISCUSS_CONVERSATION_READ'		, 1 );
define( 'DISCUSS_CONVERSATION_ARCHIVED' , 0 );
define( 'DISCUSS_CONVERSATION_PUBLISHED', 1 );
define( 'DISCUSS_CONVERSATION_DELETED'	, 2 );
define( 'DISCUSS_NO_LIMIT'				, -10 );

define( 'DISCUSS_POST_STATUS_OFF'			, 0 );
define( 'DISCUSS_POST_STATUS_ON_HOLD'		, 1 );
define( 'DISCUSS_POST_STATUS_ACCEPTED'		, 2 );
define( 'DISCUSS_POST_STATUS_WORKING_ON'	, 3 );
define( 'DISCUSS_POST_STATUS_REJECT'		, 4 );

// @since Foundry 3.1
// Foundry
require_once(JPATH_ROOT . '/media/foundry/3.1/joomla/framework.php');
FD31_FoundryFramework::defineComponentConstants( "EasyDiscuss" );

