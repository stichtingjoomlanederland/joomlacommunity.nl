<?php
defined('JPATH_BASE') or die;

$url = JURI::current();

$title = $displayData['item']->title;
$title = str_replace('&', 'and', $title);
$title = str_replace('| ', '', $title);
$inline = isset($displayData['inline']) ? $displayData['inline'] : false;
?>

<?php if ($displayData['title'] && !$inline) : ?>
<p class="article-meta-label"><?php echo $displayData['title']; ?></p>
<?php endif; ?>
<ul class="list-inline share-buttons">
	<?php if ($inline) : ?>
        <li class="share-title">
	        <?php echo $displayData['title']; ?>
        </li>
    <?php endif;?>
	<?php if ($displayData['twitter']) : ?>
		<li class="share-twitter">
			<a href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&via=joomlacommunity&text=<?php echo $title; ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=400');return false;">
                <i class="fa fa-twitter-square" aria-hidden="true"></i>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($displayData['facebook']) : ?>
		<li class="share-facebook">
			<a href="http://www.facebook.com/sharer.php?u=<?php echo $url; ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=400');return false;">
                <i class="fa fa-facebook-square" aria-hidden="true"></i>
			</a>
		</li>
	<?php endif; ?>

	<?php if ($displayData['linkedin']) : ?>
        <li class="share-linkedin">
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $url; ?>&title=<?php echo $title; ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=400,width=400');return false;">
                <i class="fa fa-linkedin-square" aria-hidden="true"></i>
            </a>
        </li>
	<?php endif; ?>
</ul>
