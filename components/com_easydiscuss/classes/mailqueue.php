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

require_once DISCUSS_HELPERS . '/router.php';

class DMailQueue
{
	function sendOnPageLoad()
	{
		$db 	= DiscussHelper::getDBO();
		$config	= DiscussHelper::getConfig();
		$max	= (int) $config->get('main_mailqueuenumber');

		// Delete existing mails that has already been sent.
		$query		= 'DELETE FROM ' . $db->nameQuote( '#__discuss_mailq' ) . ' WHERE '
					. $db->nameQuote( 'status' ) . '=' . $db->Quote( 1 )
					. ' AND DATEDIFF(NOW(), `created`) >= 30';

		$db->setQuery( $query );
		$db->Query();

		$query  = 'SELECT `id` FROM `#__discuss_mailq` WHERE `status` = 0';
		$query  .= ' ORDER BY `created` ASC';
		$query  .= ' LIMIT ' . $max;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if(! empty($result))
		{
			foreach($result as $mail)
			{
				$mailq	= DiscussHelper::getTable( 'MailQueue' );
				$mailq->load($mail->id);

				// update the status to 1 == proccessed
 				$mailq->status  = 1;
 				if( $mailq->store() )
				{
					if( DiscussHelper::getJoomlaVersion() > '1.6' )
					{
						$mail   	= JFactory::getMailer();
						$result 	= $mail->sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body , $mailq->ashtml );

						// This code is to record all the activities from crons.
						// Need to create a table for this
						// $date = JFactory::getDate();

						// $data 				= new stdClass();
						// $data->id 			= null;
						// $data->recipient 	= $mailq->recipient;
						// $data->subject 		= $mailq->subject;
						// $data->body 		= $mailq->body;
						// $data->status 		= $mailq->status;
						// $data->created 		= $date->toMySQL();

						// $db->insertObject( '#__discuss_cron_logs', $data, id );

	 				}
					else
					{
						JUtility::sendMail($mailq->mailfrom, $mailq->fromname, $mailq->recipient, $mailq->subject, $mailq->body , $mailq->ashtml );
					}

				}

				//end foreach
			}
		}

	}

	public function parseEmails()
	{
		require_once DISCUSS_CLASSES . '/mailbox.php';
		$config = DiscussHelper::getConfig();


		// Default email parser
		$mailer	= new DiscussMailer();
		$state	= $mailer->connect( $config->get( 'main_email_parser_username' ), $config->get( 'main_email_parser_password' ) );

		if( $state )
		{
			self::processEmails( $mailer );
		}

		// Category email parser
		$model = DiscussHelper::getModel('Categories', true);
		$cats = $model->getAllCategories();

		if( is_array($cats) )
		{
			foreach( $cats as $cat )
			{
				$category = DiscussHelper::getTable( 'Category' );
				$category->load( $cat->id );

				$enable = explode( ',' , $category->getParam( 'cat_email_parser_switch') );

				if( $enable[0] )
				{
					$catMail = explode( ',' , $category->getParam( 'cat_email_parser') );
					$catPass = explode( ',' , $category->getParam( 'cat_email_parser_password') );

					$mailer	= new DiscussMailer();
					$state	= $mailer->connect( $catMail[0], $catPass[0] );

					if( $state )
					{
						self::processEmails( $mailer, $category );
					}
				}

			}
		}

		return true;
	}


	/*
	 * Connect from parseEmails
	 */
	private function processEmails( $mailer = '', $category = '' )
	{
		// @task: Only search for messages that are new.
		$unread	= $mailer->searchMessages( 'UNSEEN' );

		// If there is no unread emails, just skip this altogether
		if (!$unread) {
			echo JText::_( 'COM_EASYDISCUSS_NO_EMAILS_TO_PARSE' );
			return false;
		}

		$config = DiscussHelper::getConfig();
		$acl = DiscussHelper::getHelper( 'ACL' );
		$filter = JFilterInput::getInstance();
		$total = 0;

		$replyBreaker = $config->get('mail_reply_breaker');

		foreach ($unread as $sequence) {

			// Get the message info
			$info = $mailer->getMessageInfo($sequence);
			$from = $info->from;
			$senderName = $from[0]->personal;

			// Get the subject of the email and clean it to avoid any unclose html tags
			$subject = $filter->clean($info->subject);

			// @rule: Detect if this is actually a reply.
			preg_match('/\[\#(.*)\]/is', $subject, $matches);

			$isReply = !empty( $matches );
			$message = new DiscussMailerMessage( $mailer->stream , $sequence );

			// Load up the post object
			$post = DiscussHelper::getTable('Post');

			// Get the html output
			$html = $message->getHTML();

		    // Default allowed html codes
		    $allowed = '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>,<b>,<i>,<u>';

			// Remove disallowed tags
			$html = strip_tags($html, $allowed);

			// Remove img tags because we do not support email embeded images
			$pattern = array();
			$pattern[] = '/<img.*?src=["|\'](.*?)["|\'].*?\>/ims';
			$html = preg_replace( $pattern, array( '' ), $html );

			$editor 	= !$isReply ? $config->get( 'layout_editor' ) : $config->get( 'layout_reply_editor' );
			$contentType = $editor == 'bbcode' ? 'bbcode' : 'html';

			if ($editor == 'bbcode') {

				// remove &nbsp; from content if there is any
				$html = JString::str_ireplace('&nbsp;', ' ', $html);

				// Switch html back to bbcode
				$html 	= EasyDiscussParser::html2bbcode( $html );

				// Update the quote messages
				$html 	= EasyDiscussParser::quoteBbcode( $html );

				//since the editor is a bbcode, we should not allow any html tags.
				$html = strip_tags($html);
			}

			// Insert default subject if emails do not contain title
			if (empty($subject)) {
				$subject = JText::_('COM_EASYDISCUSS_EMAIL_NO_SUBJECT');
			}

			$post->set( 'content' 	, $html );
			$post->set( 'content_type', $contentType);
			$post->set( 'title'		, $subject );
			$post->set('alias', DiscussHelper::getAlias($title, 'post'));
			$post->set( 'published'	, DISCUSS_ID_PUBLISHED );
			$post->set( 'created'	, DiscussHelper::getDate()->toMySQL() );
			$post->set( 'replied'	, DiscussHelper::getDate()->toMySQL() );
			$post->set( 'modified'	, DiscussHelper::getDate()->toMySQL() );


			// If this is a reply, and the site isn't configured to parse replies, skip this
			if ($isReply && !$config->get('main_email_parser_replies')) {
				continue;
			}

			//add this for category email parser
			if (!empty($category)) {

				$post->category_id=$category->id;

			} else {
				// By default, set the category to the one pre-configured at the back end.
				$post->category_id = $config->get('main_email_parser_category');
			}

			if ($isReply) {
				$parentId	= (int) $matches[1];
				$post->set( 'parent_id' , $parentId );

				// Trim content, get text before the defined line
				if( $replyBreaker ) {
					if( $pos = JString::strpos($post->content, $replyBreaker) ) {
						$post->content = JString::substr($post->content, 0, $pos);
					}
				}

				// Since this is a reply, we need to determine the correct category for it based on the parent discussion.
				$parent = DiscussHelper::getTable('Post');
				$parent->load($parentId);

				$post->category_id = $parent->category_id;
			}

			// @rule: Map the sender's email with the user in Joomla?
			$replyToEmail	= $info->fromemail;

			// Lookup for the user based on their email address.
			$user = DiscussHelper::getUserByEmail($replyToEmail);

			if ($user instanceof JUser) {

				$post->user_id = $user->id;
				$post->user_type = DISCUSS_POSTER_MEMBER;
			} else {
				// Guest posts
				$post->user_type = DISCUSS_POSTER_GUEST;
				$post->poster_name = $senderName;
				$post->poster_email = $replyToEmail;
			}

			// check if guest can post question or not. if not skip the processing.
			if ($post->get( 'user_type') == DISCUSS_POSTER_GUEST) {

				$acl = DiscussHelper::getHelper( 'ACL', '0' );

				if (!$acl->allowed('add_question')) {
					continue;
				}
			}

			// If the system is configured to moderate all emails, then we should update the state accordingly
			if ($config->get('main_email_parser_moderation')) {
				$post->set('published', DISCUSS_ID_PENDING);
			}

			// @rule: Process the post
			$post->store();

			// prepare email content and information.
			$profile = DiscussHelper::getTable('Profile');

			if (isset($user->id)) {
				$profile->load($user->id);
			}

			// For use within the emails.
			$emailData					= array();
			$emailData['postTitle']		= $post->title;
			$emailData['postAuthor']	= $profile->id ? $profile->getName() : $post->poster_name;
			$emailData['postAuthorAvatar' ] = $profile->getAvatar();
			$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $post->id, false, true);

			$emailContent 	= $post->content;


			if( $post->content_type != 'html' )
			{
				// the content is bbcode. we need to parse it.
				$emailContent	= EasyDiscussParser::bbcode( $emailContent );
				$emailContent	= EasyDiscussParser::removeBrTag( $emailContent );
			}

			// If post is html type we need to strip off html codes.
			if( $post->content_type == 'html' )
			{
				$emailContent 			= strip_tags( $post->content );
			}

			// Notify moderators when there's a new pending post
			if ($post->published == DISCUSS_ID_PENDING) {

				// Generate hashkeys to map this current request
				$hashkey = DiscussHelper::getTable('HashKeys');
				$hashkey->uid = $post->id;
				$hashkey->type = DISCUSS_QUESTION_TYPE;
				$hashkey->store();

				// Prepare the email template.
				$approveURL = DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
				$rejectURL = DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
				$emailData[ 'moderation' ]	= '<div style="display:inline-block;width:100%;padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:\'Lucida Grande\',Tahoma,Arial;font-size:12px;text-align:left">';
				$emailData[ 'moderation' ] .= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_POST' ) . '</a>';
				$emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . ' <a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';
				$emailData[ 'moderation' ] .= '</div>';
				$emailData['postContent']	= $post->content;
				$emailData['emailTemplate']	= 'email.subscription.site.moderate.php';
				$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_MODERATE', $post->id , $post->title);

				// Notify the moderators now
				DiscussHelper::getHelper('Mailer')->notifyAdministrators($emailData, array($replyToEmail), $config->get('notify_admin'), $config->get('notify_moderator'));

			} elseif($post->published == DISCUSS_ID_PUBLISHED) {

					//$emailData['attachments']	= $attachments;
					$emailData['postContent']	= $post->content;
					$emailData['emailTemplate']	= 'email.subscription.site.new.php';
					$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_QUESTION_ASKED', $post->id , $post->title);

					DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin' ), $config->get( 'notify_moderator' ) );
				}

			$attachments	= array();
			$attachments	= $message->getAttachment();

			// process attached images
			if (!empty($attachments))
			{
				$config				= DiscussHelper::getConfig();
				$main_image_path	= rtrim('images/easydiscuss_images/', '/');

				$file 	= JPATH_ROOT . DIRECTORY_SEPARATOR . $main_image_path;

				if( !JFolder::exists($file) )
				{
					JFolder::create( $file );
				}

				$senderMail	= $info->fromemail;
				$user 		= DiscussHelper::getUserByEmail( $senderMail );
				$userid 	= $user->get( 'id' );

				$rel_upload_path	= $main_image_path . '/' . $userid;

				$userUploadPath		= JPATH_ROOT . DIRECTORY_SEPARATOR . $main_image_path . DIRECTORY_SEPARATOR . $userid;
				$userUploadPath		= JPath::clean($userUploadPath);

				$dir				= $userUploadPath . DIRECTORY_SEPARATOR;
				$tmp_dir			= JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

				$uri				= JURI::base().$main_image_path.'/'.$userid.'/';

				if(! JFolder::exists($dir))
				{
					JFolder::create($dir);
				}

				foreach ($attachments as $attachment)
				{
					// clean up file name
					if(strpos($attachment['name'], '/') !== FALSE)
					{
						$attachment['name'] = substr($attachment['name'], strrpos($attachment['name'],'/')+1 );
					}
					elseif(strpos($attachment['name'], '\\' !== FALSE))
					{
						$attachment['name'] = substr($attachment['name'], strrpos($attachment['name'],'\\')+1 );
					}

					// @task: check if the attachment has file extension. ( assuming is images )
					$imgExts        = array( 'jpg', 'png', 'gif', 'JPG', 'PNG', 'GIF', 'jpeg', 'JPEG' );
					$imageSegment   = explode('.', $attachment['name']);

					if( ! in_array( $imageSegment[ count( $imageSegment ) - 1 ], $imgExts ) )
					{
						$attachment['name'] = $attachment['name'] . '.jpg';
					}

					// @task: Store the file into a temporary location first.
					$attachment['tmp_name']	= $tmp_dir . $attachment['name'];


					JFile::write( $attachment['tmp_name'], $attachment['data']);

					$atmTitle = $attachment['name'];
					$atmURL	= $uri.$attachment['name'];

					// Bind file attachments
					jimport( 'joomla.filesystem.file' );
					jimport( 'joomla.filesystem.folder' );
					jimport( 'joomla.utilities.utility' );

					// @rule: Create default media path
					$path   = DISCUSS_MEDIA . '/' . trim( $config->get( 'attachment_path' ) , DIRECTORY_SEPARATOR );

					if( !JFolder::exists( $path ) )
					{
						JFolder::create( $path );
						JFile::copy( DISCUSS_ROOT . '/index.html' , $path . '/index.html' );
					}

					$maxSize	= (double) $config->get( 'attachment_maxsize' ) * 1024 * 1024;
					$extension  = JFile::getExt( $attachment['name'] );

					// Skip empty data's.
					if( !$extension )
					{
						continue;
					}

					$allowed	= explode( ',' , $config->get( 'main_attachment_extension' ) );

					// @rule: Check for allowed extensions
					if( !isset( $extension ) || !in_array( strtolower($extension) , $allowed ) )
					{
						echo 'Invalid extension.';
					}
					else
					{
						$size   	= $attachment['size'];
						$name		= $attachment['name'];
						$mime		= $attachment['mime'];
						$tmpName 	= $attachment['tmp_name'];

						// Check the mime contains the attachment type, if not we insert our own
						$imgExts        = array( 'jpg', 'png', 'gif', 'JPG', 'PNG', 'GIF', 'jpeg', 'JPEG' );
						if( in_array( $mime, $imgExts ) )
						{
							$mime = 'image/' . $mime;
						}
						else
						{
							$mime = 'application/' . $mime;
						}

						// @rule: File size should not exceed maximum allowed size
						if( !empty( $size ) && $size < $maxSize )
						{
							$hash			= DiscussHelper::getHash( $name . DiscussHelper::getDate()->toMySQL() );
							$attachment     = DiscussHelper::getTable( 'Attachments' );

							$attachment->set( 'path'		, $hash );
							$attachment->set( 'title'		, $name );
							$attachment->set( 'uid' 		, $post->id );
							$attachment->set( 'type'		, ( $isReply ? 'replies' : 'questions' ) );
							$attachment->set( 'created'		, DiscussHelper::getDate()->toMySQL() );
							$attachment->set( 'published'	, true );
							$attachment->set( 'mime'		, $mime );
							$attachment->set( 'size'		, $size );


							if( !JFile::copy( $tmpName , $path . '/' . $hash ) )
							{
								echo 'Copy failed from tmp to attachment folder';
							}

							$attachment->store();

							// Create a thumbnail if attachment is an image
							if( DiscussHelper::getHelper( 'Image' )->isImage($name) )
							{

								require_once DISCUSS_CLASSES . '/simpleimage.php';
								$image	= new SimpleImage;

								$image->load($tmpName);
								$image->resizeToFill( 160 , 120 );

								$image->save($path . '/' . $hash . '_thumb', $image->image_type);

							}

							// @task: Once the attachment is processed, delete the temporary file.
							JFile::delete( $tmpName );
						}
						else
						{

							echo 'Invalid extension ' . $name;
						}
					}

				}
			}

			// Send notification email to the subscribers in the thread.
			if( $isReply && $post->get( 'published') == DISCUSS_ID_PUBLISHED )
			{
				self::replyNotifyUsers( $post , $user , $senderName );
			}

			// @task: Increment the count.
			$total	+= 1;

			// @rule: Only send autoresponders when it's a new post.
			if( !$isReply && $config->get( 'main_email_parser_receipt' ) && $post->get( 'published' ) == DISCUSS_ID_PUBLISHED )
			{
				$sendAsHTML	= (bool) $config->get( 'notify_html_format' );

				$theme		= new DiscussThemes();
				$postId		= $post->get( 'id' );

				if( $isReply )
				{
					$postId	= $parentId;
				}

				$url		= DiscussRouter::getRoutedURL( 'index.php?option=com_easydiscuss&view=post&id=' . $postId , false , true );


				$emailData 				= array();
				$emailData['postLink']	= $url;

				if( $post->get( 'user_type') == DISCUSS_POSTER_GUEST )
				{
					$emailData[ 'postAuthor' ]	= $senderName;
				}
				else
				{
					$profile 	= DiscussHelper::getTable( 'Profile' );
					$profile->load( $user->id );

					$emailData['postAuthor' ]	= $profile->getName();
				}

				require_once DISCUSS_CLASSES . '/notification.php';
				$notification	= new DNotification();
				$output 		= $notification->getEmailTemplateContent( 'email.accepted.responder.php' , $emailData );

				$app		= JFactory::getApplication();

				if( !$sendAsHTML )
				{
					$output	= strip_tags( $output );
				}

				// @rule: Send confirmation message.
				JUtility::sendMail( $app->getCfg( 'mailfrom' ) , $app->getCfg( 'fromname' ) , $replyToEmail , '[#' . $post->id . ']: ' . $subject , $output , $sendAsHTML );
			}

			if( !$isReply && $post->get( 'published' ) == DISCUSS_ID_PUBLISHED )
			{
				// Send email to subscribers about new post has created
				Discusshelper::sendNotification( $post , $post->parent_id, true, $post->user_id, DISCUSS_ID_PENDING );
			}

			echo JText::sprintf( 'COM_EASYDISCUSS_EMAIL_PARSED' , $total );
		}

	}

	public function replyNotifyUsers( $reply , $user , $senderName )
	{
		//send notification to all comment's subscribers that want to receive notification immediately
		$notify		= DiscussHelper::getNotification();
		$emailData	= array();
		$config		= DiscussHelper::getConfig();

		$parent		= DiscussHelper::getTable( 'Post' );
		$parent->load( $reply->parent_id );

		if( $reply->get( 'user_type') == DISCUSS_POSTER_GUEST )
		{
			$emailData['postAuthor']	= $senderName;
			$emailData['commentAuthor']	= $senderName;
			$emailData['replyAuthorAvatar' ]	= '';
		}
		else
		{
			$profile 	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $user->id );

			$emailData['replyAuthor' ]			= $profile->getName();
			$emailData['commentAuthor']			= $profile->getName();
			$emailData['replyAuthorAvatar' ]	= $profile->getAvatar();
		}

		$emailContent = $reply->content;

		if( $reply->content_type != 'html' )
		{
			// the content is bbcode. we need to parse it.
			$emailContent	= EasyDiscussParser::bbcode( $emailContent);
			$emailContent	= EasyDiscussParser::removeBrTag( $emailContent);
		}

		// If reply is html type we need to strip off html codes.
		if( $reply->content_type == 'html' )
		{
			$emailContent 			= strip_tags( $emailContent );
		}

		$emailContent	= $parent->trimEmail( $emailContent );

		$emailData['postTitle']		= $parent->title;
		$emailData['comment']		= $reply->content;
		$emailData['postLink']		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id=' . $parent->id, false, true);
		$emailData['replyContent']	= $reply->content;

		$attachments	= $reply->getAttachments();
		$emailData['attachments']	= $attachments;

		$excludeEmails = array();
		$subscriberEmails			= array();

		if( ($config->get('main_sitesubscription') ||  $config->get('main_postsubscription') ) && $config->get('notify_subscriber') && $reply->published == DISCUSS_ID_PUBLISHED)
		{
			$emailData['emailTemplate']	= 'email.subscription.reply.new.php';
			$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);

			$emailData['post_id'] = $parent->id;
			$emailData['cat_id'] = $parent->category_id;
			//$subcribersEmails = DiscussHelper::getHelper( 'Mailer' )->notifyThreadSubscribers( $emailData );

			//$excludeEmails		= array_merge( $excludeEmails, $subcribersEmails);

			// Notify all subscriber about new replies
			DiscussHelper::getHelper( 'Mailer' )->notifySubscribers( $emailData, $excludeEmails );
		}

		//notify post owner.
		$postOwnerId	= $parent->user_id;
		$postOwner		= JFactory::getUser( $postOwnerId );
		$ownerEmail		= $postOwner->email;

		if( $parent->user_type != 'member' )
		{
			$ownerEmail 	= $parent->poster_email;
		}

		if( $config->get( 'notify_owner' ) && $reply->published	== DISCUSS_ID_PUBLISHED && ($postOwnerId != $user->id) && !in_array( $ownerEmail , $subscriberEmails ) && !empty( $ownerEmail ) )
		{
			$emailData['owner_email'] = $ownerEmail;
			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
			$emailData['emailTemplate'] = 'email.post.reply.new.php';
			DiscussHelper::getHelper( 'Mailer' )->notifyThreadOwner( $emailData );

			$excludeEmails[] = $ownerEmail;
		}

		// Notify Participants
		if( $config->get( 'notify_participants' ) && $reply->published	== DISCUSS_ID_PUBLISHED )
		{
			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
			$emailData['emailTemplate'] = 'email.post.reply.new.php';
			DiscussHelper::getHelper( 'Mailer' )->notifyThreadParticipants( $emailData, $excludeEmails );
		}

		//if reply under moderation, send owner a notification.
		if( $reply->published == DISCUSS_ID_PENDING )
		{
			// Generate hashkeys to map this current request
			$hashkey		= DiscussHelper::getTable( 'Hashkeys' );
			$hashkey->uid	= $reply->id;
			$hashkey->type	= DISCUSS_REPLY_TYPE;
			$hashkey->store();

			$approveURL	= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=approvePost&key=' . $hashkey->key );
			$rejectURL	= DiscussHelper::getExternalLink('index.php?option=com_easydiscuss&controller=posts&task=rejectPost&key=' . $hashkey->key );
			$emailData[ 'moderation' ]	= '<div style="display:inline-block;width:100%;padding:20px;border-top:1px solid #ccc;padding:20px 0 10px;margin-top:20px;line-height:19px;color:#555;font-family:\'Lucida Grande\',Tahoma,Arial;font-size:12px;text-align:left">';
			$emailData[ 'moderation' ] .= '<a href="' . $approveURL . '" style="display:inline-block;padding:5px 15px;background:#fc0;border:1px solid #caa200;border-bottom-color:#977900;color:#534200;text-shadow:0 1px 0 #ffe684;font-weight:bold;box-shadow:inset 0 1px 0 #ffe064;-moz-box-shadow:inset 0 1px 0 #ffe064;-webkit-box-shadow:inset 0 1px 0 #ffe064;border-radius:2px;moz-border-radius:2px;-webkit-border-radius:2px;text-decoration:none!important">' . JText::_( 'COM_EASYDISCUSS_EMAIL_APPROVE_REPLY' ) . '</a>';
			$emailData[ 'moderation' ] .= ' ' . JText::_( 'COM_EASYDISCUSS_OR' ) . ' <a href="' . $rejectURL . '" style="color:#477fda">' . JText::_( 'COM_EASYDISCUSS_REJECT' ) . '</a>';
			$emailData[ 'moderation' ] .= '</div>';

			$emailData['emailSubject'] = JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_MODERATE', $parent->title);
			$emailData['emailTemplate'] = 'email.post.reply.moderation.php';

			DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin' ), $config->get( 'notify_moderator' ) );

		} elseif( $reply->published	== DISCUSS_ID_PUBLISHED ) {

			$emailData['emailTemplate']	= 'email.post.reply.new.php';
			$emailData['emailSubject']	= JText::sprintf('COM_EASYDISCUSS_NEW_REPLY_ADDED', $parent->id , $parent->title);
			$emailData['post_id'] = $parent->id;

			DiscussHelper::getHelper( 'Mailer' )->notifyAdministrators( $emailData, array(), $config->get( 'notify_admin_onreply' ), $config->get( 'notify_moderator_onreply' ) );
		}
	}
}
