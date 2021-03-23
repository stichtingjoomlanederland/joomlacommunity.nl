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

class EasyDiscussThemesHelperPost extends EasyDiscussHelperAbstract
{
	/**
	 * Generates the attachments indicator for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function attachments(EasyDiscussPost $post)
	{
		static $html = [];

		if (!isset($html[$post->id])) {

			if (!$post->getAttachments()) {
				$html[$post->id] = '';
				return $html[$post->id];
			}

			$theme = ED::themes();
			$theme->set('post', $post);

			$attachments = $theme->output('site/helpers/post/attachments');

			$html[$post->id] = $attachments;
		}

		return $html[$post->id];
	}

	/**
	 * Renders the author 
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function author(EasyDiscussPost $post)
	{
		static $cache = [];

		$user = $post->getOwner();
		$anonymous = $post->isAnonymous();
		$key = $user->id;

		if ($anonymous) {
			$key = 'anonymous';

			// If viewer can view anonymous data, the key should be different
			if ($post->canAccessAnonymousPost()) {
				$key .= $post->poster_name;
			}
		}

		// Do not cache the guest as there might have different guests posted
		if (!isset($cache[$key]) || !$key) {
			$theme = ED::themes();
			$theme->set('user', $user);
			$theme->set('post', $post);
			$cache[$key] = $theme->output('site/helpers/post/author');
		}

		return $cache[$key];
	}

	/**
	 * Renders a list of badges beside the authors name in post listing
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function badges(DiscussProfile $author)
	{
		if (!$this->config->get('main_badges')) {
			return;
		}

		// Non logged in users will never have badge
		if (is_null($author->id)) {
			return;
		}

		static $data = [];

		if (!isset($data[$author->id])) {

			$badges = $author->getBadges();

			$theme = ED::themes();
			$theme->set('author', $author);
			$theme->set('badges', $badges);
			$data[$author->id] = $theme->output('site/helpers/post/badges');
		}

		return $data[$author->id];
	}

	/**
	 * Generates the category label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function category($category)
	{
		static $cache = [];

		$key = $category->id;

		if (!isset($cache[$key])) {
			$theme = ED::themes();
			$theme->set('category', $category);
			$cache[$key] = $theme->output('site/helpers/post/category');
		}

		return $cache[$key];
	}

	/**
	 * Generates the featured label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function featured()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/helpers/post/featured');
		}

		return $html;
	}

	/**
	 * Renders filters for posts
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function filters($baseUrl, $activeFilter, $activeCategory = null, $activeSort = null, $options = [])
	{
		static $labels = null;
		static $types = null;
		static $priorities = null;
		static $categories = null;

		// Normalize options
		$selectedLabels = ED::normalize($options, 'selectedLabels', []);
		$selectedTypes = ED::normalize($options, 'selectedTypes', []);
		$selectedPriorities = ED::normalize($options, 'selectedPriorities', []);
		$showCategories = ED::normalize($options, 'showCategories', true);
		$showSorting = ED::normalize($options, 'showSorting', true);
		$search = ED::normalize($options, 'search', '');
		
		$config = ED::config();

		if (is_null($categories) && $showCategories) {
			$model = ED::model('categories');
			$categories = $model->getCategoryTree([], [
				'showSubCategories' => false,
				'showPostCount' => false
			]);
		}

		if (is_null($labels)) {
			$labels = [];
			
			if ($config->get('main_labels')) {
				$model = ED::model('PostLabels');
				$labels = $model->getLabels();

				if ($labels) {
					foreach ($labels as $label) {
						ED::cache()->set($label, 'labels');
					}
				}
			}
		}

		if (is_null($types)) {

			$types = [];

			if ($config->get('layout_post_types')) {
				$model = ED::model('PostTypes');
				$types = $model->getPostTypesOnListings($activeCategory);

				foreach ($types as $type) {
					ED::cache()->set($type, 'posttypes', 'alias');
				}
			}
		}

		if (is_null($priorities)) {
			$priorities = [];

			if ($config->get('post_priority')) {
				$model = ED::model('Priorities');
				$priorities = $model->getAllPriorities();

				foreach ($priorities as $priority) {
					ED::cache()->set($priority, 'priorities');
				}
			}
		}

		$activeLabels = [];

		if ($selectedLabels) {
			// Ensure that they are an array
			if (!is_array($selectedLabels)) {
				$selectedLabels = [$selectedLabels];
			}

			foreach ($selectedLabels as $selectedLabelId) {
				$selectedLabel = ED::cache()->get($selectedLabelId, 'labels');

				$activeLabels[] = $selectedLabel;
			}
		}

		$activePostTypes = [];

		if ($selectedTypes) {
			// Ensure that they are an array
			if (!is_array($selectedTypes)) {
				$selectedTypes = [$selectedTypes];
			}

			foreach ($selectedTypes as $selectedTypeId) {
				$selectedType = ED::cache()->get($selectedTypeId, 'posttypes');

				if ($selectedType) {
					$activePostTypes[] = $selectedType;
				}
			}
		}

		$activePriorities = [];

		if ($selectedPriorities) {
			// Ensure that they are an array
			if (!is_array($selectedPriorities)) {
				$selectedPriorities = [$selectedPriorities];
			}

			foreach ($selectedPriorities as $selectedPriorityId) {
				$selectedPriority = ED::cache()->get($selectedPriorityId, 'priorities');

				if ($selectedPriority) {
					$activePriorities[] = $selectedPriority;
				}
			}
		}

		// Prior to 5.0.x, the all filter was using 'allposts'. We need to change it to 'all'
		if ($activeFilter == 'allposts') {
			$activeFilter = 'all';
		}

		$activeCategory = ED::category($activeCategory);

		$hasFilters = false;

		if ($activeLabels || $activePostTypes || $activePriorities) {
			$hasFilters = true;
		}

		// Active tag
		$activeTag = ED::normalize($options, 'activeTag', null);
		$activeTag = ED::tag((int) $activeTag);

		$theme = ED::themes();
		$theme->set('hasFilters', $hasFilters);
		$theme->set('search', $search);
		$theme->set('showCategories', $showCategories);
		$theme->set('showSorting', $showSorting);
		$theme->set('activeSort', $activeSort);
		$theme->set('activeTag', $activeTag);
		$theme->set('activeCategory', $activeCategory);
		$theme->set('categories', $categories);
		$theme->set('selectedLabels', $selectedLabels);
		$theme->set('selectedTypes', $selectedTypes);
		$theme->set('selectedPriorities', $selectedPriorities);
		$theme->set('activeLabels', $activeLabels);
		$theme->set('activePriorities', $activePriorities);
		$theme->set('activePostTypes', $activePostTypes);
		$theme->set('activeFilter', $activeFilter);
		$theme->set('baseUrl', $baseUrl);
		$theme->set('labels', $labels);
		$theme->set('types', $types);
		$theme->set('priorities', $priorities);
		$output = $theme->output('site/helpers/post/filters');

		return $output;		
	}

	/**
	 * Generates the hidden indicator for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function hidden()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/helpers/post/hidden');
		}

		return $html;
	}

	/**
	 * Generates the last replied time for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function lastReplied(EasyDiscussPost $post)
	{
		$replier = $post->getLastReplier();

		if (!$replier) {
			return;
		}

		static $cache = [];

		$key = $post->id;

		if (!isset($cache[$key])) {
			$theme = ED::themes();
			$theme->set('replier', $replier);
			$theme->set('post', $post);

			$cache[$key] = $theme->output('site/helpers/post/last.replied');
		}

		return $cache[$key];
	}

	/**
	 * Renders the posts location
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function location(EasyDiscussPost $post)
	{
		if (!$post->hasLocation()) {
			return;
		}

		if (!$post->isQuestion() && !$this->config->get('main_location_reply')) {
			return;
		}

		if ($post->isQuestion() && !$this->config->get('main_location_discussion')) {
			return;
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$html = $theme->output('site/helpers/post/location');

		return $html;
	}

	/**
	 * Generates the locked label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function locked($options = [])
	{
		static $html = [];

		$size = ED::normalize($options, 'size', '01');
		$key = $size;

		if (!isset($html[$key])) {
			$theme = ED::themes();
			$theme->set('size', $size);

			$html[$key] = $theme->output('site/helpers/post/locked');
		}

		return $html[$key];
	}

	/**
	 * Generates the new label
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function new()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/helpers/post/new');
		}

		return $html;
	}

	/**
	 * Renders the custom post label for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function label($label)
	{
		if (!$this->config->get('main_labels')) {
			return;
		}

		static $cache = [];

		if (!isset($cache[$label->id])) {
			// Always default font color to white
			$fontColor = '#fff';

			$theme = ED::themes();
			$theme->set('fontColor', $fontColor);
			$theme->set('label', $label);

			$cache[$label->id] = $theme->output('site/helpers/post/label');
		}

		return $cache[$label->id];
	}

	/**
	 * Renders the post tag for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function tags(EasyDiscussPost $post)
	{
		$tags = $post->getTags();

		if (!$tags) {
			return;
		}

		$theme = ED::themes();
		$theme->set('tags', $tags);

		$output = $theme->output('site/helpers/post/tags');

		return $output;
	}

	/**
	 * Display the imported from email parser indication for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function email()
	{
		static $output = null;

		if (is_null($output)) {
			$theme = ED::themes();
			$output = $theme->output('site/helpers/post/email');
		}

		return $output;
	}

	/**
	 * Generates the password label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function password()
	{
		static $html = null;

		if (is_null($html)) {
			$theme = ED::themes();
			$html = $theme->output('site/helpers/post/password');
		}

		return $html;
	}

	/**
	 * Renders the list of participants for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function participants($participants)
	{
		if (!$participants) {
			return;
		}

		$theme = ED::themes();
		$theme->set('participants', $participants);
		$output = $theme->output('site/helpers/post/participants');

		return $output;
	}

	/**
	 * Renders the list of participants for the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function priority(EasyDiscussPost $post)
	{
		static $cache = [];

		if (!$this->config->get('post_priority')) {
			return;
		}

		if (!isset($cache[$post->id])) {
			if (!$post->getPriority()) {
				$cache[$post->id] = '';

				return;
			}

			$priority = $post->getPriority();

			$theme = ED::themes();
			$theme->set('priority', $priority);
			$cache[$post->id] = $theme->output('site/helpers/post/priority');
		}

		return $cache[$post->id];
	}

	/**
	 * Generates the replies icon
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function replies(EasyDiscussPost $post)
	{
		$theme = ED::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/replies');

		return $output;
	}

	/**
	 * Generates the ratings for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function ratings(EasyDiscussPost $post)
	{
		if (!$this->config->get('main_ratings')) {
			return;
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$output = $theme->output('site/helpers/post/ratings');

		return $output;
	}

	/**
	 * Generates the resolved label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function resolved($options = [])
	{
		static $html = [];

		$size = ED::normalize($options, 'size', '01');

		$key = $size;

		if (!isset($html[$key])) {

			if (!$this->config->get('main_qna')) {
				$html = '';
				return;
			}

			$theme = ED::themes();
			$theme->set('size', $size);
			
			$html[$key] = $theme->output('site/helpers/post/resolved');
		}

		return $html[$key];
	}

	/**
	 * Renders the post schema (json+ld) data on the page
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function schema(EasyDiscussPost $post, $answer = null, $tags = [])
	{
		static $cache = [];

		if (!isset($cache[$post->id])) {
			$schemaTags = [];

			$theme = ED::themes();

			if ($tags) {
				foreach ($tags as $tag) {
					$schemaTags[] = ED::string()->escape($tag->title);
				}
			}
			
			$theme->set('answer', $answer);
			$theme->set('post', $post);
			$theme->set('schemaTags', $schemaTags);
			
			$cache[$post->id] = $theme->output('site/helpers/post/schema');
		}

		return $cache[$post->id];
	}

	/**
	 * Renders the signature in a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function signature(EasyDiscussPost $post)
	{
		static $cache = [];

		if (!$this->config->get('main_signature_visibility') || $post->isAnonymous()) {
			return;
		}

		$user = $post->getOwner();

		if (!isset($cache[$user->id])) {
			$signature = trim($user->getSignature());
			
			if (!$signature || !ED::acl($user->id)->allowed('show_signature')) {
				$cache[$user->id] = '';
				return $cache[$user->id];
			}

			$theme = ED::themes();
			$theme->set('signature', $signature);
			$cache[$user->id] = $theme->output('site/helpers/post/signature');
		}

		return $cache[$user->id];
	}

	/**
	 * Renders the title of the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function title(EasyDiscussPost $post, $options = [])
	{
		$enforceMaxLength = ED::normalize($options, 'enforceMaxLength', true);
		$customClass = ED::normalize($options, 'customClass', '');
		$truncateLength = ED::normalize($options, 'truncateLength', 0);

		$title = $post->getTitle();
		$maxLength = $this->config->get('layout_title_maxlength');

		if ($truncateLength) {
			$maxLength = $truncateLength;
		}

		if ($enforceMaxLength && EDJString::strlen($title) > $maxLength) {
			$title = EDJString::substr($title, 0, $maxLength) . ' ' . JText::_('COM_ED_ELLIPSES');
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('title', $title);
		$theme->set('customClass', $customClass);
		
		$output = $theme->output('site/helpers/post/title');
		return $output;
	}

	/**
	 * Renders the content of the post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function content(EasyDiscussPost $post, $options = [])
	{
		$customContent = ED::normalize($options, 'customContent', '');
		$customClass = ED::normalize($options, 'customClass', '');
		$maxLength = ED::normalize($options, 'truncateLength', 0);

		if ($post->isProtected() && !$post->isMine()) {
			$content = '<i>' . JText::_('COM_EASYDISCUSS_PASSWORD_PROTECTED') . '</i>';
		} else {

			$content = strip_tags($post->getContent());

			// We'll assume where the custom content has been formatted.
			if ($customContent) {
				$content = $customContent;
			}

			if ($maxLength && EDJString::strlen($content) > $maxLength) {
				$content = EDJString::substr($content, 0, $maxLength) . ' ' . JText::_('COM_ED_ELLIPSES');
			}
		}

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('content', $content);
		$theme->set('customClass', $customClass);
		
		$output = $theme->output('site/helpers/post/content');
		return $output;
	}

	/**
	 * Generates the post type label for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function type(EasyDiscussPost $post, $useCache = true)
	{
		if (!$this->config->get('layout_post_types')) {
			return;
		}

		$type = $post->getPostTypeObject($useCache);

		if (!$type) {
			return;
		}

		$typeSuffix = $post->getPostTypeSuffix();

		$theme = ED::themes();
		$theme->set('post', $post);
		$theme->set('type', $type);
		$theme->set('typeSuffix', $typeSuffix);
		
		$output = $theme->output('site/helpers/post/type');

		return $output;
	}

	/**
	 * Renders viewers of a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function viewers(EasyDiscussPost $post)
	{
		static $cache = [];

		if (!isset($cache[$post->id])) {
			$config = ED::config();
			$enabled = $config->get('main_viewingpage');

			if (!$enabled) {
				return;
			}

			// Default hash
			$uri = $post->getNonSEFLink();
			$hash = md5($uri);

			$model = ED::model('Users');
			$users = $model->getPageViewers($hash);

			if (!$users) {
				return;
			}

			$theme = ED::themes();
			$theme->set('users', $users);

			$cache[$post->id] = $theme->output('site/post/widgets/viewers/default');
		}

		return $cache[$post->id];
	}

	/**
	 * Renders the vote widget for a post
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function votes(EasyDiscussPost $post)
	{
		static $cache = [];
		
		if (!$this->config->get('main_allowvote') && !$this->config->get('main_allowquestionvote')) {
			return;
		}

		if (!isset($cache[$post->id])) {
			if (!$post->isVoteEnabled()) {
				$cache[$post->id] = '';

				return;
			}

			$theme = ED::themes();
			$theme->set('post', $post);

			$cache[$post->id] = $theme->output('site/helpers/post/votes');
		}

		return $cache[$post->id];
	}
}
