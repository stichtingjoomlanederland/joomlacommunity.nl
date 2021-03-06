<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') || die;

use FOF40\Container\Container;
use FOF40\Model\DataModel;
use Joomla\CMS\Language\Text;

/**
 * @property   int    admintools_scanalert_id
 * @property   string path
 * @property   int    scan_id
 * @property   string diff
 * @property   string threat_score
 *
 * @property   int    newfile
 * @property   int    suspicious
 * @property   int    acknowledged
 *
 * @method  $this   scan_id() scan_id(int $v)
 * @method  $this   acknowledged() acknowledged(bool $v)
 * @method  $this   status() status(string $v)
 * @method  $this   search() search(string $v)
 */
class ScanAlerts extends DataModel
{
	/**
	 * Size threshold for reading file contents. To calculate the score we have to read the whole file, with large ones
	 * (ie log files) we could run out of memory, causing a fatal error.
	 *
	 * @var int
	 */
	private $filesizeThreshold = 5242880;

	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_scanalerts';
		$config['idFieldName'] = 'admintools_scanalert_id';
		$config['aliasFields'] = ['enabled' => 'acknowledged'];
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
			->select([
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
			]);

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

			if (!in_array($order, ['path', 'threat_score', 'acknowledged', 'filestatus', 'newfile', 'suspcious']))
			{
				$order = 'threat_score';
				$dir   = 'DESC';
			}

			$query->order($db->qn($order) . ' ' . $dir);
		}

		return $query;
	}

	public function getFileSourceForDisplay($highlight = false)
	{
		if (!file_exists(JPATH_ROOT . '/' . $this->path))
		{
			return null;
		}

		$filepath = JPATH_ROOT . '/' . $this->path;
		$filesize = @filesize($filepath);

		// With very large files do not display the whole contents, but instead show a placeholder
		if ($filesize > $this->filesizeThreshold)
		{
			return Text::sprintf('COM_ADMINTOOLS_SCANS_FILE_TOO_LARGE', round($filesize / 1024 / 1024, 2));
		}

		$filedata = @file_get_contents($filepath);

		if (!$highlight)
		{
			return htmlentities($filedata);
		}

		$highlightPrefixSuspicious = "%*!*[[###  ";
		$highlightSuffixSuspicious = "  ###]]*!*%";
		$highlightPrefixKnownHack  = "%*{{!}}*[[###  ";
		$highlightSuffixKnownHack  = "  ###]]*{{!}}*%";

		/** @var string $encodedConfig Defined in the included file */
		require_once $this->container->backEndPath . '/Model/Scanner/encodedconfig.php';

		$zipped = pack('H*', $encodedConfig);
		unset($encodedConfig);

		$json_encoded = gzinflate($zipped);
		unset($zipped);

		$new_list = json_decode($json_encoded, true);
		extract($new_list);

		unset($new_list);

		/** @var array $suspiciousWords Simple array of words that are suspicious */
		/** @var array $knownHackSignatures Known hack signatures, $signature => $weight */
		/** @var array $suspiciousRegEx Suspicious constructs' RegEx, $regex => $weight */


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

		$i = 0;

		foreach ($suspiciousRegEx as $pattern => $value)
		{
			$i++;
			$count = preg_match_all($pattern, $filedata, $matches);

			if (!$count)
			{
				continue;
			}

			$filedata = preg_replace_callback($pattern, function ($m) use ($highlightPrefixSuspicious, $highlightSuffixSuspicious, $i) {
				return $highlightPrefixSuspicious . $m[0] . $highlightSuffixSuspicious;
				// DEBUG
				// return $highlightPrefixSuspicious . "[[[ $i ]]]" . $m[0] . $highlightSuffixSuspicious;
			}, $filedata);
		}

		$filedata = htmlentities($filedata);

		$filedata = str_replace([
			$highlightPrefixSuspicious,
			$highlightSuffixSuspicious,
		], [
			'<mark class="adminToolsSuspicious">',
			'</mark>',
//			'<span style="background: yellow; font-weight: bold; color: red; padding: 2px 4px">',
//			'</span>',
		], $filedata);

		$filedata = str_replace([
			$highlightPrefixKnownHack,
			$highlightSuffixKnownHack,
		], [
			'<mark class="adminToolsKnownHack">',
			'</mark>',
//			'<span style="background: red; font-weight: bold; color: white; padding: 2px 4px">',
//			'</span>',
		], $filedata);

		return $filedata;
	}

	/**
	 * Mark all entries of the specified scan as safe.
	 *
	 * @param   int  $scan_id  The ID of the scan
	 *
	 * @since   5.2.1
	 */
	public function markAllSafe($scan_id)
	{
		$scan_id = max(0, (int) $scan_id);

		if ($scan_id == 0)
		{
			return;
		}

		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->update($db->qn($this->tableName))
			->set([
				$db->qn('acknowledged') . ' = ' . $db->q(1),
			])
			->where($db->qn('scan_id') . ' = ' . $db->q($scan_id))
			->where($db->qn('threat_score') . ' > ' . $db->q(0));
		$db->setQuery($query)->execute();
	}

	protected function onBeforeSave(&$data)
	{
		// Let's remove all the fields created by the `AS xxx` SQL syntax
		$fakeFields = ['newfile', 'suspicious', 'filestatus'];

		foreach ($fakeFields as $field)
		{
			// I can't use `isset` since if we have a null key the check will return false, but that
			// would cause an error during the update
			if (array_key_exists($field, $this->recordData))
			{
				unset($this->recordData[$field]);
			}
		}
	}
}
