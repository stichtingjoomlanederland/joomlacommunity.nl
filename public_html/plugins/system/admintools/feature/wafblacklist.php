<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

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

		$method = array($db->q(''), $db->q(strtoupper($_SERVER['REQUEST_METHOD'])));
		$option = array($db->q(''));
		$view   = array($db->q(''));
		$task   = array($db->q(''));

		if ($this->input->getCmd('option', ''))
		{
			$option[] = $db->q($this->input->getCmd('option', ''));
		}

		if ($this->input->getCmd('view', ''))
		{
			$view[] = $db->q($this->input->getCmd('view', ''));
		}

		if ($this->input->getCmd('task', ''))
		{
			$task[] = $db->q($this->input->getCmd('task', ''));
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
		            ->order($db->qn('query') . ' ASC');;

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

		// I can't use JInput since it will fetch data from cookies, too.
		$inputSources = array('get', 'post');

		// Ok, let's analyze all the matching rules
		$block = false;

		foreach ($rules as $rule)
		{
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
			$this->exceptionsHandler->blockRequest('wafblacklist');
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
		// Exact match
		else
		{
			if ($key == $ruleQuery)
			{
				$found = true;
			}
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