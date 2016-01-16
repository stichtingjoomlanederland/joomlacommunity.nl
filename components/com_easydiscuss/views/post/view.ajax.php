<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';
require_once DISCUSS_HELPERS . '/input.php';
require_once DISCUSS_CLASSES . '/themes.php';
require_once DISCUSS_CLASSES . '/composer.php';
require_once DISCUSS_HELPERS . '/parser.php';
require_once DISCUSS_HELPERS . '/string.php';
require_once DISCUSS_HELPERS . '/filter.php';
require_once DISCUSS_HELPERS . '/integrate.php';
require_once DISCUSS_HELPERS . '/user.php';
require_once DISCUSS_HELPERS . '/router.php';

require_once( DISCUSS_ROOT . '/views.php' );

class EasyDiscussViewPost extends EasyDiscussView
{
    protected $err  = null;

    /**
     * Displays a dialog with a category selection.
     *
     * @since   3.0
     * @access  public
     */
    public function movePost( $id )
    {
        $ajax   = new Disjax();
        $my     = JFactory::getUser();
        $post   = DiscussHelper::getTable( 'Post' );
        $state  = $post->load( $id );

        if( !$state || !$id || $post->parent_id )
        {
            echo JText::_( 'COM_EASYDISCUSS_SYSTEM_INVALID_ID' );
            return $ajax->send();
        }

        // Load the category of the post.
        $category   = DiscussHelper::getTable( 'Category' );
        $category->load( $post->category_id );

        // Load the access.
        $access     = $post->getAccess( $category );

        if( !$my->id || !$access->canMove() )
        {
            echo JText::_( 'COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS' );
            return $ajax->send();
        }

        // Get list of categories.
        $categories         = DiscussHelper::populateCategories( '' , '' , 'select' , 'category_id', $post->category_id , true, true , true , true );

        $theme              = new DiscussThemes();
        $theme->set( 'categories'   , $categories );
        $theme->set( 'id'           , $id );

        $content            = $theme->fetch( 'ajax.post.move.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;
        $options->title     = JText::_( 'COM_EASYDISCUSS_MOVE_POST_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_MOVE' );
        $button->form       = '#frmMovePost';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();

    }

    /**
     * Displays al ist of moderators on the site.
     *
     * @since   3.2
     * @access  public
     * @param   string
     * @return
     */
    public function getModerators()
    {
        $ajax           = DiscussHelper::getHelper('Ajax');

        if (!EDC::isSiteAdmin() && !EDC::isModerator()) {
            return;
        }

        $postId         = JRequest::getString('id');
        $categoryId     = JRequest::getString('category_id');
        $moderators     = DiscussHelper::getHelper( 'Moderator' )->getModeratorsDropdown( $categoryId );

        $html   = '';
        if( !empty($moderators) )
        {
            $theme  = new DiscussThemes();
            $theme->set( 'moderators' , $moderators );
            $theme->set( 'postId' , $postId );
            $html   = $theme->fetch( 'post.assignment.item.php' );
        }
        else
        {
            $html = '<li class="pa-10">' . JText::_( 'COM_EASYDISCUSS_NO_MODERATOR_FOUND' ) . '</li>';
        }

        $ajax->success( $html );
    }

    public function similarQuestion()
    {
        $ajax   = DiscussHelper::getHelper( 'Ajax' );
        $config = DiscussHelper::getConfig();

        // if enabled
        $html   = '';
        $query  = JRequest::getString( 'query' );
        $posts  = DiscussHelper::getSimilarQuestion( $query );

        if( !empty( $posts ) )
        {
            foreach( $posts as &$post)
            {
                $post->title    = DiscussHelper::wordFilter($post->title);

            }
            $theme  = new DiscussThemes();
            $theme->set( 'posts' , $posts );
            $html   = $theme->fetch( 'ajax.similar.question.list.php' , array('dialog'=> true ) );
        }

        //$ajax->EasyDiscuss.$('#div').html( 'value' );
        $ajax->success( $html );
    }

    public function selectCategory()
    {
        $ajax       = new Disjax();
        $theme      = new DiscussThemes();
        $content    = $theme->fetch( 'ajax.selectcategory.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;
        $options->title     = JText::_( 'COM_EASYDISCUSS_DIALOG_TITLE_FORM_ERROR' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    public function checklogin()
    {
        $my = JFactory::getUser();
        $ajax   = new Disjax();

        if(empty($my->id))
        {
            $config     = DiscussHelper::getConfig();
            $tpl        = new DiscussThemes();
            $session    = JFactory::getSession();

            $acl        = DiscussHelper::getHelper( 'ACL', '0' );

            $defaultUserType = $acl->allowed('add_reply') ? 'guest' : 'member';
            $return     = DiscussRouter::_('index.php?option=com_easydiscuss&view=ask', false);
            $token      = DiscussHelper::getToken();

            $guest = new stdClass();
            if($session->has( 'guest_reply_authentication', 'discuss' ))
            {
                $session_request    = JString::str_ireplace(',', "\r\n", $session->get('guest_reply_authentication', '', 'discuss'));
                $guest_session      = new JParameter( $session_request );

                $guest->email   = $guest_session->get('email', '');
                $guest->name    = $guest_session->get('name', '');
            }

            $twitter    = '';
            if($config->get('integration_twitter_consumer_secret_key'))
            {
                require_once DISCUSS_HELPERS . '/twitter.php';
                $twitter = DiscussTwitterHelper::getAuthentication();
            }

            $tpl->set( 'return'     , base64_encode($return) );
            $tpl->set( 'config'     , $config );
            $tpl->set( 'token'      , $token );
            $tpl->set( 'guest'      , $guest );
            $tpl->set( 'twitter'    , $twitter );

            $html = $tpl->fetch( 'login.php' );
            $ajax->script( 'discuss.login.token = "'.$token.'";');

            $options = new stdClass();
            $options->title = JText::_( 'COM_EASYDISCUSS_LOGIN' );
            $options->content = $html;

            $ajax->dialog( $options );

            $ajax->script( 'discuss.login.showpane(\''.$defaultUserType.'\');');
        }
        else
        {
            $ajax->script( "EasyDiscuss.$( '#user_type' ).val( 'member' );" );
            $ajax->script( "discuss.reply.post();" );
        }
        $ajax->script( 'discuss.spinner.hide("reply_loading");');
        $ajax->send();
    }

    public function deletePostForm( $id = null , $type = null , $url = null )
    {
        $ajax       = new Disjax();
        $theme      = new DiscussThemes();

        $theme->set( 'id'   , $id );
        $theme->set( 'type' , $type );
        $theme->set( 'url'  , base64_encode( $url ) );
        $content    = $theme->fetch( 'ajax.delete.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;

        $title              = $type == 'reply' ? 'COM_EASYDISCUSS_REPLY_DELETE_TITLE' : 'COM_EASYDISCUSS_ENTRY_DELETE_TITLE';
        $options->title     = JText::_( $title );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
        $button->form       = '#deletePostForm';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    /**
     * Displays a confirmation dialog to accept a reply item as an answer.
     *
     * @since   3.0
     * @access  public
     * @param   int     The unique post id.
     */
    public function confirmAccept( $id = null )
    {
        $ajax       = new Disjax();
        $theme      = new DiscussThemes();

        $theme->set( 'id'   , $id );
        $content    = $theme->fetch( 'ajax.accept.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;

        $options->title     = JText::_( 'COM_EASYDISCUSS_REPLY_ACCEPT_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
        $button->form       = '#acceptPostForm';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    public function confirmReject( $id = null )
    {
        $ajax       = new Disjax();
        $theme      = new DiscussThemes();

        $theme->set( 'id'   , $id );
        $content    = $theme->fetch( 'ajax.reject.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;

        $options->title     = JText::_( 'COM_EASYDISCUSS_REPLY_REJECT_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
        $button->form       = '#rejectPostForm';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    public function ajaxRefreshTwitter()
    {
        require_once DISCUSS_HELPERS . '/twitter.php';

        $disjax = new Disjax();

        $header = '<h1>'.JText::_('COM_EASYDISCUSS_TWITTER').'</h1>';
        $html   = trim(DiscussTwitterHelper::getAuthentication());

        $disjax->script('EasyDiscuss.$(\'#usertype_twitter_pane\').html(\''.$header.$html.'\');');

        $disjax->send();
    }

    public function ajaxSignOutTwitter()
    {
        require_once DISCUSS_HELPERS . '/twitter.php';

        $disjax = new Disjax();
        $session = JFactory::getSession();

        if($session->has( 'twitter_oauth_access_token', 'discuss' ))
        {
            $session->clear( 'twitter_oauth_access_token', 'discuss' );
        }

        $header = '<h1>'.JText::_('COM_EASYDISCUSS_TWITTER').'</h1>';
        $html   = trim(DiscussTwitterHelper::getAuthentication());

        $disjax->script('EasyDiscuss.$(\'#usertype_twitter_pane\').html(\''.$header.addslashes($html).'\');');

        $disjax->send();
    }

    public function ajaxGuestReply($email = null, $name = null)
    {
        require_once DISCUSS_HELPERS . '/email.php';

        $disjax = new Disjax();

        if(empty($email))
        {
            $disjax->script("EasyDiscuss.$('#usertype_status .msg_in').html('".JText::_('COM_EASYDISCUSS_PLEASE_INSERT_YOUR_EMAIL_ADDRESS_TO_PROCEED')."');");
            $disjax->script("EasyDiscuss.$('#usertype_status .msg_in').addClass('alert alert-error');");
            $disjax->script("EasyDiscuss.$('#edialog-guest-reply').removeAttr('disabled');");
            $disjax->send();
            return false;
        }

        if(DiscussEmailHelper::isValidInetAddress($email)==false)
        {
            $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').html(\''.JText::_('COM_EASYDISCUSS_INVALID_EMAIL_ADDRESS').'\');');
            $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').addClass(\'alert alert-error\');');

            $disjax->script("EasyDiscuss.$('#edialog-guest-reply').removeAttr('disabled');");
        }
        else
        {
            $session = JFactory::getSession();

            if($session->has( 'guest_reply_authentication', 'discuss' ))
            {
                $session->clear( 'guest_reply_authentication', 'discuss' );
            }

            $name = ($name)? $name : $email;

            $session->set('guest_reply_authentication', "email=".$email.",name=".$name."", 'discuss');


            $disjax->script('EasyDiscuss.$(\'#user_type\').val(\'guest\');');
            $disjax->script('EasyDiscuss.$(\'#poster_name\').val(EasyDiscuss.$(\'#discuss_usertype_guest_name\').val());');
            $disjax->script('EasyDiscuss.$(\'#poster_email\').val(EasyDiscuss.$(\'#discuss_usertype_guest_email\').val());');
            $disjax->script('disjax.closedlg();');
            $disjax->script( 'discuss.reply.submit();' );
        }

        $disjax->send();
    }

    public function ajaxMemberReply($username = null, $password = null, $token = null)
    {
        $disjax     = new Disjax();
        $mainframe  = JFactory::getApplication();

        JRequest::setVar( $token, 1 );

        if(empty($username) || empty($password))
        {
            $disjax->script("EasyDiscuss.$('#usertype_status .msg_in').html('".JText::_('COM_EASYDISCUSS_PLEASE_INSERT_YOUR_USERNAME_AND_PASSWORD')."');");
            $disjax->script("EasyDiscuss.$('#usertype_status .msg_in').addClass('alert alert-error');");
            $disjax->script("EasyDiscuss.$('#edialog-member-reply').prop('disabled', false);");
            $disjax->send();
            return false;
        }

        // Check for request forgeries
        if(JRequest::checkToken('request'))
        {
            $credentials = array();

            $credentials['username'] = $username;
            $credentials['password'] = $password;

            $result = $mainframe->login($credentials);

            if (!JError::isError($result))
            {
                $token = DiscussHelper::getToken();
                $disjax->script( 'EasyDiscuss.$(".easydiscuss-token").val("' . $token . '");');
                $disjax->script('disjax.closedlg();');
                $disjax->script( 'discuss.reply.submit();' );
            }
            else
            {
                $error = JError::getError();

                $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').html(\''.$error->message.'\');');
                $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').addClass(\'alert alert-error\');');
                $disjax->script('EasyDiscuss.$(\'#edialog-member-reply\').prop(\'disabled\', false);');
            }
        }
        else
        {
            $token = DiscussHelper::getToken();
            $disjax->script( 'discuss.login.token = "'.$token.'";' );

            $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').html(\''.JText::_('COM_EASYDISCUSS_MEMBER_LOGIN_INVALID_TOKEN').'\');');
            $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').addClass(\'alert alert-error\');');

            $disjax->script( 'EasyDiscuss.$(\'#edialog-reply\').prop(\'disabled\', false);' );
        }

        $disjax->send();
    }

    public function ajaxTwitterReply()
    {
        $disjax = new Disjax();

        $twitterUserId              = '';
        $twitterScreenName          = '';
        $twitterOauthToken          = '';
        $twitterOauthTokenSecret    = '';

        $session = JFactory::getSession();

        if($session->has( 'twitter_oauth_access_token', 'discuss' ))
        {
            $session_request    = JString::str_ireplace(',', "\r\n", $session->get('twitter_oauth_access_token', '', 'discuss'));
            $access_token       = new JParameter( $session_request );

            $twitterUserId              = $access_token->get('user_id', '');
            $twitterScreenName          = $access_token->get('screen_name', '');
            $twitterOauthToken          = $access_token->get('oauth_token', '');
            $twitterOauthTokenSecret    = $access_token->get('oauth_token_secret', '');
        }

        if(empty($twitterUserId) || empty($twitterOauthToken) || empty($twitterOauthTokenSecret))
        {
            $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').html(\''.JText::_('COM_EASYDISCUSS_TWITTER_REQUIRES_AUTHENTICATION').'\');');
            $disjax->script('EasyDiscuss.$(\'#usertype_status .msg_in\').addClass(\'alert alert-error\');');
            $disjax->script('EasyDiscuss.$(\'#edialog-twitter-reply\').attr(\'disabled\', \'\');');
        }
        else
        {
            $screen_name = $twitterScreenName? $twitterScreenName : $twitterUserId;
            $disjax->script('EasyDiscuss.$(\'#user_type\').val(\'twitter\');');
            $disjax->script('EasyDiscuss.$(\'#poster_name\').val(\''.$screen_name.'\');');
            $disjax->script('EasyDiscuss.$(\'#poster_email\').val(\''.$twitterUserId.'\');');
            $disjax->script('disjax.closedlg();');
            $disjax->script( 'discuss.reply.submit();' );
        }

        $disjax->send();
    }

    /**
     * Lock a specific discussion given the post id.
     *
     * @since   3.0
     * @access  public
     * @param   int     The post's id.
     */
    public function ajaxLockPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post       = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $category   = DiscussHelper::getTable( 'Category' );
        $category->load( $post->category_id );

        $access     = $post->getAccess( $category );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $acl        = DiscussHelper::getHelper( 'ACL' );

        if( !$access->canLock() )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        //update isresolve flag
        $post->islock   = 1;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id && $config->get( 'main_notifications_locked' ))
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_LOCKED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => DISCUSS_NOTIFICATIONS_LOCKED,
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_LOCKED') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );
        $ajax->script( 'EasyDiscuss.$( ".discuss-item" ).addClass("is-locked");' );

        // For flatt theme
        $ajax->script( 'EasyDiscuss.$( ".discuss-action-bar" ).addClass("is-locked");' );
        $ajax->send();
        return;
    }

    /**
     * Unlocks a discussion.
     *
     * @since   3.0
     * @access  public
     * @param   int     The post's unique id.
     */
    public function ajaxUnlockPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post           = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $acl        = DiscussHelper::getHelper( 'ACL' );
        $isModerator    = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin && !$acl->allowed('lock_discussion', '0') )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        //update isresolve flag
        $post->islock   = 0;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id && $config->get( 'main_notifications_locked' ) )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_UNLOCKED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => DISCUSS_NOTIFICATIONS_UNLOCKED,
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        $ajax->script( 'window.location.reload();');
        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_UNLOCKED') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );
        $ajax->script( 'EasyDiscuss.$( ".discuss-item").removeClass("is-locked");' );

        // For flatt theme
        $ajax->script( 'EasyDiscuss.$( ".discuss-action-bar" ).removeClass("is-locked");' );
        $ajax->send();
        return;
    }

    /**
     * Mark's a discussion as resolve.
     *
     * @since   3.0
     * @access  public
     *
     */
    public function resolve( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post       = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $isModerator = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        $post->isresolve    = DISCUSS_ENTRY_RESOLVED;

        // When post is resolve state, other post status must remove
        $post->post_status  = DISCUSS_POST_STATUS_OFF;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $my     = JFactory::getUser();

        // @rule: Badges only applicable when they resolve their own post.
        if( $post->get( 'user_id' ) == $my->id )
        {
            // Add logging for user.
            DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.resolved.discussion' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_RESOLVED_OWN_DISCUSSION' , $post->title ), $post->id );

            DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.resolved.discussion' , $my->id );
            DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.resolved.discussion' , $my->id );

            // Assign badge for EasySocial
            DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'resolve.reply' , $my->id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_RESOLVED_OWN_DISCUSSION' , $post->title ) );
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id && $config->get( 'main_notifications_resolved' ) )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_RESOLVED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => DISCUSS_NOTIFICATIONS_RESOLVED,
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        // Add resolved button to the view.
        ob_start();
        ?>
        <a id="post_unresolve_link" href="javascript:void(0);" onclick="discuss.post.unresolve('<?php echo $postId; ?>');EasyDiscuss.$(this).parents('.discuss-status').hide();" class="resolved-button float-r">
            <?php echo JText::_('COM_EASYDISCUSS_RESOLVED'); ?>
        </a>
        <?php

        $contents   = ob_get_contents();
        ob_end_clean();

        $ajax->append( 'discuss-status' , $contents );
        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_ENTRY_RESOLVED') );


        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );


        // Add Status
        $ajax->script( 'EasyDiscuss.$(".discussQuestion").addClass("is-resolved");' );

        // For flatt theme
        $ajax->script( 'EasyDiscuss.$( ".discuss-action-bar" ).addClass("is-resolved");' );

        // Remove other status
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass("label-post_status-on-hold");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus").removeClass("label-post_status-accepted");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus").removeClass("label-post_status-working-on");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus").removeClass("label-post_status-reject");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus").html("");' );

        $ajax->send();
        return;
    }


    /**
     * Ajax Call
     * Set as unresolve
     */
    public function unresolve( $postId = null )
    {
        $ajax   = new Disjax();

        if(empty($postId))
        {
            $ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#reports-msg .msg_in" ).addClass( "alert alert-error" );' );
            $ajax->send();
            $ajax->success();
            return;
        }

        $post   = DiscussHelper::getTable( 'Post' );
        $post->load( $postId );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $isModerator = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        //update isresolve flag
        $post->isresolve    = DISCUSS_ENTRY_UNRESOLVED;


        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications .msg_in' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications .msg_in" ).addClass( "alert alert-error" );' );
            $ajax->send();
            $ajax->success();
            return;
        }

        // now we clear off all the accepted answers.
        $post->clearAccpetedReply();

        // Clear resolved buttons.
        $ajax->script( 'EasyDiscuss.$(".discuss-status #post_unresolve_link").remove();' );
        //$ajax->assign( 'dc_main_notifications .msg_in' , JText::_('COM_EASYDISCUSS_ENTRY_UNRESOLVED') );
        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_ENTRY_UNRESOLVED') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications .msg_in" ).addClass( "dc_success" );' );
        $ajax->script( 'EasyDiscuss.$( "#title_' . $postId . ' span.resolved" ).remove();' );

        // Update the state of the item and remove 'is-resolved'
        $ajax->script( 'EasyDiscuss.$(".discussQuestion").removeClass("is-resolved");' );

        // For flatt theme
        $ajax->script( 'EasyDiscuss.$( ".discuss-action-bar" ).removeClass("is-resolved");' );

        $ajax->send();
        $ajax->success();
        return;
    }


    /**
     * Ajax Call
     * Get raw content from db
     */
    public function ajaxGetRawContent( $postId = null )
    {
        $djax   = new Disjax();

        if(! empty($postId))
        {
            $postTable          = DiscussHelper::getTable( 'Post' );
            $postTable->load( $postId );

            $djax->value('reply_content_' . $postId, $postTable->content);
        }

        $djax->send();
        return;
    }


    /**
     * Process new reply submission called via an iframe.
     *
     * @since   2.0
     * @access  public
     */
    public function ajaxSubmitReply()
    {
        // Process when a new reply is made from bbcode / wysiwyg editor
        $my         = JFactory::getUser();
        $config     = DiscussHelper::getConfig();
        $ajax       = new Disjax();
        $acl        = DiscussHelper::getHelper( 'ACL' );
        $post       = JRequest::get( 'POST' );

        // @task: User needs to be logged in, in order to submit a new reply.
        if( !$acl->allowed('add_reply', '0') && $my->id == 0 )
        {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_INORDER_TO_REPLY');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        if( !$acl->allowed('add_reply', '0') )
        {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        if( !isset( $post[ 'parent_id' ] ) )
        {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        $question       = DiscussHelper::getTable( 'Post' );
        $state          = $question->load( $post[ 'parent_id' ] );

        if( !$state )
        {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        // Ensure that the user really has access to the discussion
        if ($question->private && $my->id != $question->user_id && !DiscussHelper::isSiteAdmin() && !DiscussHelper::isModerator($post->category_id, $my->id)) {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        $questionCategory   = DiscussHelper::getTable( 'Category' );
        $questionCategory->load( $question->category_id );

        $questionAccess     = $question->getAccess( $questionCategory );

        if( !$questionAccess->canReply() )
        {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ENTRY_NO_PERMISSION_TO_REPLY');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        if( empty( $post[ 'dc_reply_content' ] ) )
        {
            // Append result
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        if( empty($my->id) )
        {
            if(empty($post['user_type']))
            {
                // Append result
                $output = array();
                $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_INVALID_USER_TYPE');
                $output[ 'type' ]       = 'error';

                echo $this->_outputJson( $output );
                return false;
            }

            if(!DiscussUserHelper::validateUserType($post['user_type']))
            {
                $output = array();
                $output[ 'message' ]    = JText::sprintf('COM_EASYDISCUSS_THIS_USERTYPE_HAD_BEEN_DISABLED', $post['user_type']);
                $output[ 'type' ]       = 'error';

                echo $this->_outputJson( $output );
                return false;
            }

            if( empty($post['poster_name']) || empty($post['poster_email']) )
            {
                $output = array();
                $output[ 'message' ]    = JText::sprintf('COM_EASYDISCUSS_GUEST_SIGN_IN_DESC');
                $output[ 'type' ]       = 'error';

                echo $this->_outputJson( $output );
                return false;
            }
        }
        else
        {
            $post['user_type']      = 'member';
            $post['poster_name']    = '';
            $post['poster_email']   = '';
        }

        // get id if available
        $id     = 0;

        // set alias
        $post['alias']  = DiscussHelper::getAlias( $post['title'], 'post' );

        // set post owner
        $post['user_id']            = $my->id;

        $content    = JRequest::getVar( 'dc_reply_content', '', 'post', 'none' , JREQUEST_ALLOWRAW );
        $content    = DiscussHelper::getHelper( 'String ')->unhtmlentities($content);

        // Rebind the post data
        $post[ 'dc_reply_content' ] = $content;
        $post[ 'content_type' ]     = DiscussHelper::getEditorType( 'reply' );

        // Set the ip address
        $post[ 'ip' ]   = JRequest::getVar( 'REMOTE_ADDR' , '' , 'SERVER' );

        // bind the table
        $table      = DiscussHelper::getTable( 'Post' );
        $table->bind( $post , true );

        // Set the category id for the reply since we might need to use this for acl checks.
        $table->category_id     = $question->category_id;

        if($config->get('main_moderatepost', 0) && !DiscussHelper::isModerateThreshold( $my->id ) && !DiscussHelper::isSiteAdmin( $post->user_id ) && !DiscussHelper::isModerator($post->category_id, $my->id) )
        {
            $table->published   = DISCUSS_ID_PENDING;
        }
        else
        {
            $table->published   = DISCUSS_ID_PUBLISHED;
        }

        require_once DISCUSS_CLASSES . '/recaptcha.php';

        if( DiscussRecaptcha::isRequired() )
        {
            $obj = DiscussRecaptcha::recaptcha_check_answer( $config->get('antispam_recaptcha_private') , $_SERVER['REMOTE_ADDR'] , $post['recaptcha_challenge_field'] , $post['recaptcha_response_field'] );

            if(!$obj->is_valid)
            {
                $output = array();
                $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE');
                $output[ 'type' ]       = 'error.captcha';

                echo $this->_outputJson( $output );
                return false;
            }
        }
        else if( $config->get( 'antispam_easydiscuss_captcha' ) )
        {
            $runCaptcha = DiscussHelper::getHelper( 'Captcha' )->showCaptcha();

            if( $runCaptcha )
            {
                $response = JRequest::getVar( 'captcha-response' );
                $captchaId = JRequest::getInt( 'captcha-id' );

                $discussCaptcha = new stdClass();
                $discussCaptcha->captchaResponse = $response;
                $discussCaptcha->captchaId = $captchaId;

                $state = DiscussHelper::getHelper( 'Captcha' )->verify( $discussCaptcha );

                if( !$state )
                {
                    $output = array();
                    $output[ 'message' ]    = JText::sprintf('COM_EASYDISCUSS_INVALID_CAPTCHA');
                    $output[ 'type' ]       = 'error';

                    echo $this->_outputJson( $output );
                    return false;
                }
            }
        }

        if( $config->get( 'antispam_akismet' ) && ( $config->get('antispam_akismet_key') ) )
        {
            require_once DISCUSS_CLASSES . '/akismet.php';

            $data = array(
                            'author'    => $my->name,
                            'email'     => $my->email,
                            'website'   => DISCUSS_JURIROOT ,
                            'body'      => $post['dc_reply_content'] ,
                            'alias'     => ''
                        );

            $akismet = new Akismet( DISCUSS_JURIROOT , $config->get( 'antispam_akismet_key' ) , $data );

            if( !$akismet->errorsExist() )
            {
                if( $akismet->isSpam() )
                {
                    $output = array();
                    $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_AKISMET_SPAM_DETECTED');
                    $output[ 'type' ]       = 'error';

                    echo $this->_outputJson( $output );
                    return false;
                }
            }
        }

        // hold last inserted ID in DB
        $lastId = null;

        // @rule: Bind parameters
        $table->bindParams( $post );

        $isNew  = true;

        // @trigger: onBeforeSave
        DiscussEventsHelper::importPlugin( 'content' );
        DiscussEventsHelper::onContentBeforeSave('reply', $table , $isNew);

        if ( !$table->store() )
        {
            $output = array();
            $output[ 'message' ]    = JText::_('COM_EASYDISCUSS_ERROR_SUBMIT_REPLY');
            $output[ 'type' ]       = 'error';

            echo $this->_outputJson( $output );
            return false;
        }

        // Process poll items.
        if( $config->get( 'main_polls_replies' ) )
        {
            $polls          = JRequest::getVar( 'pollitems' );

            if( !is_array( $polls ) )
            {
                $polls      = array( $polls );
            }

            // If the post is being edited and
            // there is only 1 poll item which is also empty,
            // we need to delete existing polls tied to this post.
            //if( count( $polls ) == 1 && empty( $polls[0] ) && !$isNew )
            if( !$isNew )
            {
                $post->removePoll();
            }

            if( count( $polls ) > 0 )
            {
                $hasPolls       = false;

                foreach( $polls as $poll )
                {
                    // As long as there is 1 valid poll, we need to store them.
                    if( !empty( $poll ) )
                    {
                        $hasPolls   = true;
                        break;
                    }
                }

                if( $hasPolls )
                {
                    $pollItems      = JRequest::getVar( 'pollitems' );

                    // Check if the multiple polls checkbox is it checked?
                    $multiplePolls  = JRequest::getVar( 'multiplePolls' , '0' );

                    if( $pollItems )
                    {
                        // As long as we need to create the poll answers, we need to create the main question.
                        $pollTitle  = JRequest::getVar( 'poll_question' , '' );

                        // Since poll question are entirely optional.
                        $pollQuestion   = DiscussHelper::getTable( 'PollQuestion' );
                        $pollQuestion->loadByPost( $table->id );

                        $pollQuestion->post_id  = $table->id;
                        $pollQuestion->title    = $pollTitle;
                        $pollQuestion->multiple = $config->get( 'main_polls_multiple' ) ? $multiplePolls : false;
                        $pollQuestion->store();

                        if( !$isNew )
                        {
                            // Try to detect which poll items needs to be removed.
                            $remove = JRequest::getVar( 'pollsremove' );

                            if( !empty( $remove ) )
                            {
                                $remove = explode( ',' , $remove );

                                foreach( $remove as $id )
                                {
                                    $id     = (int) $id;
                                    $poll   = DiscussHelper::getTable( 'Poll' );
                                    $poll->load( $id );
                                    $poll->delete();
                                }
                            }
                        }

                        foreach( $pollItems as $item )
                        {
                            $value  = (string) $item;

                            if( trim( $value ) == '' )
                                continue;

                            $poll   = DiscussHelper::getTable( 'Poll' );

                            if( !$poll->loadByValue( $value , $table->id , $multiplePolls ) )
                            {

                                $poll->set( 'value'         , $value );
                                $poll->set( 'post_id'       , $table->get( 'id' ) );

                                $poll->store();
                            }
                        }
                    }
                }
            }
        }

        // Process custom fields
        $this->saveCustomFieldsValue( $table->id );

        // @trigger: onAfterSave
        DiscussEventsHelper::onContentAfterSave('reply', $table , $isNew);

        // @rule: Add notifications for the thread starter
        if( $table->published && $config->get( 'main_notifications_reply') )
        {
            // Get all users that are subscribed to this post
            $model          = $this->getModel( 'Posts' );
            $participants   = $model->getParticipants( $table->parent_id );

            // Add the thread starter into the list of participants.
            $participants[] = $question->get( 'user_id' );

            // Notify all subscribers
            foreach( $participants as $participant )
            {
                if( $participant != $my->id )
                {
                    $notification   = DiscussHelper::getTable( 'Notifications' );

                    $notification->bind( array(
                            'title'     => JText::sprintf( 'COM_EASYDISCUSS_REPLY_DISCUSSION_NOTIFICATION_TITLE' , $question->get( 'title' ) ),
                            'cid'       => $question->get( 'id' ),
                            'type'      => DISCUSS_NOTIFICATIONS_REPLY,
                            'target'    => $participant,
                            'author'    => $table->get( 'user_id' ),
                            'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $question->get( 'id' )
                        ) );
                    $notification->store();
                }
            }

            // @rule: Detect if any names are being mentioned in the post
            $names          = DiscussHelper::getHelper( 'String' )->detectNames( $table->content );

            if( $names )
            {
                foreach( $names as $name )
                {
                    $name           = JString::str_ireplace( '@' , '' , $name );
                    $id             = DiscussHelper::getUserId( $name );

                    if( !$id || $id == $table->get( 'user_id') )
                    {
                        continue;
                    }

                    $notification   = DiscussHelper::getTable( 'Notifications' );

                    $notification->bind( array(
                            'title'     => JText::sprintf( 'COM_EASYDISCUSS_MENTIONED_REPLY_NOTIFICATION_TITLE' , $question->get( 'title' ) ),
                            'cid'       => $question->get( 'id' ),
                            'type'      => DISCUSS_NOTIFICATIONS_MENTIONED,
                            'target'    => $id,
                            'author'    => $table->get( 'user_id' ),
                            'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $question->get( 'id' )
                        ) );
                    $notification->store();
                }
            }
        }

        if( $table->published && !$question->private)
        {
            // Create notification item in EasySocial
            DiscussHelper::getHelper( 'EasySocial' )->notify( 'new.reply' , $table , $question );

            // @rule: Badges
            DiscussHelper::getHelper( 'History' )->log( 'easydiscuss.new.reply' , $table->user_id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_REPLY', $question->title), $table->id );
            DiscussHelper::getHelper( 'Badges' )->assign( 'easydiscuss.new.reply' , $table->user_id );
            DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.new.reply' , $table->user_id, $table);

            // Assign badge for EasySocial
            DiscussHelper::getHelper( 'EasySocial' )->assignBadge( 'reply.question' , $table->user_id , JText::sprintf( 'COM_EASYDISCUSS_BADGES_HISTORY_NEW_REPLY' , $question->title ) );

            // @rule: AUP integrations
            DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_NEW_REPLY , $table->user_id , $question->title );

            // @rule: ranking
            DiscussHelper::getHelper( 'ranks' )->assignRank( $table->user_id, $config->get( 'main_ranking_calc_type' ) );
        }

        // Bind file attachments
        if( $acl->allowed( 'add_attachment' , '0' ) )
        {
            if ( !$table->bindAttachments() && $table->getError())
            {
                $output = array();
                $output[ 'message' ]    = $table->getError();
                $output[ 'type' ]       = 'error';

                echo $this->_outputJson( $output );
                return false;
            }
        }

        $replier     = new stdClass();

        if($my->id > 0)
        {
            $replier->id    = $my->id;
            $replier->name  = $my->name;
        }
        else
        {
            $replier->id    = 0;
            $replier->name  = JText::_('COM_EASYDISCUSS_GUEST'); // TODO: user the poster_name
        }

        //load porfile info and auto save into table if user is not already exist in discuss's user table.
        $creator = DiscussHelper::getTable( 'Profile' );
        $creator->load( $replier->id);

        $table->user = $creator;

        $voteModel = $this->getModel('votes');

        // clean up bad code
        $table->content_raw = $table->content;
        //$table->content       = DiscussHelper::parseContent( $table->content );

        // @rule: URL References
        $table->references  = $table->getReferences();

        // Since this is a new reply, it's impossible that it has been voted before.
        $table->voted       = false;

        // get total vote for this reply
        $table->totalVote   = $table->sum_totalvote;

        $result['status']   = 'success';
        $result['title']    = JText::_('COM_EASYDISCUSS_SUCCESS_SUBMIT_REPLY');
        $result['id']       = $table->id;
        $result['message']  = JText::_('COM_EASYDISCUSS_REPLY_SAVED');

        $table->title       = DiscussHelper::wordFilter( $table->title);
        $table->content     = DiscussHelper::wordFilter( $table->content);

        // Legacy fix when switching from WYSIWYG editor to bbcode.
        $table->content     = EasyDiscussParser::html2bbcode( $table->content );

        $table->content     = DiscussHelper::formatContent( $table , true );

        //all access control goes here.
        $canDelete      = false;
        $isMainLocked   = false;

        if( DiscussHelper::isSiteAdmin() || $acl->allowed('delete_reply', '0') || $table->user_id == $my->id )
        {
            $canDelete  = true;
        }

        $parent         = DiscussHelper::getTable( 'Post' );
        $parent->load( $table->parent_id );

        $isMainLocked   = $parent->islock;

        //default value
        $table->isVoted         = 0;
        $table->total_vote_cnt  = 0;
        $table->likesAuthor     = '';
        $table->minimize        = 0;

        if ( $config->get( 'main_content_trigger_replies' ) )
        {
            $tempContent = $table->content;
            $table->content = str_replace( '@', '&#64;', $tempContent);

            // process content plugins
            DiscussEventsHelper::importPlugin( 'content' );
            DiscussEventsHelper::onContentPrepare('reply', $table);

            $table->event = new stdClass();

            $results    = DiscussEventsHelper::onContentBeforeDisplay('reply', $table);
            $table->event->beforeDisplayContent = trim(implode("\n", $results));

            $results    = DiscussEventsHelper::onContentAfterDisplay('reply', $table);
            $table->event->afterDisplayContent  = trim(implode("\n", $results));
        }

        $tpl    = new DiscussThemes();

        $category       = DiscussHelper::getTable( 'Category' );
        $category->load( $question->category_id );

        $table->access  = $table->getAccess( $category );

        // Since the reply dont have any comments yet.
        $table->comments    = array();

        $tpl->set('category', $category);
        $tpl->set( 'post'           , $table );
        $tpl->set( 'question'       , $parent );
        $tpl->set( 'isMine'         , DiscussHelper::isMine( $parent->user_id) );
        $tpl->set( 'isAdmin'        , DiscussHelper::isSiteAdmin() );
        $tpl->set( 'isMainLocked'   , $isMainLocked);

        $recaptcha  = '';
        $enableRecaptcha    = $config->get('antispam_recaptcha', 0);
        $publicKey          = $config->get('antispam_recaptcha_public');


        $html   = ( $table->published == DISCUSS_ID_PENDING ) ? $tpl->fetch( 'post.reply.item.moderation.php' ) : $tpl->fetch( 'post.reply.item.php' );

        //send notification to all comment's subscribers that want to receive notification immediately
        $notify = DiscussHelper::getNotification();
        $excludeEmails = array();

        $attachments    = $table->getAttachments();
        $emailData['attachments']   = $attachments;
        $emailData['postTitle']     = $parent->title;
        $emailData['comment']       = DiscussHelper::parseContent( $table->content );
        $emailData['commentAuthor'] = ($my->id) ? $creator->getName() : $table->poster_name;
        $emailData['postLink']      = DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parent->id, false, true);

        $emailContent   = $table->content;

        $isEditing = $isNew == true ? false : true;
        $emailContent = DiscussHelper::bbcodeHtmlSwitcher( $table, 'reply', $isEditing );

        $emailContent   = $question->trimEmail( $emailContent );

        $emailData['replyContent']  = $emailContent;
        $emailData['replyAuthor' ]  = ($my->id) ? $creator->getName() : $table->poster_name;
        $emailData['replyAuthorAvatar' ] = $creator->getAvatar();
        $emailData['post_id']       = $parent->id;
        $emailData['cat_id']        = $parent->category_id;

        $subscriberEmails           = array();

        if( ($config->get('main_sitesubscription') ||  $config->get('main_postsubscription') ) && $config->get('notify_subscriber') && $table->published == DISCUSS_ID_PUBLISHED)
        {
            $emailData['emailTemplate'] = 'email.subscription.reply.new.php';
            $emailData['emailSubject']  = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);

            $posterEmail        = $post['poster_email'] ? $post['poster_email'] : $my->email;

            // Get the emails of user who subscribe to this post only
            // This does not send to subscribers whom subscribe to site and category
            $subcribersEmails   = DiscussHelper::getHelper( 'Mailer' )->notifyThreadSubscribers( $emailData, array($posterEmail, $my->email) );

            $excludeEmails[]    = $posterEmail;
            $excludeEmails      = array_merge( $excludeEmails, $subcribersEmails);
            $excludeEmails      = array_unique( $excludeEmails );
        }

        //notify post owner.
        $postOwnerId    = $parent->user_id;
        $postOwner      = JFactory::getUser( $postOwnerId );
        $ownerEmail     = $postOwner->email;

        if( $parent->user_type != 'member' )
        {
            $ownerEmail     = $parent->poster_email;
        }

        // Notify Owner
        // if reply under moderation, send owner a notification.
        if( $config->get( 'notify_owner' ) && $table->published == DISCUSS_ID_PUBLISHED && ($postOwnerId != $replier->id) && !in_array( $ownerEmail , $excludeEmails ) && !empty( $ownerEmail ) )
        {

            $emailData['owner_email'] = $ownerEmail;
            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
            $emailData['emailTemplate'] = 'email.post.reply.new.php';
            DiscussHelper::getHelper( 'Mailer' )->notifyThreadOwner( $emailData );

            // Notify Participants
            $excludeEmails[] = $ownerEmail;
            $excludeEmails   = array_unique( $excludeEmails );
        }

        if( $config->get( 'notify_participants' ) && $table->published  == DISCUSS_ID_PUBLISHED )
        {
            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
            $emailData['emailTemplate'] = 'email.post.reply.new.php';
            DiscussHelper::getHelper( 'Mailer' )->notifyThreadParticipants( $emailData, $excludeEmails );
        }


        if( $table->published == DISCUSS_ID_PENDING )
        {
            // Notify admins.

            // Generate hashkeys to map this current request
            $hashkey        = DiscussHelper::getTable( 'Hashkeys' );
            $hashkey->uid   = $table->id;
            $hashkey->type  = DISCUSS_REPLY_TYPE;
            $hashkey->store();

            require_once DISCUSS_HELPERS . '/router.php';
            $approveURL     = DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
            $rejectURL      = DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
            $emailData[ 'moderation' ]  = '<div style="display:inline-block;width:100%;padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:\'Lucida Grande\',Tahoma,Arial;font-size:12px;text-align:left">';
            $emailData[ 'moderation' ] .= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_REPLY' ) . '</a>';
            $emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . ' <a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';
            $emailData[ 'moderation' ] .= '</div>';

            $emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_MODERATE', $parent->title);
            $emailData['emailTemplate'] = 'email.post.reply.moderation.php';

            DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin' ), $config->get( 'notify_moderator' ) );

        } elseif( $table->published == DISCUSS_ID_PUBLISHED && !$question->private) {

            $emailData['emailTemplate'] = 'email.post.reply.new.php';
            $emailData['emailSubject']  = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
            $emailData['post_id'] = $parent->id;

            DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, $excludeEmails, $config->get( 'notify_admin_onreply' ), $config->get( 'notify_moderator_onreply' ) );
        }

        // @rule: Jomsocial activity integrations
        if( $table->published == DISCUSS_ID_PUBLISHED && !$question->private)
        {
            DiscussHelper::getHelper( 'jomsocial' )->addActivityReply( $table );
            DiscussHelper::getHelper( 'easysocial')->replyDiscussionStream( $table );
        }

        $autoSubscribed = false;

        if( $config->get('main_autopostsubscription') && $config->get('main_postsubscription') && $table->user_type != 'twitter')
        {
            //automatically subscribe this user into this post.
            $subscription_info = array();
            $subscription_info['type']      = 'post';
            $subscription_info['userid']    = ( !empty($table->user_id) ) ? $table->user_id : '0';
            $subscription_info['email']     = ( !empty($table->user_id) ) ? $my->email : $table->poster_email;;
            $subscription_info['cid']       = $parent->id;
            $subscription_info['member']    = ( !empty($table->user_id) ) ? '1':'0';
            $subscription_info['name']      = ( !empty($table->user_id) ) ? $my->name : $table->poster_name;
            $subscription_info['interval']  = 'instant';

            $model  = $this->getModel( 'Subscribe' );
            $sid     = '';

            if( $subscription_info['userid'] == 0)
            {
                $sid = $model->isPostSubscribedEmail($subscription_info);
                if( empty( $sid ) )
                {
                    if( $model->addSubscription($subscription_info))
                    {
                        $autoSubscribed = true;
                    }
                }
            }
            else
            {
                $sid = $model->isPostSubscribedUser($subscription_info);
                if( empty( $sid['id'] ))
                {
                    //add new subscription.
                    if( $model->addSubscription($subscription_info) )
                    {
                        $autoSubscribed = true;
                    }
                }
            }
        }

        // Append result
        $output                 = array();
        $output[ 'message' ]    = ($autoSubscribed) ? JText::_( 'COM_EASYDISCUSS_SUCCESS_REPLY_POSTED_AND_SUBSCRIBED' ) : JText::_( 'COM_EASYDISCUSS_SUCCESS_REPLY_POSTED' );
        $output[ 'type' ]       = 'success';
        $output[ 'html' ]       = $html;


        // Perhaps the viewer is unable to view the replies.
        if (!$questionCategory->canViewReplies()) {
            $output['message']  = JText::_('COM_EASYDISCUSS_REPLY_SUCCESS_BUT_UNABLE_TO_VIEW_REPLIES');
        }

        if(  $enableRecaptcha && !empty( $publicKey ) && $recaptcha )
        {
            $output[ 'type' ]   = 'success.captcha';
        }

        echo $this->_outputJson( $output );
    }

    private function _outputJson( $output = null )
    {
        return '<script type="text/json" id="ajaxResponse">' . $this->json_encode( $output ) . '</script>';
    }

    /**
     * Delete post
     * and delete all reply as well
     */
    public function ajaxDeleteReply( $postId = null )
    {
        $djax   = new Disjax();
        $my     = JFactory::getUser();

        if(empty($postId))
        {
            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_ERROR_DELETE_REPLY_TITLE');
            $options->content = JText::_('COM_EASYDISCUSS_MISSING_POST_ID');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
            return;
        }

        // bind the table
        $postTable      = DiscussHelper::getTable( 'Post' );
        $postTable->load( $postId );

        $isMine     = DiscussHelper::isMine($postTable->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();

        if ( !$isMine && !$isAdmin )
        {
            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_ERROR_DELETE_REPLY_TITLE');
            $options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_DELETE');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
            return;
        }

        //chekc if the parent being locked. if yes, do not allow delete.
        $parentId       = $postTable->parent_id;
        $parentTable    = DiscussHelper::getTable( 'Post' );
        $parentTable->load( $parentId );

        if($parentTable->islock)
        {
            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_ERROR_DELETE_REPLY_TITLE');
            $options->content = JText::_('COM_EASYDISCUSS_MAIN_POST_BEING_LOCKED');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
            return;
        }

        // @trigger: onBeforeDelete
        DiscussEventsHelper::importPlugin( 'content' );
        DiscussEventsHelper::onContentBeforeDelete('reply', $postTable);

        if( !$postTable->delete() )
        {
            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_ERROR_DELETE_REPLY_TITLE');
            $options->content = JText::_('COM_EASYDISCUSS_ERROR_DELETE_REPLY');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
            return;
        }
        else
        {
            // @trigger: onAfterDelete
            DiscussEventsHelper::onContentAfterDelete('reply', $postTable);

            // @rule: Process AUP integrations
            DiscussHelper::getHelper( 'Aup' )->assign( DISCUSS_POINTS_DELETE_REPLY , $postTable->user_id , $parentTable->title );

            DiscussHelper::getHelper( 'Points' )->assign( 'easydiscuss.remove.reply' , $my->id );

            $djax->script('EasyDiscuss.$("#dc_reply_' . + $postId .'").fadeOut(\'500\');');
            $djax->script('EasyDiscuss.$("#dc_reply_' . + $postId .'").remove();');

            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_SUCCESS_DELETE_REPLY_TITLE');
            $options->content = JText::_('COM_EASYDISCUSS_SUCCESS_DELETE_REPLY');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
        }

        $djax->send();
        return;
    }

    /**
     * Get edit form with all details
     */
    public function ajaxGetEditForm( $postId = null )
    {
        $config     = DiscussHelper::getConfig();
        $djax       = new Disjax();
        $my         = JFactory::getUser();
        $id         = $postId;

        $postTable  = DiscussHelper::getTable( 'Post' );
        $postTable->load( $id );

        $isMine     = DiscussHelper::isMine($postTable->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();

        if ( !$isMine && !$isAdmin )
        {
            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_ERROR');
            $options->content = JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_PERFORM_THE_REQUESTED_ACTION');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
            return;
        }

        if ( empty($id) )
        {
            $options = new stdClass();
            $options->title = JText::_('COM_EASYDISCUSS_ERROR');
            $options->content = JText::_('COM_EASYDISCUSS_ERROR_LOAD_POST');

            $buttons            = array();
            $button             = new stdClass();
            $button->title      = JText::_( 'COM_EASYDISCUSS_OK' );
            $button->action     = 'disjax.closedlg();';
            $button->className  = 'btn-primary';
            $buttons[]          = $button;
            $options->buttons   = $buttons;

            $djax->dialog( $options );
            $djax->send();
            return;
        }
        else
        {
            // get post tags
            $postsTagsModel = $this->getModel('PostsTags');

            $tags = $postsTagsModel->getPostTags( $id );

            // clean up bad code
            $postTable->tags    = $tags;

            $result['status']   = 'success';
            $result['id']       = $postTable->id;

            // select top 20 tags.
            $tagmodel   = $this->getModel( 'Tags' );
            $tags       = $tagmodel->getTagCloud('20','post_count','DESC');

            //recaptcha integration
            $recaptcha  = '';
            $enableRecaptcha    = $config->get('antispam_recaptcha');
            $publicKey          = $config->get('antispam_recaptcha_public');
            $skipRecaptcha      = $config->get('antispam_skip_recaptcha');

            $model      = DiscussHelper::getModel( 'Posts' );
            $postCount  = count( $model->getPostsBy( 'user' , $my->id ) );

            if( $enableRecaptcha && !empty( $publicKey ) && $postCount < $skipRecaptcha )
            {
                require_once DISCUSS_CLASSES . '/recaptcha.php';
                $recaptcha  = getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl') );
            }

            $tpl    = new DiscussThemes();
            $tpl->set( 'post'       , $postTable );
            $tpl->set( 'config'     , $config );
            $tpl->set( 'tags'       , $tags );
            $tpl->set( 'recaptcha'  , $recaptcha );
            $tpl->set( 'isEditMode' , true );

            $result['output']   = $tpl->fetch('new.post.php');

            $djax->assign('dc_main_post_edit', $result['output']);
            $djax->script('EasyDiscuss.$("#dc_main_post_edit").slideDown(\'fast\');');
            $djax->script('EasyDiscuss.$("#edit_content").markItUp(mySettings);');

        }

        $djax->send();
        return;
    }


    public function ajaxReloadRecaptcha($divId = null, $reId = 'recaptcha-image')
    {
        $config     = DiscussHelper::getConfig();
        $mainframe  = JFactory::getApplication();
        $my         = JFactory::getUser();
        $djax       = new Disjax();

        //recaptcha integration
        $recaptcha  = '';
        $enableRecaptcha    = $config->get('antispam_recaptcha', 0);
        $publicKey          = $config->get('antispam_recaptcha_public');
        $skipRecaptcha      = $config->get('antispam_skip_recaptcha');

        $model      = DiscussHelper::getModel( 'Posts' );
        $postCount  = count( $model->getPostsBy( 'user' , $my->id ) );

        if( $enableRecaptcha && !empty( $publicKey ) && $postCount < $skipRecaptcha )
        {
            require_once DISCUSS_CLASSES . '/recaptcha.php';
            $recaptcha  = getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl'), $reId );

            $djax->assign($divId, $recaptcha);
        }
        else
        {
            //somehow ajax must return something.
            $djax->assign($divId, '');
        }

        $djax->send();
        return;
    }

    public function ajaxIsFav( $postId )
    {
        $my     = JFactory::getUser();
        //$postId   = JRequest::getInt( 'postId' );
        $db     = DiscussHelper::getDBO();

        $query  = ' SELECT '.$db->nameQuote('id').' FROM '.$db->nameQuote('#__discuss_favourites');
        $query  .= ' WHERE '.$db->nameQuote('user_id'). ' = '.$db->quote($my->id);
        $query  .= ' AND '.$db->nameQuote('post_id'). ' = '.$db->quote($postId);


        $db->setQuery($query);
        $result = $db->loadResult();

        $ajax = DiscussHelper::getHelper( 'ajax' );

        if(empty( $result ))
        {
            // This post haven't favourite
            $ajax->success(0);
        }
        else
        {
            // This post is already favourite
            $ajax->success(1);
        }
        $ajax->send();
    }


    /**
     * Displays a form to confirm to feature a discussion.
     *
     * @since   3.0
     * @access  public
     *
     */
    public function confirmFeature( $postId )
    {
        $ajax       = new Disjax();
        $theme      = new DiscussThemes();

        $theme->set( 'id'   , $postId );
        $content    = $theme->fetch( 'ajax.feature.php' , array('dialog'=> true ) );

        $options    = new stdClass();
        $options->content   = $content;

        $options->title     = JText::_( 'COM_EASYDISCUSS_CONFIRM_FEATURE_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
        $button->form       = '#frmFeature';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    /**
     * Displays a form to confirm to feature a discussion.
     *
     * @since   3.0
     * @access  public
     *
     */
    public function confirmUnfeature( $postId )
    {
        $ajax       = new Disjax();
        $theme      = new DiscussThemes();

        $theme->set( 'id'   , $postId );
        $content    = $theme->fetch( 'ajax.unfeature.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;

        $options->title     = JText::_( 'COM_EASYDISCUSS_CONFIRM_UNFEATURE_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_NO' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_YES' );
        $button->form       = '#frmUnfeature';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    public function ajaxSetFavouritePost( $postId )
    {
        $my     = JFactory::getUser();
        //$postId   = JRequest::getInt( 'postId' );
        $date   = DiscussHelper::getDate();
        $get    = DiscussHelper::getTable( 'Favourites' );

        // Set your favourite post here..
        $favArray               = array();
        $favArray['user_id']    = $my->id;
        $favArray['post_id']    = $postId;
        $favArray['created']    = $date->toMySQL();

        $get->bind( $favArray );
        $get->store();

        $ajax = DiscussHelper::getHelper( 'ajax' );
        $ajax->success();
        $ajax->send();
    }

    public function ajaxRemoveFavouritePost( $postId )
    {
        $my     = JFactory::getUser();
        //$postId   = JRequest::getInt( 'postId' );
        $date   = DiscussHelper::getDate();
        $get    = DiscussHelper::getTable( 'Favourites' );

        // Set your favourite post here..
        $favArray               = array();
        $favArray['user_id']    = $my->id;
        $favArray['post_id']    = $postId;
        $favArray['created']    = $date->toMySQL();

        $key = $get->load( '0', $my->id, $postId );
        $get->delete( $key );

        $ajax = DiscussHelper::getHelper( 'ajax' );
        $ajax->success();
        $ajax->send();
    }

    /**
     * Displays the report form dialog
     *
     * @since   3.0
     */
    public function reportForm( $id = null )
    {
        $config = DiscussHelper::getConfig();
        $my   = JFactory::getUser();
        $disjax = new Disjax();

        $template   = new DiscussThemes();
        $template->set( 'id' , $id );
        $html       = $template->fetch( 'ajax.report.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->title     = JText::_('COM_EASYDISCUSS_REPORT_ABUSE');
        $options->content   = $html;


        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_SUBMIT' );
        $button->form       = '#reportForm';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $disjax->dialog($options);

        $disjax->send();
    }


    private function _fieldValidate($post = null)
    {

        $mainframe  = JFactory::getApplication();
        $valid      = true;

        $message    = '<ul class="reset-ul">';

        if(JString::strlen($post['title']) == 0 || $post['title'] == JText::_('COM_EASYDISCUSS_POST_TITLE_EXAMPLE'))
        {
            $messag .= '<li>' . JText::_('COM_EASYDISCUSS_POST_TITLE_CANNOT_EMPTY') . '</li>';
            $valid  = false;
        }

        if(JString::strlen($post['dc_reply_content']) == 0)
        {
            $messag .= '<li>' . JText::_('COM_EASYDISCUSS_POST_CONTENT_IS_EMPTY') . '</li>';
            $valid  = false;
        }

        $tags           = '';
        if(! isset($post['tags[]']))
        {
            $messag .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG') . '</li>';
            $valid  = false;
        }
        else
        {
            $tags           = $post['tags[]'];
            if(empty($tags))
            {
                $messag .= '<li>' . JText::_('COM_EASYDISCUSS_POST_EMPTY_TAG') . '</li>';
                $valid  = false;
            }
        }

        $message .= '</ul>';

        $returnVal      = array();

        $returnVal[]    = $valid;
        $returnVal[]    = $message;

        return $returnVal;
    }


    private function _validateCommentFields($post = null)
    {
        $config = DiscussHelper::getConfig();

        if(JString::strlen($post['comment']) == 0)
        {
            $this->err[0]   = JText::_( 'COM_EASYDISCUSS_COMMENT_IS_EMPTY' );
            $this->err[1]   = 'comment';
            return false;
        }

        if($config->get('main_comment_tnc') == true)
        {
            if(empty($post['tnc']))
            {
                $this->err[0]   = JText::_( 'COM_EASYDISCUSS_TERMS_PLEASE_ACCEPT' );
                $this->err[1]   = 'tnc';
                return false;
            }
        }

        return true;
    }

    public function _trim(&$text = null)
    {
        $text = JString::trim($text);
    }

    public function ajaxSubscribe($id = null)
    {
        $disjax     = new disjax();
        $mainframe  = JFactory::getApplication();
        $my         = JFactory::getUser();
        $sitename   = $mainframe->getCfg('sitename');

        $tpl    = new DiscussThemes();
        $tpl->set( 'id', $id );
        $tpl->set( 'my', $my );
        $content    = $tpl->fetch( 'ajax.subscribe.post.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->title     = JText::_( 'COM_EASYDISCUSS_SUBSCRIBE_TO_POST' );
        $options->content   = $content;

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_SUBSCRIBE' );
        $button->action     = 'discuss.subscribe.post(' . $id . ')';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $disjax->dialog($options);

        $disjax->send();
    }

    public function ajaxAddSubscription($type = 'post', $email = null, $name = null, $interval = null, $cid = '0')
    {
        $disjax     = new Disjax();
        $mainframe  = JFactory::getApplication();
        $my         = JFactory::getUser();
        $config     = DiscussHelper::getConfig();
        $msg        = '';
        $msgClass   = 'dc_success';

        $JFilter    = JFilterInput::getInstance();
        $name       = $JFilter->clean($name, 'STRING');

        jimport( 'joomla.mail.helper' );

        if( !JMailHelper::isEmailAddress($email) )
        {
            $disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
            $disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_INVALID_EMAIL') );
            $disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "alert alert-error" );' );
            $disjax->send();
            return;
        }

        $subscription_info = array();
        $subscription_info['type'] = $type;
        $subscription_info['userid'] = $my->id;
        $subscription_info['email'] = $email;
        $subscription_info['cid'] = $cid;
        $subscription_info['member'] = ($my->id)? '1':'0';
        $subscription_info['name'] = ($my->id)? $my->name : $name;
        $subscription_info['interval'] = $interval;

        //validation
        if(JString::trim($subscription_info['email']) == '')
        {
            $disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
            $disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_EMAIL_IS_EMPTY') );
            $disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "alert alert-error" );' );
            $disjax->send();
            return;
        }

        if(JString::trim($subscription_info['name']) == '')
        {
            $disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
            $disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_NAME_IS_EMPTY') );
            $disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "alert alert-error" );' );
            $disjax->send();
            return;
        }

        $model  = $this->getModel( 'Subscribe' );
        $sid    = '';


        if($my->id == 0)
        {
            $sid = $model->isPostSubscribedEmail($subscription_info);
            if($sid != '')
            {
                //user found.
                // show message.
                $disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
                $disjax->assign( 'dc_subscribe_notification .msg_in' , JText::_('COM_EASYDISCUSS_ALREADY_SUBSCRIBED_TO_POST') );
                $disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "dc_alert" );' );
                $disjax->send();
                return;

            }
            else
            {
                if(!$model->addSubscription($subscription_info))
                {
                    $msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
                    $msgClass = 'alert alert-error';
                }
            }
        }
        else
        {
            $sid = $model->isPostSubscribedUser($subscription_info);

            if($sid['id'] != '')
            {
                // user found.
                // update the email address
                if(!$model->updatePostSubscription($sid['id'], $subscription_info))
                {
                    $msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
                    $msgClass = 'alert alert-error';
                }
            }
            else
            {
                //add new subscription.
                if(!$model->addSubscription($subscription_info))
                {
                    $msg = JText::sprintf('COM_EASYDISCUSS_SUBSCRIPTION_FAILED');
                    $msgClass = 'alert alert-error';
                }
            }
        }

        $msg = empty($msg)? JText::_('COM_EASYDISCUSS_SUBSCRIPTION_SUCCESS') : $msg;

        // Change the email icons to unsubscribe now.
        $disjax->script( 'EasyDiscuss.$(".via-email").removeClass("via-email").addClass( "cancel-email" );');

        $disjax->script( 'discuss.spinner.hide( "dialog_loading" );' );
        $disjax->assign( 'dc_subscribe_notification .msg_in' , $msg );
        $disjax->script( 'EasyDiscuss.$( "#dc_subscribe_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
        $disjax->script( 'EasyDiscuss.$( ".dialog-buttons .si_btn" ).hide();' );
        $disjax->send();
        return;
    }

    public function getMoreVoters($postid = null, $limit = null)
    {
        $disjax     = new disjax();

        $voteModel  = $this->getModel('votes');
        $total      = $voteModel->getTotalVotes( $postid );

        if(!empty($total))
        {
            $voters = DiscussHelper::getVoters($postid, $limit);
            $msg    = JText::sprintf('COM_EASYDISCUSS_VOTES_BY', $voters->voters);

            if($voters->shownVoterCount < $total)
            {
                $limit += '5';

                $msg .= '[<a href="javascript:void(0);" onclick="disjax.load(\'post\', \'getMoreVoters\', \''.$postid.'\', \''.$limit.'\');">'.JText::_('COM_EASYDISCUSS_MORE').'</a>]';
            }

            $disjax->assign( 'dc_reply_voters_'.$postid , $msg );
        }

        $disjax->send();
        return;
    }

    public function deleteAttachment( $id = null )
    {
        require_once JPATH_ROOT . '/components/com_easydiscuss/controllers/attachment.php';

        $disjax     = new Disjax();

        $controller = new EasyDiscussControllerAttachment();

        $msg        = JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_FAILED');
        $msgClass   = 'alert alert-error';
        if($controller->deleteFile($id))
        {
            $msg        = JText::_('COM_EASYDISCUSS_ATTACHMENT_DELETE_SUCCESS');
            $msgClass   = 'dc_success';
            $disjax->script( 'EasyDiscuss.$( "#dc-attachments-'.$id.'" ).remove();' );
        }

        $disjax->assign( 'dc_post_notification .msg_in' , $msg );
        $disjax->script( 'EasyDiscuss.$( "#dc_post_notification .msg_in" ).addClass( "'.$msgClass.'" );' );
        $disjax->script( 'EasyDiscuss.$( "#button-delete-att-'.$id.'" ).prop("disabled", false);' );

        $disjax->send();
    }

    public function nameSuggest( $part )
    {
        $ajax       = DiscussHelper::getHelper( 'Ajax' );
        $db         = DiscussHelper::getDBO();
        $config     = DiscussHelper::getConfig();
        $property   = $config->get( 'layout_nameformat' );

        $query      = 'SELECT a.`id`,a.`' . $property . '` AS title FROM '
                    . $db->nameQuote( '#__users' ) . ' AS a '
                    . 'LEFT JOIN ' . $db->nameQuote( '#__discuss_users' ) . ' AS b '
                    . 'ON a.`id`=b.`id`';

        if( $property == 'nickname' )
        {
            $query  .= ' WHERE b.' . $db->nameQuote( $property ) . ' LIKE ' . $db->Quote( '%' . $part . '%' );
        }
        else
        {
            $query  .= ' WHERE a.' . $db->nameQuote( $property ) . ' LIKE ' . $db->Quote( '%' . $part . '%' );
        }

        $db->setQuery( $query );
        $names      = $db->loadObjectList();

        require_once DISCUSS_CLASSES . '/json.php';
        $json       = new Services_JSON();
        $ajax->success( $json->encode( $names ) );
        $ajax->send();
    }

    /**
     * Displays the embed video dialog
     *
     * @since   2.0
     * @access  public
     * @param   null
     */
    public function showVideoDialog( $element = null , $caretPosition = null )
    {
        $theme = new DiscussThemes();
        $content    = $theme->fetch( 'ajax.video.form.php' , array('dialog'=> true ) );

        $ajax               = new Disjax();

        $options            = new stdClass();
        $options->content   = $content;
        $options->title     = JText::_( 'COM_EASYDISCUSS_BBCODE_INSERT_VIDEO' );

        $buttons            = array();

        // Add buttons
        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        // Add buttons
        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_INSERT' );
        $button->action     = 'insertVideoCode( EasyDiscuss.$("#videoURL").val() , "' . $caretPosition . '" , "' . $element . '" );';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        // Set buttons for this dialog
        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    public function ajaxSaveLabel()
    {
        $ajax   = DiscussHelper::getHelper( 'Ajax' );

        if( !JRequest::checkToken() )
        {
            $ajax->fail( JText::_( 'Invalid Token' ) );
            return $ajax->send();
        }

        $postId     = JRequest::getInt( 'postId', 'post' );
        $labelId    = JRequest::getInt( 'labelId', 'post' );
        $post       = DiscussHelper::getTable( 'Post' );

        if( !$post->load( $postId ) )
        {
            $ajax->fail( 'Cannot load Post ID' );
            return $ajax->send();
        }

        $category   = DiscussHelper::getTable( 'Category' );
        $category->load( $post->category_id );
        $access     = $post->getAccess($category);

        if( !$access->canLabel() )
        {
            $ajax->fail( 'Permission denied' );
            return $ajax->send();
        }

        $postLabel = DiscussHelper::getTable( 'PostLabel' );
        $postLabel->load($post->id);

        // Add new record if assignee was changed
        if( $postLabel->post_label_id != $labelId )
        {
            $newpostLabel = DiscussHelper::getTable( 'PostLabel' );

            $newpostLabel->post_id          = $post->id;
            $newpostLabel->post_label_id    = (int) $labelId;

            if( !$newpostLabel->store() )
            {
                $ajax->fail( 'Storing failed' );
                return $ajax->send();
            }
        }

        // $labels = DiscussHelper::getModel( 'Labels' )->getLabels();

        // $theme   = new DiscussThemes();
        // $theme->set( 'post'      , $post );
        // $theme->set( 'labels'    , $labels );
        // $html    = $theme->fetch( 'post.label.php' );

        $ajax->success( $html );
    }

    public function ajaxModeratorAssign()
    {
        $ajax   = DiscussHelper::getHelper( 'Ajax' );

        if( !JRequest::checkToken() )
        {
            $ajax->fail( JText::_( 'Invalid Token' ) );
            return $ajax->send();
        }

        $postId = JRequest::getInt( 'postId', 'post' );
        $userId = JRequest::getInt( 'userId', 'post' );
        $post   = DiscussHelper::getTable( 'Post' );

        if( !$post->load( $postId ) )
        {
            $ajax->fail( 'Cannot load Post ID' );
            return $ajax->send();
        }

        $category = DiscussHelper::getTable( 'Category' );
        $category->load( $post->category_id );
        $access = $post->getAccess($category);

        if( !$access->canAssign() )
        {
            $ajax->fail( 'Permission denied' );
            return $ajax->send();
        }

        $assignment = DiscussHelper::getTable( 'PostAssignment' );
        $assignment->load($post->id);

        // Add new record if assignee was changed
        if( $assignment->assignee_id != $userId )
        {
            $newAssignment = DiscussHelper::getTable( 'PostAssignment' );

            $newAssignment->post_id     = $post->id;
            $newAssignment->assignee_id = (int) $userId;
            $newAssignment->assigner_id = (int) JFactory::getUser()->id;

            if( !$newAssignment->store() )
            {
                $ajax->fail( 'Storing failed' );
                return $ajax->send();
            }
        }

        $moderators = DiscussHelper::getHelper( 'Moderator' )->getModeratorsDropdown( $post->category_id );

        $theme  = new DiscussThemes();
        $theme->set( 'post'         , $post );
        $theme->set( 'moderators'   , $moderators );
        $html   = $theme->fetch( 'post.assignment.php' );

        $ajax->success( $html );
    }

    /**
     * Check for updates
     *
     * @since   3.0
     * @access  public
     * @param   null
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function getUpdateCount()
    {
        $ajax   = DiscussHelper::getHelper( 'Ajax' );

        $id     = JRequest::getInt( 'id', 0 );

        if( $id === 0 )
        {
            $ajax->reject();
            return $ajax->send();
        }

        $postsModel     = DiscussHelper::getModel( 'posts' );

        $totalReplies   = (int) $postsModel->getTotalReplies( $id );

        $totalComments  = (int) $postsModel->getTotalComments( $id, 'thread' );

        $ajax->resolve( $totalReplies, $totalComments );
        return $ajax->send();
    }

    /**
     * Get comments based on pagination load more
     *
     * @since   3.0
     * @access  public
     * @param   null
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function getComments()
    {
        $theme  = new DiscussThemes();
        $ajax   = DiscussHelper::getHelper( 'Ajax' );
        $model  = DiscussHelper::getModel( 'Posts' );
        $config = DiscussHelper::getConfig();

        $id     = JRequest::getInt( 'id', 0 );

        $start  = JRequest::getInt( 'start', 0 );

        $total  = $model->getTotalComments( $id );

        if( $start >= $total )
        {
            return $ajax->reject();
        }

        $comments = $model->getComments( $id, $config->get( 'main_comment_pagination_count' ), $start );

        if( empty( $comments ) )
        {
            return $ajax->reject();
        }

        $count = count( $comments );

        $nextCycle = ( $start + $count ) < $total;

        $comments = DiscussHelper::formatComments( $comments );

        $html = '';

        foreach( $comments as $comment )
        {
            $theme->set( 'comment', $comment );
            $html .= $theme->fetch( 'post.reply.comment.item.php' );
        }

        return $ajax->resolve( $html, $nextCycle );
    }

    /**
     * Get replies based on pagination load more
     *
     * @since   3.0
     * @access  public
     * @param   null
     * @author  Jason Rey <jasonrey@stackideas.com>
     */
    public function getReplies()
    {
        $theme  = new DiscussThemes();
        $ajax   = DiscussHelper::getHelper( 'Ajax' );
        $model  = DiscussHelper::getModel( 'Posts' );
        $config = DiscussHelper::getConfig();

        $id     = JRequest::getInt( 'id', 0 );

        $sort   = JRequest::getString( 'sort', DiscussHelper::getDefaultRepliesSorting() );

        $start  = JRequest::getInt( 'start', 0 );

        $total  = $model->getTotalReplies( $id );

        if( $start >= $total )
        {
            return $ajax->reject();
        }

        $replies = $model->getReplies( $id, $sort, $start, $config->get( 'layout_replies_list_limit' ) );

        if( empty( $replies ) )
        {
            return $ajax->reject();
        }

        $count = count( $replies );

        $nextCycle = ( $start + $count ) < $total;

        // Load the category
        $post       = DiscussHelper::getTable( 'Posts' );
        $post->load( $id );
        $category   = DiscussHelper::getTable( 'Category' );
        $category->load( (int) $post->category_id );

        $replies = DiscussHelper::formatReplies( $replies, $category );

        $html = '';

        foreach( $replies as $reply )
        {
            $theme->set('category', $category);
            $theme->set('question', $post);
            $theme->set('post', $reply);
            $html .= '<li>' . $theme->fetch( 'post.reply.item.php' ) . '</li>';
        }

        return $ajax->resolve( $html, $nextCycle );
    }

    /**
     * Triggered when edit reply button is clicked so that we can return the correct output
     * to the caller.
     *
     * @since   1.0
     * @access  public
     * @param   null
     * @return
     */
    public function editReply()
    {
        // Load up our own ajax library.
        $ajax   = DiscussHelper::getHelper( 'Ajax' );
        $id     = JRequest::getInt( 'id', 0 );
        $config = DiscussHelper::getConfig();

        if( $id === 0 )
        {
            $ajax->reject();
            return $ajax->send();
        }

        // Load the post table out.
        $post       = DiscussHelper::getTable( 'Post' );
        $state      = $post->load($id);

        $post->content_raw = $post->content;

        // TODO: Determine if this person can edit this post
        $composer               = new DiscussComposer( 'editing' , $post );
        $composer->renderMode   = "explicit";

        $ajax->resolve($composer->id, $composer->getComposer());

        return $ajax->send();
    }


    public function checkEmpty( $post , $ajax )
    {
        // do checking here!
        if( empty( $post[ 'content' ] ) )
        {
            $ajax->reject('error', JText::_('COM_EASYDISCUSS_ERROR_REPLY_EMPTY'));
            $ajax->send();

            exit;
        }
    }

    /**
     * Determines if the captcha is correct
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function checkCaptcha( $post )
    {
        $config     = DiscussHelper::getConfig();
        $my         = JFactory::getUser();

        $ajax   = DiscussHelper::getHelper( 'Ajax' );


        // Get recaptcha configuration
        $recaptcha  = $config->get( 'antispam_recaptcha');
        $public     = $config->get( 'antispam_recaptcha_public');
        $private    = $config->get( 'antispam_recaptcha_private');

        require_once DISCUSS_CLASSES . '/recaptcha.php';

        if( DiscussRecaptcha::isRequired() )
        {
            $obj = DiscussRecaptcha::recaptcha_check_answer( $private , $_SERVER['REMOTE_ADDR'] , $post['recaptcha_challenge_field'] , $post['recaptcha_response_field'] );

            if(!$obj->is_valid)
            {
                $ajax->reloadCaptcha();
                $ajax->reject('error', JText::_('COM_EASYDISCUSS_POST_INVALID_RECAPTCHA_RESPONSE'));
                $ajax->send();

                return false;
            }
        }
        else if ( $config->get( 'antispam_easydiscuss_captcha' ) )
        {
            $runCaptcha = DiscussHelper::getHelper( 'Captcha' )->showCaptcha();

            if( $runCaptcha )
            {
                $response = JRequest::getVar( 'captcha-response' );
                $captchaId = JRequest::getInt( 'captcha-id' );

                $discussCaptcha = new stdClass();
                $discussCaptcha->captchaResponse = $response;
                $discussCaptcha->captchaId = $captchaId;

                $state = DiscussHelper::getHelper( 'Captcha' )->verify( $discussCaptcha );

                if( !$state )
                {
                    $ajax->reject('error', JText::_('COM_EASYDISCUSS_INVALID_CAPTCHA'));
                    $ajax->send();

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Some desc
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function processPolls( $post )
    {
        $config         = DiscussHelper::getConfig();

        // Process poll items
        $includePolls   = JRequest::getBool( 'pollitems' , false );

        // Process poll items here.
        if( $includePolls && $config->get( 'main_polls') )
        {
            $pollItems      = JRequest::getVar( 'pollitems' );
            $pollItemsOri   = JRequest::getVar( 'pollitemsOri' );

            // Delete polls if necessary since this post doesn't contain any polls.
            //if( !$isNew && !$includePolls )
            if( count( $pollItems ) == 1 && empty( $pollItems[0] ) && !$isNew )
            {
                $post->removePoll();
            }

            // Check if the multiple polls checkbox is it checked?
            $multiplePolls  = JRequest::getVar( 'multiplePolls' , '0' );

            if( $pollItems )
            {
                // As long as we need to create the poll answers, we need to create the main question.
                $pollTitle  = JRequest::getVar( 'poll_question' , '' );

                // Since poll question are entirely optional.
                $pollQuestion   = DiscussHelper::getTable( 'PollQuestion' );
                $pollQuestion->loadByPost( $post->id );

                $pollQuestion->post_id  = $post->id;
                $pollQuestion->title    = $pollTitle;
                $pollQuestion->multiple = $config->get( 'main_polls_multiple' ) ? $multiplePolls : false;
                $pollQuestion->store();

                if( !$isNew )
                {
                    // Try to detect which poll items needs to be removed.
                    $remove = JRequest::getVar( 'pollsremove' );

                    if( !empty( $remove ) )
                    {
                        $remove = explode( ',' , $remove );

                        foreach( $remove as $id )
                        {
                            $id     = (int) $id;
                            $poll   = DiscussHelper::getTable( 'Poll' );
                            $poll->load( $id );
                            $poll->delete();
                        }
                    }
                }

                for( $i = 0; $i < count($pollItems); $i++ )
                {
                    $item    = $pollItems[$i];
                    $itemOri = isset( $pollItemsOri[$i] ) ? $pollItemsOri[$i] : '';

                    $value      = (string) $item;
                    $valueOri   = (string) $itemOri;

                    if( trim( $value ) == '' )
                        continue;

                    $poll   = DiscussHelper::getTable( 'Poll' );

                    if( empty( $valueOri ) && !empty( $value ) )
                    {
                        // this is a new item.
                        $poll->set( 'value'         , $value );
                        $poll->set( 'post_id'       , $post->get( 'id' )  );
                        $poll->store();
                    }
                    else if( !empty( $valueOri ) && !empty( $value )  )
                    {
                        // update existing value.
                        $poll->loadByValue( $valueOri , $post->get( 'id' ) );
                        $poll->set( 'value'         , $value );
                        $poll->store();
                    }

                }

            }
        }
    }

    /**
     * Triggers when an edited reply is saved.
     *
     * @since   3.0
     * @param   null
     * @return  null
     */
    public function saveReply()
    {
        // Load ajax library
        $ajax       = DiscussHelper::getHelper( 'Ajax' );
        $config     = DiscussHelper::getConfig();

        // Get the posted data
        $data   = JRequest::get( 'post' );

        // Prepare the output data
        $output         = array();
        $output['id']   = $data[ 'post_id' ];
        $acl            = DiscussHelper::getHelper( 'ACL' );
        $my             = JFactory::getUser();

        // Check for empty content
        $this->checkEmpty( $data , $ajax );

        // Rebind the post data because it may contain HTML codes
        $data[ 'content' ]      = JRequest::getVar( 'content', '', 'post', 'none' , JREQUEST_ALLOWRAW );
        $data[ 'content_type' ] = DiscussHelper::getEditorType( 'reply' );

        // Load up the post table
        $post       = DiscussHelper::getTable( 'Post' );
        $post->load( $data[ 'post_id' ] );

        // Bind the post table with the data
        $post->bind( $data );

        // Check if the post data is valid
        if( !$post->id || !$data[ 'post_id' ] )
        {
            $ajax->reject( 'error' , JText::_( 'COM_EASYDISCUSS_SYSTEM_INVALID_ID' ) );
            return $ajax->send();
        }

        // Only allow users with proper access
        $isModerator = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        // Do not allow unauthorized access
        if( !DiscussHelper::isSiteAdmin() && $post->user_id != $my->id && !$acl->allowed( 'edit_reply' , 0 ) && !$isModerator )
        {
            $ajax->reject('error', JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS'));
            $ajax->send();
        }

        // Get the new content from the post data
        $post->content      = $data[ 'content' ];

        // Validate captcha
        $this->checkCaptcha( $data );

        // @rule: Bind parameters
        if( $config->get( 'reply_field_references' ) )
        {
            $post->bindParams($data);
        }

        // Bind file attachments
        if ($acl->allowed('add_attachment', '0')) {
            $post->bindAttachments();
        }

        // Determines if this is a new post.
        $isNew  = false;

        // @trigger: onBeforeSave
        DiscussEventsHelper::importPlugin( 'content' );
        DiscussEventsHelper::onContentBeforeSave( 'post' , $post , $isNew );

        // Try to store the post now
        if( !$post->store() )
        {
            $ajax->reject('error', JText::_('COM_EASYDISCUSS_ERROR') );
            $ajax->send();
        }

        // Process polls
        $this->processPolls( $post );

        // Process custom fields
        $this->saveCustomFieldsValue( $post->id );

        // @trigger: onAfterSave
        DiscussEventsHelper::onContentAfterSave( 'post', $post, $isNew);

        // Filter for badwords
        $post->title    = DiscussHelper::wordFilter( $post->title );
        $post->content  = DiscussHelper::wordFilter( $post->content );

        // Determines if the user is allowed to delete this post
        $canDelete  = false;

        if( DiscussHelper::isSiteAdmin() || $acl->allowed('delete_reply', '0') || $post->user_id == $my->id )
        {
            $canDelete  = true;
        }

        // URL References
        $post->references   = $post->getReferences();

        // Get the voted state
        $voteModel          = DiscussHelper::getModel( 'Votes' );
        $post->voted        = $voteModel->hasVoted( $post->id );

        // Get total votes for this post
        $post->totalVote    = $post->sum_totalvote;

        // Load profile info
        $creator    = DiscussHelper::getTable( 'Profile' );
        $creator->load( $post->user_id );

        // Assign creator
        $post->user     = $creator;

        // Format the content.
        $tmp                = $post->content;
        $post->content_raw  = $post->content;
        $post->content      = DiscussHelper::formatContent( $post );

        // Once the formatting is done, we need to escape the raw content
        $post->content_raw  = DiscussHelper::getHelper( 'String' )->escape( $tmp );

        // Store the default values
        //default value
        $post->isVoted          = 0;
        $post->total_vote_cnt   = 0;
        $post->likesAuthor      = '';
        $post->minimize         = 0;

        // Trigger reply
        $post->triggerReply();

        // Load up parent's post
        $question       = DiscussHelper::getTable( 'Post' );
        $question->load( $post->parent_id );

        $recaptcha          = '';
        $enableRecaptcha    = $config->get('antispam_recaptcha');
        $publicKey          = $config->get('antispam_recaptcha_public');
        $skipRecaptcha      = $config->get('antispam_skip_recaptcha');

        $model      = DiscussHelper::getModel( 'Posts' );
        $postCount  = count( $model->getPostsBy( 'user' , $my->id ) );

        if( $enableRecaptcha && !empty( $publicKey ) && $postCount < $skipRecaptcha )
        {
            require_once DISCUSS_CLASSES . '/recaptcha.php';
            $recaptcha  = getRecaptchaData( $publicKey , $config->get('antispam_recaptcha_theme') , $config->get('antispam_recaptcha_lang') , null, $config->get('antispam_recaptcha_ssl'), 'edit-reply-recaptcha' .  $post->id);
        }

        // Get the post access object here.
        $category       = DiscussHelper::getTable( 'Category' );
        $category->load( $post->category_id );

        $access         = $post->getAccess( $category );
        $post->access   = $access;

        // Get comments for the post
        $commentLimit       = $config->get( 'main_comment_pagination' ) ? $config->get( 'main_comment_pagination_count' ) : null;
        $comments           = $post->getComments( $commentLimit );
        $post->comments     = DiscussHelper::formatComments( $comments );


        $theme  = new DiscussThemes();

        $theme->set('question' , $question);
        $theme->set('post'     , $post);
        $theme->set('category' , $category);

        // Get theme file output
        $contents   = $theme->fetch( 'post.reply.item.php' );

        $ajax->resolve( $contents );
        return $ajax->send();
    }

    public function saveCustomFieldsValue( $id = null )
    {
        if( !empty($id) )
        {
            //Clear off previous records before storing
            $ruleModel = DiscussHelper::getModel( 'CustomFields' );
            $ruleModel->deleteCustomFieldsValue( $id, 'update' );

            $post = DiscussHelper::getTable( 'Post' );
            $post->load( $id );

            // Process custom fields.
            $fieldIds = JRequest::getVar( 'customFields' );

            if( !empty($fieldIds) )
            {
                foreach( $fieldIds as $fieldId )
                {
                    $fields = JRequest::getVar( 'customFieldValue_'.$fieldId );

                    if( !empty($fields) )
                    {
                        // Cater for custom fields select list
                        // To detect if there is no value selected for the select list custom fields
                        if( in_array( 'defaultList', $fields ) )
                        {
                            $tempKey = array_search( 'defaultList', $fields );
                            $fields[ $tempKey ] = '';
                        }
                    }

                    $post->bindCustomFields( $fields, $fieldId );
                }
            }
        }
    }

    /**
     * Displays the branch confirmation form
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function branchForm( $id )
    {
        $ajax   = new Disjax();

        $model      = DiscussHelper::getModel( 'Posts' );

        $posts      = $model->getDiscussions( array( 'limit' => DISCUSS_NO_LIMIT , 'exclude' => array( $id ) ) );

        $theme  = new DiscussThemes();
        $theme->set( 'id' , $id );

        $content            = $theme->fetch( 'ajax.post.branch.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;
        $options->title     = JText::_( 'COM_EASYDISCUSS_BRANCH_POST_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_BRANCH' );
        $button->form       = '#frmBranch';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    /**
     * Merges the current discussion into an existing discussion
     *
     * @since   1.0
     * @access  public
     * @param   string
     * @return
     */
    public function mergeForm( $id )
    {
        $ajax   = new Disjax();

        $model      = DiscussHelper::getModel( 'Posts' );

        $posts      = $model->getDiscussions( array( 'limit' => DISCUSS_NO_LIMIT , 'exclude' => array( $id ) ) );

        $theme  = new DiscussThemes();
        $theme->set( 'posts'    , $posts );
        $theme->set( 'current'  , $id );

        $content            = $theme->fetch( 'ajax.post.merge.php' , array('dialog'=> true ) );

        $options            = new stdClass();
        $options->content   = $content;
        $options->title     = JText::_( 'COM_EASYDISCUSS_MERGE_POST_TITLE' );

        $buttons            = array();

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_CLOSE' );
        $button->action     = 'disjax.closedlg();';
        $buttons[]          = $button;

        $button             = new stdClass();
        $button->title      = JText::_( 'COM_EASYDISCUSS_BUTTON_MERGE' );
        $button->form       = '#frmMergePost';
        $button->className  = 'btn-primary';
        $buttons[]          = $button;

        $options->buttons   = $buttons;

        $ajax->dialog( $options );
        $ajax->send();
    }

    public function ajaxOnHoldPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post           = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $acl        = DiscussHelper::getHelper( 'ACL' );
        $isModerator    = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin && !$acl->allowed('mark_on_hold', '0') )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        // Turn on the on-hold status,
        // DISCUSS_POST_STATUS_ON_HOLD = 1
        $post->post_status = DISCUSS_POST_STATUS_ON_HOLD;

        // When it is on hold, other status must turn off
        $post->isresolve = 0;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_ON_HOLD_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => 'onHold',
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_ON_HOLD') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );

        // Remove other status
        $ajax->script( 'EasyDiscuss.$( ".discuss-item" ).removeClass( "is-resolved" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-on-hold" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-accept" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-working-on" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-reject" );' );

        // Add status
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).addClass("label-post_status-on-hold");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).html("' . JText::_( "COM_EASYDISCUSS_POST_STATUS_ON_HOLD" ) . '");' );
        $ajax->send();
        return;
    }

    public function ajaxAcceptedPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post       = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine         = DiscussHelper::isMine($post->user_id);
        $isAdmin        = DiscussHelper::isSiteAdmin();
        $acl            = DiscussHelper::getHelper( 'ACL' );
        $isModerator    = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin && !$acl->allowed('mark_accepted', '0') )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        // Turn on the accepted status,
        // DISCUSS_POST_STATUS_ACCEPTED = 2
        $post->post_status = DISCUSS_POST_STATUS_ACCEPTED;

        // Other status must turn off
        $post->isresolve = 0;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();

        if( $post->get( 'user_id') != $my->id )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_ACCEPTED_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => 'accepted',
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        // Remove other status
        $ajax->script( 'EasyDiscuss.$( ".discuss-item" ).removeClass( "is-resolved" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-on-hold" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-accept" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-working-on" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-reject" );' );

        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_ACCEPTED') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );

        // Add status
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).addClass("label-post_status-accept");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).html("' . JText::_( "COM_EASYDISCUSS_POST_STATUS_ACCEPTED" ) . '");' );
        $ajax->send();
        return;
    }

    public function ajaxWorkingOnPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post       = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $acl        = DiscussHelper::getHelper( 'ACL' );
        $isModerator    = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin && !$acl->allowed('mark_working_on', '0') )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).html( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        // Turn on the accepted status,
        // DISCUSS_POST_STATUS_WORKING_ON = 3
        $post->post_status = DISCUSS_POST_STATUS_WORKING_ON;

        // Other status must turn off
        $post->isresolve = 0;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id  )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_WORKING_ON_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => 'workingOn',
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        // Remove other status
        $ajax->script( 'EasyDiscuss.$( ".discuss-item" ).removeClass( "is-resolved" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-on-hold" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-accept" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-working-on" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-reject" );' );


        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_WORKING_ON') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );

        // Add status
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).addClass("label-post_status-working-on");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).html("' . JText::_( "COM_EASYDISCUSS_POST_STATUS_WORKING_ON" ) . '");' );
        $ajax->send();
        return;
    }

    public function ajaxRejectPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post       = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $acl        = DiscussHelper::getHelper( 'ACL' );
        $isModerator    = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin && !$acl->allowed('mark_reject', '0') )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        // Turn on the accepted status,
        // DISCUSS_POST_STATUS_REJECT = 4
        $post->post_status = DISCUSS_POST_STATUS_REJECT;

        // Other status must turn off
        $post->isresolve = 0;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_REJECT_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => 'reject',
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        // Remove other status
        $ajax->script( 'EasyDiscuss.$( ".discuss-item" ).removeClass( "is-resolved" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-on-hold" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-accept" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-working-on" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-reject" );' );

        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_REJECT') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );

        // Add status
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).addClass("label-post_status-reject");' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).html("' . JText::_( "COM_EASYDISCUSS_POST_STATUS_REJECT" ) . '");' );
        $ajax->send();
        return;
    }

    public function ajaxNoStatusPost( $id = null )
    {
        $ajax   = new Disjax();
        $config = DiscussHelper::getConfig();

        if( !$id )
        {
            $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID') );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        $post           = DiscussHelper::getTable( 'Post' );
        $post->load( $id );

        $isMine     = DiscussHelper::isMine($post->user_id);
        $isAdmin    = DiscussHelper::isSiteAdmin();
        $acl        = DiscussHelper::getHelper( 'ACL' );
        $isModerator    = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );

        if ( !$isMine && !$isAdmin && !$acl->allowed('mark_no_status', '0') )
        {
            if( !$isModerator )
            {
                $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
                $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
                $ajax->send();
                return;
            }
        }

        // Turn on the on-hold status,
        // DISCUSS_POST_STATUS_OFF = 0
        $post->post_status = DISCUSS_POST_STATUS_OFF;

        if ( !$post->store() )
        {
            $ajax->assign( 'dc_main_notifications' , $post->getError() );
            $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-error" );' );
            $ajax->send();
            return;
        }

        // @rule: Add notifications for the thread starter
        $my     = JFactory::getUser();
        if( $post->get( 'user_id') != $my->id )
        {
            $notification   = DiscussHelper::getTable( 'Notifications' );
            $notification->bind( array(
                    'title' => JText::sprintf( 'COM_EASYDISCUSS_NO_STATUS_DISCUSSION_NOTIFICATION_TITLE' , $post->title ),
                    'cid'   => $post->get( 'id' ),
                    'type'  => 'unhold',
                    'target'    => $post->get( 'user_id' ),
                    'author'    => $my->id,
                    'permalink' => 'index.php?option=com_easydiscuss&view=post&id=' . $post->get( 'id' )
                ) );
            $notification->store();
        }

        // Remove other status
        $ajax->script( 'EasyDiscuss.$( ".discuss-item" ).removeClass( "is-resolved" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-on-hold" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-accept" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-working-on" );' );
        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).removeClass( "label-post_status-reject" );' );

        $ajax->assign( 'dc_main_notifications' , JText::_('COM_EASYDISCUSS_POST_NO_STATUS') );
        $ajax->script( 'EasyDiscuss.$( "#dc_main_notifications" ).addClass( "alert alert-success" );' );

        $ajax->script( 'EasyDiscuss.$( ".postStatus" ).html("");' );
        $ajax->send();
        return;
    }
}
