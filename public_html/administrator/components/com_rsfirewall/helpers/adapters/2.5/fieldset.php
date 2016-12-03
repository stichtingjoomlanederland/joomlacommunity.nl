<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

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
	
	public function showField($label, $input) {
		?>
		<li>
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
}