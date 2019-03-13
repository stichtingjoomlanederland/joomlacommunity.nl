<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Container\Container;
use FOF30\Input\Input;

defined('_JEXEC') or die;

class AtsystemFeatureWafblacklist extends AtsystemFeatureAbstract
{
	protected $loadOrder = 25;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return true;
	}

	/**
	 * Filters visitor access using WAF blacklist rules
	 */
	public function onAfterRoute()
	{
		$db = $this->db;

		$method    = array($db->q(''), $db->q(strtoupper($_SERVER['REQUEST_METHOD'])));
		$option    = array($db->q(''));
		$view      = array($db->q(''));
		$task      = array($db->q(''));
		$rawView   = $this->input->getCmd('view', '');
		$rawTask   = $this->input->getCmd('task', '');
		$rawOption = $this->input->getCmd('option', '');

		if ($rawOption)
		{
			$option[] = $db->q($rawOption);
		}

		if ($rawView)
		{
			$view[] = $db->q($rawView);
		}

		if ($rawTask)
		{
			$task[] = $db->q($rawTask);
		}

		// Parse task=viewName.taskName
		if (empty($rawView) && (strpos($rawTask, '.') !== false))
		{
			list($viewExplode, $taskExplode) = explode('.', $rawTask, 2);
			$view[] = $db->q($viewExplode);
			$task[] = $db->q($taskExplode);
		}
		/**
		 * If we have separate view=viewName and task=taskName variables look for a rule where task=viewName.taskName in
		 * case the user ignored the documentation or tried to be "clever".
		 */
		elseif (strpos($rawTask, '.') === false)
		{
			$task[] = $db->q("$rawView.$rawTask");
		}

		// Let's get the rules for the current input values or the empty ones
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__admintools_wafblacklists'))
			->where($db->qn('verb') . ' IN(' . implode(',', $method) . ')')
			->where($db->qn('option') . ' IN(' . implode(',', $option) . ')')
			->where($db->qn('view') . ' IN(' . implode(',', $view) . ')')
			->where($db->qn('task') . ' IN(' . implode(',', $task) . ')')
			->where($db->qn('enabled') . ' = ' . $db->q(1))
			->group($db->qn('query'))
			->order($db->qn('query') . ' ASC');

		try
		{
			$rules = $db->setQuery($query)->loadObjectList();
		}
		catch (Exception $e)
		{
			return;
		}

		if (!$rules)
		{
			return;
		}

		// We need FOF 3 loaded for this feature to work
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			// FOF 3.0 is not installed
			return;
		}

		// Let me see if I am a backend application
		$isBackend = Container::getInstance('com_admintools')->platform->isBackend();

		// I can't use JInput since it will fetch data from cookies, too.
		$inputSources = array('get', 'post');

		// Ok, let's analyze all the matching rules
		$block = false;

		foreach ($rules as $rule)
		{
			if (!isset($rule->application))
			{
				$rule->application = '';
			}

			// Am I in the correct side of the application?
			// Please note: continue and break have the same meaning inside a switch statement, so we have to
			// continue 2 to break the current switch AND move on the next item of the array
			switch ($rule->application)
			{
				case 'site':
					if ($isBackend)
					{
						continue 2;
					}

					break;

				case 'admin':
					if (!$isBackend)
					{
						continue 2;
					}

					break;
			}

			/**
			 * Make sure the view/task matches.
			 *
			 * This is a bit complicated since we have to take into account that EITHER OF the request AND the rule may
			 * be using the task=viewName.taskName notation. Moreover, empty views and tasks in rules act as wildcards.
			 */
			$view = isset($viewExplode) ? $viewExplode : $rawView;
			$task = isset($taskExplode) ? $taskExplode : $rawTask;
			$hasMatch = false;
			// -- Empty view and task: rule applies to entire component
			$hasMatch = $hasMatch || (($rule->view == '') && ($rule->task == ''));
			// -- Request view matches rule view AND rule task is either empty or matches request task
			$hasMatch = $hasMatch || ((!empty($view) && ($rule->view == $view)) && (empty($rule->task) || ($rule->task == $task)));
			// -- Request task matches rule task AND view task is either empty or matches request view
			$hasMatch = $hasMatch || ((!empty($task) && ($rule->task == $task)) && (empty($rule->view) || ($rule->view == $view)));
			// -- Both view and task matched by the rule's task AND the rule's view is empty
			$hasMatch = $hasMatch || ((!empty($task) && !empty($view) && ($rule->task == "$view.$task")) && empty($rule->view));

			if (!$hasMatch)
			{
				continue;
			}

			// Empty query => block everything for this VERB/OPTION/VIEW/TASK combination
			if (!$rule->query)
			{
				$block = true;
				break;
			}

			foreach ($inputSources as $inputSource)
			{
				$inputObject = new Input($inputSource);

				foreach ($inputObject->getData() as $key => $value)
				{
					if ($this->isBlockedByRule($rule, $key, $value))
					{
						$block = true;

						break 3;
					}
				}
			}
		}

		if ($block)
		{
			$extraInfo = '';

			// If the rule matched any variable, let's print the variables that caused the block, so we can inspect later
			if (isset($inputSource) && isset($inputObject))
			{
				// PLEASE NOTE! If POST data is passed, but the GET array is empty, Input will use the whole $_REQUEST
				// array, so $inputSource will be GET even if we truly had a POST request. However this is an edge case
				$extraInfo = "Hash      : " . strtoupper($inputSource) . "\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($inputObject->getData(), true);
				$extraInfo .= "\n";
			}

			$this->exceptionsHandler->blockRequest('wafblacklist', null, $extraInfo);
		}
	}

	private function isBlockedByRule($rule, $key, $value, $prefix = '')
	{
		// Handle array values
		if (is_array($value))
		{
			foreach ($value as $subKey => $subValue)
			{
				// Default: assume no prefix was set, in which case the key is the new prefix (array name).
				$newPrefix = $key;

				// If a prefix was set then we have a sub-subkey. The prefix should be prefix[key] instead
				if ($prefix)
				{
					$newPrefix = $prefix . '[' . $key . ']';
				}

				if ($this->isBlockedByRule($rule, $subKey, $subValue, $newPrefix))
				{
					return true;
				}
			}

			return false;
		}

		if ($prefix)
		{
			$key = $prefix . '[' . $key . ']';
		}

		$ruleQuery = $rule->query;

		$found = false;

		// Partial match

		if ($rule->query_type == 'P')
		{
			if (stripos($key, $ruleQuery) !== false)
			{
				$found = true;
			}
		}
		// RegEx match
		elseif ($rule->query_type == 'R')
		{
			$regex  = $ruleQuery;
			$negate = false;

			if (substr($regex, 0, 1) == '!')
			{
				$negate = true;
				$regex  = substr($regex, 1);
			}

			$found = @preg_match($regex, $key) > 0;

			if ($negate)
			{
				$found = !$found;
			}
		}
		// Exact match, empty $ruleQuery
		elseif ($ruleQuery === '')
		{
			$found = true;
		}
		// Exact match, non-empty $ruleQuery
		else
		{
			// Cannot match empty key
			if (empty($key))
			{
				return false;
			}

			if ($key == $ruleQuery)
			{
				$found = true;
			}
		}

		if (!$found)
		{
			return false;
		}

		// Ok, the query parameter is set, do I have any specific rule about the content?
		if ($found)
		{
			// Empty => always block, no matter what
			if (!$rule->query_content)
			{
				return true;
			}

			// I have to run a regex on the value
			$negate = false;
			$regex  = $rule->query_content;

			if (substr($regex, 0, 1) == '!')
			{
				$negate = true;
				$regex  = substr($regex, 1);
			}

			$isFiltered = @preg_match($regex, $value) >= 1;

			if ($negate)
			{
				$isFiltered = !$isFiltered;
			}

			if ($isFiltered)
			{
				return true;
			}
		}

		return false;
	}
}
