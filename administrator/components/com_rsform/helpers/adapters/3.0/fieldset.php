<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFieldset {
	public function startFieldset($legend='', $class='adminform form-horizontal') {
		?>
		<fieldset class="<?php echo $class; ?>">
		<?php if ($legend) { ?>
		<legend><?php echo $legend; ?></legend>
		<?php }
	}
	
	public function showField($label, $input, $attribs=array()) {
		$class 	= '';
		$id 	= '';
		
		if (isset($attribs['class'])) {
			$class = ' '.$this->escape($attribs['class']);
		}
		if (isset($attribs['id'])) {
			$id = ' id="'.$this->escape($attribs['id']).'"';
		}
		?>
		<div class="control-group<?php echo $class; ?>"<?php echo $id; ?>>
			<?php if ($label) { ?>
			<div class="control-label">
				<?php echo $label; ?>
			</div>
			<?php } ?>
			<div<?php if ($label) { ?> class="controls"<?php } ?>>
				<?php echo $input; ?>
			</div>
		</div>
		<?php
	}
	
	public function endFieldset() {
		?>
		</fieldset>
		<?php
	}
	
	protected function escape($text) {
		return htmlentities($text, ENT_COMPAT, 'utf-8');
	}
}