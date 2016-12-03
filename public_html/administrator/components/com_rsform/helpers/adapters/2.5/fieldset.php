<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFieldset {
	public function startFieldset($legend='', $class='adminform') {
		?>
		<fieldset class="<?php echo $class; ?>">
			<?php if ($legend) { ?>
			<legend><?php echo $legend; ?></legend>
			<?php } ?>
			<ul class="config-option-list">
		<?php
	}
	
	public function showField($label, $input, $attribs=array()) {
		$class 	= '';
		$id 	= '';
		
		if (isset($attribs['class'])) {
			$class = ' class="'.$this->escape($attribs['class']).'"';
		}
		if (isset($attribs['id'])) {
			$id = ' id="'.$this->escape($attribs['id']).'"';
		}
		
		?>
		<li<?php echo $class; echo $id; ?>>
			<?php echo $label; ?>
			<?php echo $input; ?>
		</li>
		<?php
	}
	
	public function endFieldset() {
		?>
			</ul>
		</fieldset>
		<div class="clr"></div>
		<?php
	}
	
	protected function escape($text) {
		return htmlentities($text, ENT_COMPAT, 'utf-8');
	}
}