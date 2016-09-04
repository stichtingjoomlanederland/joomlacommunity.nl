<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.5.0.585
 * @date        2016-08-25
 */

// no direct access
defined('_JEXEC') or die;

class WbampModel_Ruler
{
	const RULES_COUNT = 2;

	private $_request      = null;
	private $_manager      = null;
	private $_params       = null;
	private $_joomlaConfig = null;

	/**
	 * Stores request data
	 *
	 * @param $request the GET query vars from the request
	 * @param $manager the Manager object which handled the request
	 * @param $params User set params
	 * @param $joomlaConfig Global Joomla config
	 */
	public function __construct($request, $manager, $params, $joomlaConfig)
	{
		$this->_request = $request;
		$this->_manager = $manager;
		$this->_params = $params;
		$this->_joomlaConfig = $joomlaConfig;
	}

	/**
	 * Runs in sequence various checks based on URL query vars
	 * to allow or not creation of AMP version of pages
	 *
	 * returns true if passed, false if not passed and null if neutral
	 */
	public function checkRules()
	{
		$passUserRules = $this->checkComContentRules() === true || $this->checkComponentsRules() === true;

		return $passUserRules;
	}

	/**
	 * Check a rule for com_content
	 *
	 * returns true if passed, false if not passed and null if neutral
	 *
	 * @return bool|null
	 */
	public function checkComContentRules()
	{
		$pass = null;
		$component = $this->_request->getCmd('option');
		if (empty($component))
		{
			return $pass;
		}

		if ($component != 'com_content')
		{
			// rule is not applicable
			return null;
		}

		$ruleName = '_com_content_';

		if (!($this->checkRule('Itemid', $this->_params->get($ruleName . 'itemid'))))
		{
			// one applicable rule failed, global fail
			return false;
		}
		else if (!($this->checkRule('view', $this->_params->get($ruleName . 'view'))))
		{
			// one applicable rule failed, global fail
			return false;
		}
		else if (!($this->checkRule('task', $this->_params->get($ruleName . 'task'))))
		{
			// one applicable rule failed, global fail
			return false;
		}
		else if (!($this->checkComContentCategoryRule($this->_params->get('_com_content_categories'))))
		{
			// one applicable rule failed, global fail
			return false;
		}
		else if (!($this->checkRule('id', $this->_params->get('_com_content_item_id'))))
		{
			// one applicable rule failed, global fail
			return false;
		}
		else
		{
			// applicable rule passed
			$pass = true;
		}

		return $pass;
	}

	/**
	 * Check a category rule for com_content, based on catid query var.
	 * Relies on default checkRule method, but compute the catid if none
	 * is supllied in the query, based on the actual article id
	 *
	 * @param $rule
	 */
	protected function checkComContentCategoryRule($allowedValuesList)
	{
		if (empty($allowedValuesList))
		{
			// no category specified, disallow AMP
			return false;
		}

		if(in_array('__all_com_content_categories__',$allowedValuesList))
		{
			return true;
		}

		// use query catid if we have one
		$catid = $this->_request->getInt('catid');
		if (empty($catid))
		{
			// no catid, let's try find one from the article id
			$id = $this->_request->getInt('id');
			try
			{
				$catid = ShlDbHelper::selectResult('#__content', 'catid', array('id' => $id));
			}
			catch (Exception $e)
			{
				ShlSystem_Log::error('wbamp', __METHOD__ . ' ' . $e->getMessage());
			}
		}

		if (!empty($catid))
		{
			return in_array($catid, $allowedValuesList);
		}

		return false;
	}

	/**
	 * Execute user-set rules to decide whether an HTML
	 * page should also have an AMP version.
	 *
	 * returns true if passed, false if not passed and null if neutral
	 *
	 * @return bool|null
	 */
	public function checkComponentsRules()
	{
		$pass = null;
		$component = $this->_request->getCmd('option');
		if (empty($component))
		{
			return $pass;
		}

		// iterate over rules
		for ($ruleNb = 1; $ruleNb <= self::RULES_COUNT; $ruleNb++)
		{
			$ruleName = '_' . $ruleNb . '_';
			$ruleComponent = $this->_params->get($ruleName . 'component');
			// view
			if ($ruleComponent != $component)
			{
				// non applicable rule, continue to next rule
				continue;
			}
			else if (!($this->checkRule('Itemid', $this->_params->get($ruleName . 'itemid'))))
			{
				// one applicable rule failed, global fail
				return false;
			}
			else if (!($this->checkRule('view', $this->_params->get($ruleName . 'view'))))
			{
				// one applicable rule failed, global fail
				return false;
			}
			else if (!($this->checkRule('task', $this->_params->get($ruleName . 'task'))))
			{
				// one applicable rule failed, global fail
				return false;
			}
			else if (!($this->checkCategoryRule($ruleName, $this->_params->get($ruleName . 'category_name'), $this->_params->get($ruleName . 'category_values'))))
			{
				// one applicable rule failed, global fail
				return false;
			}
			else if (!($this->checkRule($this->_params->get($ruleName . 'item_name'), $this->_params->get($ruleName . 'item_values'))))
			{
				// one applicable rule failed, global fail
				return false;
			}
			else
			{
				// applicable rule passed
				$pass = true;
			}
		}

		return $pass;
	}

	/**
	 * Checks that a query variable matches a list of values
	 * Values are passed as a comma-separated list
	 * with the following convention:
	 * * means any value is accepted (but the variable must exist)
	 * empty = no value is accepted, the variable must be undefined or empty
	 *
	 * @param $varName
	 * @param $varAllowedValuesList
	 */
	private function checkRule($varName, $varAllowedValuesList)
	{
		if (empty($varName))
		{
			return true;
		}

		// clean up
		$varAllowedValuesList = JString::trim($varAllowedValuesList);
		$varAllowedValuesList = str_replace(' ', '', $varAllowedValuesList);
		$varValue = $this->_request->getString($varName);

		return $this->executeCheckRule($varValue, $varAllowedValuesList);
	}

	/**
	 * Checks that a query variable matches a list of values
	 * Values are passed as a comma-separated list
	 * with the following convention:
	 * * means any value is accepted (but the variable must exist)
	 * empty = no value is accepted, the variable must be undefined or empty
	 *
	 * @param $varName
	 * @param $varAllowedValuesList
	 */
	private function checkCategoryRule($ruleName, $varName, $varAllowedValuesList)
	{
		if (empty($varName))
		{
			return true;
		}

		// clean up
		$varAllowedValuesList = JString::trim($varAllowedValuesList);
		$varAllowedValuesList = str_replace(' ', '', $varAllowedValuesList);

		// now figure out value
		$varValue = $this->_request->getString($varName);
		if (is_null($varValue))
		{
			// the desired category var was not in the request
			// maybe it can be infered from the item id
			$itemVarName = $this->_params->get($ruleName . 'item_name');
			if (!empty($itemVarName))
			{
				$itemId = $this->_request->getString($itemVarName);
				if (!is_null($itemId))
				{
					// we have an item id, find the corresponding category
					$status = JPluginHelper::importPlugin('wbamp');
					if ($status)
					{
						$option = $this->_request->getCmd('option', '');
						$eventArgs = array(
							$option,
							$itemId,
							& $varValue,
							$this->_request
						);
						ShlSystem_Factory::dispatcher()
						                 ->trigger('onWbampGetCategoryFromItem', $eventArgs);
					}
					else
					{
						throw new Exception('Unable to load wbAMP components support plugins.');
					}

				}
			}
		}

		return $this->executeCheckRule($varValue, $varAllowedValuesList);
	}

	/**
	 * Checks that a specific value is within a specified range
	 *
	 * @param $varValue
	 * @param $varAllowedValuesList
	 * @return bool
	 */
	private function executeCheckRule($varValue, $varAllowedValuesList)
	{
		// clean up
		$varAllowedValuesList = JString::trim($varAllowedValuesList);
		$varAllowedValuesList = str_replace(' ', '', $varAllowedValuesList);

		// if allowed values is empty, query var must be empty
		if (empty($varAllowedValuesList) && !empty($varValue))
		{
			return false;
		}
		else if (empty($varAllowedValuesList))
		{
			return true;
		}

		// if allowed values is a dash, query var can be empty or have any value
		if ($varAllowedValuesList == '-')
		{
			return true;
		}

		// we have some values to check: first case: any value is allowed
		if ($varAllowedValuesList == '*' && !empty($varValue))
		{
			// the query var is not empty, this is allowed
			return true;
		}
		else if ($varAllowedValuesList == '*')
		{
			// any value is allowed, but we the query var is empty
			return false;
		}

		// last case: a specific list of values are allowed
		$allowedValues = explode(',', $varAllowedValuesList);
		$ok = in_array($varValue, $allowedValues);

		return $ok;
	}
}
