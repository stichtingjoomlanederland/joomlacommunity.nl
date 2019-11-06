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

class EasyDiscussSubscription extends EasyDiscuss
{
	public $interval = 'daily';

	/**
	 * Renders the subscription html codes
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function html($userid, $cid = 0, $type = DISCUSS_ENTITY_TYPE_POST, $class = '', $simpleText = true)
	{
		// If guest subscription is disabled, do not show subscription link at all.
		if (!$userid && !$this->config->get('main_allowguestsubscribe')) {
			return;
		}

		$model = ED::model('Subscribe', false);
		$type = $type == 'index' ? 'site' : $type;

		// Ensure that subscription is enabled for post.
		if (!$this->config->get('main_postsubscription') && $type == 'post') {
			return;
		}

		$subscribed = $model->isSubscribed($userid, $cid, $type);
		$sid = $subscribed ? $subscribed : 0;

		$view = $this->input->get('view', '', 'cmd');

		$theme = ED::themes();
		$theme->set('view', $view);
		$theme->set('subscribed', $subscribed);
		$theme->set('cid', $cid);
		$theme->set('sid', $sid);
		$theme->set('simple', $simpleText);
		$theme->set('class', $class);
		$theme->set('type', $type);

		$namespace = 'site/subscription/site';

		// if ($type == 'site' || $type == 'category') {
		// 	$namespace .= 'site';
		// } else {
		// 	$namespace .= 'post';
		// }

		$output = $theme->output($namespace);

		return $output;
	}


	public function processDigest($max = 5)
	{
		// If the feature is disabled, stop here.
		if (!$this->config->get('main_email_digest')) {
			return false;
		}

		$now = ED::date()->toSql();

		$model = ED::model('Subscribe');
		$emails = $model->getDigestSubscribers($now);

		// nothing to process
		if (! $emails) {
			return false;
		}

		foreach ($emails as $email) {

			$items = $model->getDigestEmailSubscriptions($now, $email);

			if (! $items) {
				// nothing to process
				continue;
			}

			$postsContent = "";
			$repliesContent = "";
			$commentsContent = "";

			// now we retrive subscription info
			$categorySub = array();
			$siteSub = null;

			foreach($items as $item) {

				$obj = new stdClass();

				$managelink = '';
				if ($item->member) {
					// link to manage subscriptions for member;
					$managelink = EDR::_('view=subscription', true, true);
				}


				// post limit
				$obj->limit = $item->count;

				if ($item->type == 'category') {

					$obj->id = $item->cid;
					$obj->title = $item->subtitle;
					$obj->alias = $item->subalias;
					$obj->link = EDR::getRoutedURL('view=forums&category_id=' . $item->cid, false, true);
					$obj->subdata = $item;
					$obj->unlink = ED::getUnsubscribeLink($item, true, true);
					$obj->posts = array();

					$categorySub[$item->cid] = $obj;

				} else if ($item->type == 'site') {

					$obj->subdata = $item;
					$obj->unlink = ED::getUnsubscribeLink($item, true, true);
					$obj->posts = array();

					$siteSub = $obj;
				}
			}

			// now lets get the new posts
			$posts = $model->getDigestPosts($items, $now);

			if ($posts) {

				// preload posts
				ED::post($posts);

				// group the posts
				foreach($posts as $row) {
					$post = ED::post($row->id);

					if ($row->subs_type == 'category') {
						$categorySub[$row->subs_cid]->posts[] = $post;
					} else {
						$siteSub->posts[] = $post;
					}

				}

				$namespace = "site/emails/email.digest.posts";

				$theme = ED::themes();
				$theme->set('site', $siteSub);
				$theme->set('cats', $categorySub);

				$postsContent = $theme->output($namespace);
			}

			// now lets get the replies
			$replies = array();

			if ($this->config->get('main_email_digest_reply')) {
				$replies = $model->getDigestReplies($items, $now);

				if ($replies) {
					ED::post($replies);

					$data = array();

					foreach ($replies as $row) {
						$reply = ED::post($row->id);
						$data[] = $reply;
					}

					$namespace = "site/emails/email.digest.replies";

					$theme = ED::themes();
					$theme->set('data', $data);
					$theme->set('managelink', $managelink);

					$repliesContent = $theme->output($namespace);
				}
			}

			// now lets get the comments
			$comments = array();

			if ($this->config->get('main_email_digest_comment')) {
				$comments = $model->getDigestComments($items, $now);
				if ($comments) {
					// ED::post($replies);

					$data = array();

					$maxLength = 150;

					foreach ($comments as $row) {

						$item = new stdClass();

						$item->comment = ED::badwords()->filter($row->comment);
						if (JString::strlen($item->comment) > $maxLength) {
							$item->comment = JString::substr($row->comment, 0, $maxLength) . '...';
						}
						$item->authorName = $row->name;
						$item->authorEmail = $row->email;
						$item->post = ED::post($row->post_id);
						$item->question = ED::post($row->question_id);

						$data[] = $item;
					}

					$namespace = "site/emails/email.digest.comments";
					$theme = ED::themes();
					$theme->set('data', $data);
					$theme->set('managelink', $managelink);
					$commentsContent = $theme->output($namespace);
				}
			}

			// proceed to email sending if there are something.
			if ($posts || $replies || $comments) {

				$logo = ED::getLogo();

				$theme = ED::themes();
				$theme->set('sitename', ED::jconfig()->get('sitename'));
				$theme->set('now', ED::date()->display());
				$theme->set('posts', $postsContent);
				$theme->set('replies', $repliesContent);
				$theme->set('comments', $commentsContent);
				$theme->set('logo', $logo);

				$namespace = "site/emails/email.digest.subscriptions";
				$body = $theme->output($namespace);

				$subject = JText::sprintf('COM_EASYDISCUSS_DIGEST_EMAIL_SUBJECT', ED::date()->display(), ED::jconfig()->get('sitename'));

				// add into mail queue
				ED::mailer()->addQueue($email, $subject, $body);
			}

			// now update subscriptions sent_out
			$model->updateDigestSentOut($items);
		}

		return true;
	}


	public function process()
	{
		$date = ED::date();
		$now = $date->toMySQL();

		$model = ED::model('Subscribe');
		$subscribers = $model->getSiteSubscribers($this->interval, $now);

		$total = count($subscribers);

		if(empty($total))
		{
			return false;
		}

		foreach($subscribers as $subscriber)
		{
			$notify	= DiscussHelper::getNotification();

			$data = array();
			$rows = $model->getCreatedPostByInterval($subscriber->sent_out, $now);
			$posts = array();

			if( $rows )
			{
				foreach( $rows as $row )
				{
					$row['categorylink']	= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=categorie&layout=listings&category_id='.$row['category_id'], false, true);
					$row['link']			= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=post&id='.$row['id'], false, true);
					$row['userlink'] 		= DiscussRouter::getRoutedURL('index.php?option=com_easydiscuss&view=profile&id=' . $row['user_id'] , false , true );

					$category = ED::table('Category');
					$category->load( $row['category_id'] );

					$creator = ED::user($row['user_id']);

					$row['category']	= $category->getTitle();
					$row['avatar'] 		= $creator->getAvatar();
					$row['name']	 	= $creator->getName();
					$row['date']		= DiscussDateHelper::toFormat( $row['created'] , '%b %e, %Y' );
					$row['message']		= DiscussHelper::parseContent( $row['content'] );

					$posts[]		= $row;
				}
			}
			$data['post']		= $posts;
			$data['total']		= count($data['post']);

			$data['unsubscribeLink']	= DiscussHelper::getUnsubscribeLink( $subscriber, true, true);

			$subject			= $date->toMySQL();

			switch( strtoupper($this->interval) )
			{
				case 'DAILY':
					$subject 			= $date->toFormat( '%F' );
					$data['interval']	= JText::_( 'today' );
				break;
				case 'WEEKLY':
					$subject			= $date->toFormat( '%V' );
					$data['interval']	= JText::_( 'this week' );
				break;
				case 'MONTHLY':
					$subject 	= $date->toFormat( '%B' );
					$data['interval']	= JText::_( 'this month' );
				break;
			}

			if(!empty($data['post']))
			{
				$notify->addQueue($subscriber->email, JText::sprintf('COM_EASYDISCUSS_YOUR_'.$this->interval.'_SUBSCRIPTION', $subject) , '', 'email.subscription.site.interval.php', $data);
			}

			$subscribe = DiscussHelper::getTable( 'Subscribe' );
			$subscribe->load($subscriber->id);
			$subscribe->sent_out = $now;
			$subscribe->store();
		}
	}

	public function format($posts, $type)
	{
		foreach ($posts as $item) {

			// If type is post, we need to properly format the post.
			if ($type == 'post') {

				$post = ED::post($item->cid);

				$item->repliesCount = $post->getTotalReplies();
				$item->viewCount = $post->getHits();
				$item->voteCount = $post->getTotalVotes();
				$item->likeCount = $post->getTotalLikes();
				$item->permalink = $post->getPermalink();
			}

			if ($type == 'category') {

				$category = ED::category($item->cid);

				$item->totalPosts = $category->getTotalPosts();
				$item->permalink = $category->getPermalink();
				$item->avatar = $category->getAvatar();
			}

			// Add unsubscribe link
			$item->unsubscribeLink = ED::getUnsubscribeLink($item);
		}

		return $posts;
	}

	/**
	 * Render the posts subscription graph data
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getGraphSubscription($userId, $type)
	{
		$model = ED::model('Subscribe');

		// Get the graph data
		$subscribe = $model->getSubscriptionGraph($userId, $type);

		// Format the ticks for the posts
		$postsTicks = array();

		foreach ($subscribe->dates as $dateString) {

			// Normalize the date string first
			$dateString = str_ireplace('/', '-', $dateString);
			$date = ED::date($dateString);

			$postsTicks[] = $date->display(JText::_('COM_EASYDISCUSS_DATE_DM'));
		}

		return array($subscribe, $postsTicks);
	}

	public function getTotalSubscriptions($userId)
	{
		$model = ED::model('Subscribe');

		$result = $model->getTotalSubscriptions($userId);

		return $result;
	}
}
