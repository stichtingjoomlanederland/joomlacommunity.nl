<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

class RSFormProScripting
{
	public static function compile(&$subject, $replace, $with) {
		$placeholders = array_combine($replace, $with);
		$formId = isset($placeholders['{global:formid}']) ? $placeholders['{global:formid}'] : null;
		
		$condition 	= '{[a-z0-9\_\- ]+:[a-z_]+}';
		$inner 		= '((?:(?!{/?if).)*?)';
		$pattern    = '#{if\s?('.$condition.')\s?(<=|&lt;=|>=|&gt;=|<-|&lt;-|->|-&gt;|<>|&lt;&gt;|<|&lt;|>|&gt;|!=|===|==|=)?\s?'.$inner.'?\s?}'.$inner.'{/if}#is';
		
		while (preg_match($pattern, $subject, $match)) {
			$placeholder = trim($match[1]);
			$operand	 = trim($match[2]);
			$compare	 = trim($match[3], '\'" ');
			$content 	 = $match[4];
			$value		 = !isset($placeholders[$placeholder]) ? '' : $placeholders[$placeholder];
			
			switch ($operand) {
				default:
					$result = $value;
				break;
				
				case '<=':
				case '&lt;=':
					$result = $value <= $compare;
				break;
				
				case '>=':
				case '&gt;=':
					$result = $value >= $compare;
				break;
				
				case '<>':
				case '&lt;&gt;':
					$result = $value <> $compare;
				break;
				
				case '<':
				case '&lt;':
					$result = $value < $compare;
				break;
				
				case '>':
				case '&gt;':
					$result = $value > $compare;
				break;
				
				case '!=':
					$result = $value != $compare;
				break;
				
				case '=':
				case '==':
					$result = $value == $compare;
				break;
				
				case '===':
					$result = $value === $compare;
				break;
				
				case '<-':
				case '&lt;-':
					$result = self::inclusion($value, $compare, $formId, 'right');
				break;
				
				case '->':
				case '-&gt;':
					$result = self::inclusion($value, $compare, $formId, 'right');
				break;
			}
			// if empty value remove whole line
			// else show line but remove pseudo-code
			$subject = preg_replace($pattern,
									$result ? addcslashes($content, '$') : '',
									$subject,
									1);
		}
	}
	
	public static function inclusion($value, $compare, $formId, $direction) {
		// let's make sure we have the formId defined
		if (is_null($formId)) return false;
		
		$form = RSFormProHelper::getForm($formId);
		$separator = str_replace(array('\n', '\r', '\t'), array("\n", "\r", "\t"), $form->MultipleSeparator);
		
		$values = explode($separator, $value);
		$compares = explode(',', $compare);
		
		$intersect = array_intersect($values, $compares);
		
		return ($direction == 'left' ? count($intersect) == count($compares) : count($intersect) == count($values));
	}
}