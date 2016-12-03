<?php
defined('JPATH_BASE') or die;

$url = JURI::current();

$title = $displayData['item']->title;
$title = str_replace('&', 'and', $title);
$title = str_replace('| ', '', $title);
?>

<?php if ($displayData['title']) : ?>
<strong><?php echo $displayData['title']; ?></strong>
<?php endif; ?>
<ul class="list-inline share-buttons">
	<?php if ($displayData['twitter']) : ?>
		<li class="share-twitter">
			<a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&via=funx&text=<?php echo $title; ?>"
			   onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=400');return false;">
				<span class="icon icon-twitter"></span>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($displayData['facebook']) : ?>
		<li class="share-facebook">
			<a href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>"
			   onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=400');return false;">
				<span class="icon icon-facebook"></span>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($displayData['googleplus']) : ?>
		<li class="share-googleplus">
			<a href="https://plus.google.com/share?url=<?php echo $url; ?>"><span
					class="icon icon-googleplus"></span></a>
		</li>
	<?php endif; ?>
</ul>
