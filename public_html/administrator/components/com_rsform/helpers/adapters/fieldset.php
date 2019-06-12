<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFieldset {
	public function startFieldset($legend = '', $class = 'adminform form-horizontal', $display = true) {
        $return = '<fieldset class="' . $class . '">';
        if ($legend) {
            $return .= '<h3 class="rsfp-legend">' . $legend . '</h3>';
        }

        if ($display) {
            echo $return;
        } else {
            return $return;
        }
	}
	
	public function showField($label, $input, $attribs = array(), $display = true) {
		$class 	= '';
		$id 	= '';
		
		if (isset($attribs['class'])) {
			$class = ' '.$this->escape($attribs['class']);
		}
		if (isset($attribs['id'])) {
			$id = ' id="'.$this->escape($attribs['id']).'"';
		}

        $return = '<div class="control-group' . $class . '"' . $id . '>';

        if ($label) {
            $return .= '<div class="control-label">' . $label . '</div>';
        }
        $return .= '<div' . ( $label ? ' class="controls"' : '' ) . '>' . $input . '</div>';
        $return .= '</div>';

        if ($display) {
            echo $return;
        } else {
            return $return;
        }
	}
	
	public function endFieldset($display = true) {
        $return = '</fieldset>';

        if ($display) {
            echo $return;
        } else {
            return $return;
        }
	}
	
	protected function escape($text) {
		return htmlentities($text, ENT_COMPAT, 'utf-8');
	}
}