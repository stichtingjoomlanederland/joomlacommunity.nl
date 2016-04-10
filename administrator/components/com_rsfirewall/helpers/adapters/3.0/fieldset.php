<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

class RSFieldset {
	public function startFieldset($legend='', $class='adminform form-horizontal') {
		?>
		<fieldset class="<?php echo $class; ?>">
		<?php if ($legend) { ?>
		<legend><?php echo $legend; ?></legend>
		<?php }
	}
	
	public function showField($label, $input) {
		?>
		<div class="control-group">
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
}