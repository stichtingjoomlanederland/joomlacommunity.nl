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
defined('_JEXEC') or die('Unauthorized Access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelDashboard extends EasyDiscussAdminModel
{
	public $name = 'dashboard';

	public function __construct()
	{
		parent::__construct();

		// Get the pagination from request
		$limit = $this->getStateFromRequest('limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Gets the total posts created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalPosts()
	{
		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_thread`';
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Retrieves the total replies created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalReplies()
	{
		$db = ED::db();

		$query	= 'SELECT COUNT(1) FROM `#__discuss_posts` WHERE ' . $db->nameQuote('parent_id') . ' != ' . $db->Quote('0');
		$db->setQuery($query);
		return $db->loadResult();
	}

	/**
	 * Retrieves the total number of resolved posts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalSolved()
	{
		$db = ED::db();

		$query = "select count(1) from `#__discuss_thread` as a";
		$query .= " where " . $db->nameQuote('a.published') . " = " . $db->Quote('1');
		$query .= " and " . $db->nameQuote('a.isresolve') . " = " . $db->Quote('1');

		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 *
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalTags()
	{
		$db = ED::db();
		$query	= 'SELECT COUNT(1) FROM `#__discuss_tags`';
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Retrieves the total number of categories created on the site
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getTotalCategories()
	{
		$db = ED::db();

		$query = 'SELECT COUNT(1) FROM `#__discuss_category`';
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Generates the posts graph
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostsGraph($isQuestion = true)
	{
		// Get dbo
		$db = ED::db();

		// Get the past 7 days
		$today = JFactory::getDate();
		$dates = array();

		for ($i = 0 ; $i < 7; $i++) {

			$date = JFactory::getDate('-' . $i . ' day');
			$dates[] = $date->format('Y-m-d');
		}

		// Reverse the dates
		$dates = array_reverse($dates);

		// Prepare the main result
		$result = new stdClass();
		$result->dates = $dates;
		$result->count = array();

		$i = 0;
		foreach ($dates as $date) {

			$query   = array();
			$query[] = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__discuss_posts');
			$query[] = 'WHERE DATE_FORMAT(' . $db->quoteName('created') . ', GET_FORMAT(DATE, "ISO")) =' . $db->Quote($date);
			$query[] = 'AND ' . $db->quoteName('published') . '=' . $db->Quote(1);

			if ($isQuestion) {
				$query[] = 'AND ' . $db->quoteName('parent_id') . '=' . $db->Quote(0);
			}

			$query = implode(' ', $query);

			$db->setQuery($query);
			$total = $db->loadResult();

			$result->count[$i] = $total;

			$i++;
		}

		return $result;
	}

	/**
	 * Generates the posts pie charts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getPostsPie()
	{
		$posts = ED::model('posts');

		$unanswered = $posts->getUnansweredCount();
		$unresolved = $posts->getUnresolvedCount();
		$resolved = $this->getTotalSolved();

		$charts = array('unanswered'=>'#dd5036,#e16751', 'unresolved'=>'#ffd54f,#ffe180', 'resolved'=>'#39b54a,#50c861');
		$pieArray = array();

		foreach ($charts as $chart => $color) {
			$pie = new stdclass();

			// To separate the color for highlighting.
			$color = explode(',', $color);

			$pie->value = $$chart;
			$pie->color = $color[0];
			$pie->highlight = $color[1];
			$pie->label = ucfirst($chart) . " Posts";

			$pieArray[] = $pie;
		}

		return json_encode($pieArray);
	}

	/**
	 * Generates the posts pie charts by month
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getMonthPie()
	{
		$array = array();
		$color = $this->pieColors();
		$max = 0;

		$model = ED::model('posts');
		$totalPosts = $model->getTotalPostsByMonth();

		foreach ($totalPosts as $post) {

			// Get the data from current year during first display.
			if ($post->year != date('Y')) {
				continue;
			}

			$pieColor = explode(',', $color[$max]);

			$pie = new stdclass();

			$dateObj = DateTime::createFromFormat('!m', $post->month);
			$monthName = $dateObj->format('F');

			$pie->value = $post->total;
			$pie->color = $pieColor[0];
			$pie->highlight = $pieColor[1];
			$pie->label = $monthName;

			$array[] = $pie;
			$max++;
		}

		return json_encode($array);
	}

	/**
	 * Generates the posts categories pie charts
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getCategoryPie()
	{
		$options = array('published' => true);

		// get all categories on site
		$categories = ED::model('categories');
		$categoriesTotal = $categories->getAllCategories($options);

		$color = $this->pieColors();
		$array = array();
		$max = 0;

		foreach ($categoriesTotal as $category) {
			$pie = new stdclass();
			$cat = ED::category($category->id);
			$totalPosts = $cat->getTotalPosts();

			// We do not want to show the statistic if there are no post inside the category.
			if (!$totalPosts) {
				continue;
			}

			$pie->value = $totalPosts;
			$pie->label = $category->title;

			$array[] = $pie;
			$max++;

			// For now we only support 10 categories in the charts at once.
			if ($max > 9) {
				break;
			}
		}

		// sort the categories by highest posts
		usort($array,array($this, 'sortByValue'));

		$limit = 0;

		// Since the category has been sorted, we need to sort the color ordering as well.
		foreach ($array as $key) {
			$pieColor = explode(',', $color[$limit]);

			$key->color = $pieColor[0];
			$key->highlight = $pieColor[1];

			$limit++;
		}

		return json_encode($array);
	}

	public function sortByValue($a, $b)
	{
		return $b->value - $a->value;
	}

	public function pieColors()
	{
		// Format : '#mainColor,#highlightColor'
		$color = array(
			'#5d3bb3,#7352c6',
			'#9975e6,#af93eb',
			'#428BCA,#609dd2',
			'#50b9e6,#79c9ec',
			'#30a767,#39c67a',
			'#a1d063,#b8dc89',
			'#ffd64f,#ffe180',
			'#ef6c00,#ff811a',
			'#e53835,#eb6361',
			'#ac9b91,#bdb0a8',
			'#ccc1ad,#d8cfc0',
			'#607d8b,#7794a1'
			);

		return $color;
	}
}
