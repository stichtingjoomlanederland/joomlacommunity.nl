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
		
		$condition 	= '{[a-z0-9\_\- ]+:[a-z_]+}';
		$inner 		= '((?:(?!{/?if).)*?)';
		$pattern 	= '#{if\s?('.$condition.')\s?(<=|>=|<>|<|>|!=|===|==|=)?\s?'.$inner.'?\s?}'.$inner.'{/if}#is';
		
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
			}
			
			// if empty value remove whole line
			// else show line but remove pseudo-code
			$subject = preg_replace($pattern,
									$result ? addcslashes($content, '$') : '',
									$subject,
									1);
		}
	}
}