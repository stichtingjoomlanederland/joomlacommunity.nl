<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/fields/hidden.php';

class RSFormProFieldTicket extends RSFormProFieldHidden
{
	// backend preview
	public function getPreviewInput()
	{
		return RSFormProHelper::getIcon('support') . $this->generateString();
	}
	
	// @desc Overridden here because this field generates a value based on its settings
	public function getValue()
	{
		return $this->generateString();
	}

	protected function generateString()
	{
		$type		= $this->getProperty('TICKETTYPE','RANDOM');
		$key		= '';

		if ($type == 'RANDOM')
		{
			$length 	= (int) $this->getProperty('LENGTH', 8);
			$characters = $this->getProperty('CHARACTERS', 'ALPHANUMERIC');
			switch ($characters)
			{
				case 'ALPHANUMERIC':
				default:
					$possible = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case 'ALPHA':
					$possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
					break;
				case 'NUMERIC':
					$possible = '0123456789';
					break;
			}

			if ($length < 1 || $length > 255) {
				$length = 8;
			}

			$key = '';
			$i = 0;
			while ($i < $length) {
				$key .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
				$i++;
			}
		} else if ($type == 'SEQUENTIAL') {
			$leadingLength = (int) $this->getProperty('LEADINGZEROLENGTH', '0');
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			// Catch only numeric values, this scenario happens when the ticket type was previously 'Random Type'
			$query
				->select("MAX(CAST(".$db->qn('FieldValue')." AS SIGNED))")
				->from('#__rsform_submission_values')
				->where($db->qn('FormId') . ' = ' . $db->q($this->formId))
				->where($db->qn('FieldName') . ' = ' . $db->q($this->name))
				->where($db->qn('FieldValue') . " REGEXP '^[0-9]+$'");
			$db->setQuery($query);
			$key = (int) $db->loadResult();
			$key++;
			if ($leadingLength)
			{
				$key = str_pad($key, ($leadingLength+1), '0', STR_PAD_LEFT);
			}
		}

		return $key;
	}

	protected function stringExists($string) {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('COUNT(*)')
			->from('#__rsform_submission_values')
			->where($db->qn('FormId') . ' = ' . $db->q($this->formId))
			->where($db->qn('FieldName') . ' = ' . $db->q($this->name))
			->where($db->qn('FieldValue') . ' = ' . $db->q($string));
		$db->setQuery($query);

		return $db->loadResult();
	}

	// process the field value after validation
	public function processBeforeStore($submissionId, &$post, &$files) {
		if (!isset($post[$this->name]))
		{
			return;
		}

		$value = $post[$this->name];
		while ($this->stringExists($value)) {
			$value = $this->generateString();
		}

		$post[$this->name] = $value;
	}
}