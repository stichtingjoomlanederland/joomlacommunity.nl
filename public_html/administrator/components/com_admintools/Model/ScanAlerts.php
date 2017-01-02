<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Model\DataModel;

/**
 * @property   int     admintools_scanalert_id
 * @property   string  path
 * @property   int     scan_id
 * @property   string  diff
 * @property   string  threat_score
 *
 * @property   int     newfile
 * @property   int     suspicious
 * @property   int     acknowledged
 *
 * @method  $this   scan_id() scan_id(int $v)
 * @method  $this   acknowledged() acknowledged(bool $v)
 * @method  $this   status() status(string $v)
 * @method  $this   search() search(string $v)
 */
class ScanAlerts extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_scanalerts';
		$config['idFieldName'] = 'admintools_scanalert_id';
		$config['aliasFields'] = array('enabled' => 'acknowledged');
		$config['autoChecks']  = false;

		parent::__construct($container, $config);

		$this->addKnownField('newfile');
		$this->addKnownField('suspicious');
		$this->addKnownField('filestatus');

		$this->addBehaviour('Filters');
	}

	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();

		$query = parent::buildQuery($overrideLimits)
		               ->clear('select')
		               ->clear('order')
		               ->select(array(
			               $db->qn('admintools_scanalert_id'),
			               'IF(' . $db->qn('diff') . ' != "",0,1) AS ' . $db->qn('newfile'),
			               'IF(' . $db->qn('diff') . ' LIKE "###SUSPICIOUS FILE###%",1,0) AS ' . $db->qn('suspicious'),
			               'IF(' . $db->qn('diff') . ' != "",' .
			               'IF(' . $db->qn('diff') . ' LIKE "###SUSPICIOUS FILE###%",' .
			               $db->q('0-suspicious') . ',' . $db->q('2-modified') . ')'
			               . ',' . $db->q('1-new') . ') AS ' . $db->qn('filestatus'),
			               $db->qn('path'),
			               $db->qn('threat_score'),
			               $db->qn('acknowledged'),
			               $db->qn('scan_id'),
		               ));

		$search = $this->getState('search', '');

		if ($search)
		{
			$query->where($db->qn('path') . ' LIKE ' . $db->q('%' . $search . '%'));
		}

		$status = $this->getState('status', '');

		switch ($status)
		{
			case 'new':
				$query->where('IF(' . $db->qn('diff') . ' != "",0,1) = ' . $db->q(1));
				break;

			case 'suspicious':
				$query->where('IF(' . $db->qn('diff') . ' LIKE "###SUSPICIOUS FILE###%",1,0)  = ' . $db->q(1));
				break;

			case 'modified':
				$query->where('IF(' . $db->qn('diff') . ' != "",0,1) = ' . $db->q(0));
				$query->where('IF(' . $db->qn('diff') . ' LIKE "###SUSPICIOUS FILE###%",1,0)  = ' . $db->q(0));
				break;
		}

		$safe = $this->getState('safe', '');

		if (is_numeric($safe) && ($safe != '-1'))
		{
			$query->where($db->qn('acknowledged') . ' = ' . $db->q($safe));
		}

		if (!$overrideLimits)
		{
			$order = $this->getState('filter_order', null, 'cmd');
			$dir   = $this->getState('filter_order_Dir', 'ASC', 'cmd');

			if (!in_array($order, array('path', 'threat_score', 'acknowledged', 'filestatus', 'newfile', 'suspcious')))
			{
				$order = 'threat_score';
				$dir   = 'DESC';
			}

			$query->order($db->qn($order) . ' ' . $dir);
		}

		return $query;
	}

	protected function onBeforeSave(&$data)
	{
		// Let's remove all the fields created by the `AS xxx` SQL syntax
		$fakeFields = array('newfile', 'suspicious', 'filestatus');

		foreach ($fakeFields as $field)
		{
			// I can't use `isset` since if we have a null key the check will return false, but that
			// would cause an error during the update
			if (array_key_exists($field, $this->recordData))
			{
				unset($this->recordData[ $field ]);
			}
		}
	}

	public function getFileSourceForDisplay($highlight = false)
	{
		$filedata = @file_get_contents(JPATH_ROOT . '/' . $this->path);

		if (!$highlight)
		{
			return htmlentities($filedata);
		}

		$highlightPrefixSuspicious = "%*!*[[###  ";
		$highlightSuffixSuspicious = "  ###]]*!*%";
		$highlightPrefixKnownHack = "%*{{!}}*[[###  ";
		$highlightSuffixKnownHack = "  ###]]*{{!}}*%";

		/** @var string $encodedConfig Defined in the included file */
		require_once $this->container->backEndPath . '/platform/encodedconfig.php';

		$zipped = pack('H*', $encodedConfig);
		unset($encodedConfig);

		$json_encoded = gzinflate($zipped);
		unset($zipped);

		$new_list = json_decode($json_encoded, true);
		extract($new_list);

		unset($new_list);

		/** @var array $suspiciousWords  Simple array of words that are suspicious */
		/** @var array $knownHackSignatures  Known hack signatures, $signature => $weight */
		/** @var array $suspiciousRegEx  Suspicious constructs' RegEx, $regex => $weight */


		foreach ($suspiciousWords as $word)
		{
			$replacement = $highlightPrefixSuspicious . $word . $highlightSuffixSuspicious;
			$filedata    = str_replace($word, $replacement, $filedata);
		}

		foreach ($knownHackSignatures as $signature => $sigscore)
		{
			$replacement = $highlightPrefixKnownHack . $signature . $highlightSuffixKnownHack;
			$filedata    = str_replace($signature, $replacement, $filedata);
		}

		foreach ($suspiciousRegEx as $pattern => $value)
		{
			$filedata = preg_replace_callback($pattern, function($m) use ($highlightPrefixSuspicious, $highlightSuffixSuspicious) {
				return $highlightPrefixSuspicious . $m[0] . $highlightSuffixSuspicious;
			}, $filedata);
		}

		$filedata = htmlentities($filedata);

		$filedata = str_replace([
			$highlightPrefixSuspicious,
			$highlightSuffixSuspicious
		], [
			'<span style="background: yellow; font-weight: bold; color: red; padding: 2px 4px">',
			'</span>'
		], $filedata);

		$filedata = str_replace([
			$highlightPrefixKnownHack,
			$highlightSuffixKnownHack
		], [
			'<span style="background: red; font-weight: bold; color: white; padding: 2px 4px">',
			'</span>'
		], $filedata);

		return $filedata;
	}
}