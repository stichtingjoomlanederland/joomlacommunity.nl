<?php
defined('JPATH_BASE') or die;

require_once(JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php');

$profile = DiscussHelper::getTable('Profile');
$profile->load($displayData);
?>

<div class="forum-avatar">
	<a class="auteur-image" href="<?php echo $profile->getLink(); ?>">
		<img src="<?php echo $profile->getAvatar(); ?>" alt="<?php echo $profile->nickname; ?>"/>
	</a>
</div>