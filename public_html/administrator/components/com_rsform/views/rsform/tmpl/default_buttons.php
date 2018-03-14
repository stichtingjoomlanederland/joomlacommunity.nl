<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

$rows = array_chunk($this->buttons, 4);
foreach ($rows as $buttons) {
?>
	<div class="dashboard-container">
	<?php foreach ($buttons as $button) { ?>
		<?php if ($button['access']) { ?>
			<div class="dashboard-info dashboard-button">
				<a <?php if ($button['target']) { ?> target="<?php echo $this->escape($button['target']); ?>"<?php } ?> href="<?php echo $button['link']; ?>"> 
					<img src="<?php echo $button['image']; ?>" alt="<?php echo $button['text']; ?>" />
					<span class="dashboard-title"><?php echo $button['text']; ?></span> 
				</a> 
			</div>
		<?php } ?>
	<?php } ?>
	</div>
<?php
}