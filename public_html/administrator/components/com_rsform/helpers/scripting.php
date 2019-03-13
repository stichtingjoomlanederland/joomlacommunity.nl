<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class RSFormProScripting
{
	public static function compile(&$subject, $replace, $with) {
		$placeholders = array_combine($replace, $with);
		$formId = isset($placeholders['{global:formid}']) ? $placeholders['{global:formid}'] : null;
		
		$condition 	= '({[a-z0-9\_\- ]+:[a-z_]+})';
		$inner 		= '((?:(?!{/?if).)*?)';
		
		// {Placeholder1:value} > {Placeholder2:value}
		$pattern = '#{if\s?'.$condition.'\s?(<=|&lt;=|>=|&gt;=|<-|&lt;-|->|-&gt;|<>|&lt;&gt;|<|&lt;|>|&gt;|!=|===|==|=)?\s?'.$condition.'?\s?}'.$inner.'{/if}#is';
		while (preg_match($pattern, $subject, $match)) {
			$placeholder = trim($match[1]);
			$operand	 = htmlspecialchars_decode(trim($match[2]));
			$compare 	 = trim($match[3]);
			$content 	 = $match[4];
			$value		 = !isset($placeholders[$placeholder]) ? '' : $placeholders[$placeholder];
			$compare	 = !isset($placeholders[$compare]) ? '' : $placeholders[$compare];
			
			$result = static::doComparison($value, $compare, $operand, $formId);
			
			// if empty value remove whole line
			// else show line but remove pseudo-code
			$subject = preg_replace($pattern,
									$result ? addcslashes($content, '$') : '',
									$subject,
									1);
		}
		
		// {Placeholder1:value} > 1
		$pattern = '#{if\s?'.$condition.'\s?(<=|&lt;=|>=|&gt;=|<-|&lt;-|->|-&gt;|<>|&lt;&gt;|<|&lt;|>|&gt;|!=|===|==|=)?\s?'.$inner.'?\s?}'.$inner.'{/if}#is';
		while (preg_match($pattern, $subject, $match)) {
			$placeholder = trim($match[1]);
			$operand	 = htmlspecialchars_decode(trim($match[2]));
			$compare	 = trim($match[3], '\'" ');
			$content 	 = $match[4];
			$value		 = !isset($placeholders[$placeholder]) ? '' : $placeholders[$placeholder];
			
			$result = static::doComparison($value, $compare, $operand, $formId);
			
			// if empty value remove whole line
			// else show line but remove pseudo-code
			$subject = preg_replace($pattern,
									$result ? addcslashes($content, '$') : '',
									$subject,
									1);
		}
		
		// 1 < {Placeholder1:value}
		$pattern = '#{if\s?'.$inner.'\s?(<=|&lt;=|>=|&gt;=|<-|&lt;-|->|-&gt;|<>|&lt;&gt;|<|&lt;|>|&gt;|!=|===|==|=)?\s?'.$condition.'?\s?}'.$inner.'{/if}#is';
		while (preg_match($pattern, $subject, $match)) {
			$placeholder = trim($match[1]);
			$operand	 = htmlspecialchars_decode(trim($match[2]));
			$compare	 = trim($match[3], '\'" ');
			$content 	 = $match[4];
			$value		 = !isset($placeholders[$placeholder]) ? '' : $placeholders[$placeholder];
			
			$result = static::doComparison($value, $compare, $operand, $formId);
			
			// if empty value remove whole line
			// else show line but remove pseudo-code
			$subject = preg_replace($pattern,
									$result ? addcslashes($content, '$') : '',
									$subject,
									1);
		}
	}
	
	protected static function doComparison($value, $compare, $operand, $formId)
	{
		switch ($operand)
		{
			default:
				$result = $value;
			break;
			
			case '<=':
				$result = $value <= $compare;
			break;
			
			case '>=':
				$result = $value >= $compare;
			break;
			
			case '<>':
				$result = $value <> $compare;
			break;
			
			case '<':
				$result = $value < $compare;
			break;
			
			case '>':
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
				$result = self::inclusion($value, $compare, $formId, 'left');
			break;
			
			case '->':
				$result = self::inclusion($value, $compare, $formId, 'right');
			break;
		}
		
		return $result;
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