<?php
/**
 * Shlib - programming library
 *
 * @author       Yannick Gaultier
 * @copyright    (c) Yannick Gaultier 2015
 * @package      shlib
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version      0.3.1.522
 */

/**
 * This layout displays an information message, initially collapsed
 * with a button to expand it
 */

defined('_JEXEC') or die;

/**
 * This layout displays an information message, initially collapsed
 * with a button to expand it
 */

$id = sha1(mt_rand());
$type = empty($displayData['type']) ? 'info' : $displayData['type'];
?>

<!-- shLib collapsible message -->
<section id="shl-wrapper-<?php echo $id; ?>" class="shl-collapsed-message-wrapper shl-collapsed-message-wrapper-<?php echo $type; ?>">

	<?php if(!empty($displayData['close'])) : ?>
		<button type="button" class="shl-collapsed-message-close" onclick="javascript: document.getElementById('shl-wrapper-<?php echo $id; ?>').style.display='none';">&times;</button>
	<?php endif; ?>

	<div id="shl-body-<?php echo $id; ?>" class="shl-collapsed-message collapse">

		<?php
		if (!empty($displayData['title']))
		{
			echo '<h1>' . $displayData['title'] . '</h1>';
		}
		echo $displayData['content'];
		?>

	</div>

	<?php if(!empty($displayData['collapse'])) : ?>
		<button type="button" class="shl-collapsed-message" data-toggle="collapse"
		        data-target="#shl-body-<?php echo $id; ?>"></button>
	<?php endif; ?>
</section>
<!-- shLib collapsible message -->
