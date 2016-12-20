<?php
defined('JPATH_BASE') or die;

// Load the profile data from the database.
// Required for use of the DiscussHelper
require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

$profile = DiscussHelper::getTable('Profile');
$profile->load($displayData);
$userparams        = DiscussHelper::getRegistry($profile->params);
$profile->twitter  = $userparams->get('twitter', '');
$profile->website  = $userparams->get('website', '');
$profile->facebook = $userparams->get('facebook', '');
$profile->linkedin = $userparams->get('linkedin', '');
?>

<div class="row articleinfo">
	<div class="col-sm-2 author-img">
		<a href="<?php echo $profile->getLink(); ?>">
			<img class="img-circle" src="<?php echo $profile->getAvatar(); ?>"/>
		</a>
	</div>
	<div class="col-sm-10">
		<h4><a href="<?php echo $profile->getLink(); ?>"><?php echo $profile->nickname; ?></a></h4>
		<p class="text-muted"><?php echo($profile->description); ?></p>
		<ul class="list-inline share-buttons">
			<?php if ($profile->twitter): ?>
				<li class="share-twitter">
					<a href="<?php echo($profile->twitter); ?>" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
				</li>
			<?php endif; ?>
			<?php if ($profile->facebook): ?>
				<li class="share-facebook">
					<a href="<?php echo($profile->facebook); ?>" target="_blank"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
				</li>
			<?php endif; ?>
			<?php if ($profile->linkedin): ?>
				<li class="share-linkedin">
					<a href="<?php echo($profile->linkedin); ?>" target="_blank"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
				</li>
			<?php endif; ?>
			<?php if ($profile->website): ?>
				<li class="share-website">
					<a href="<?php echo($profile->website); ?>" target="_blank"><i class="fa fa-globe" aria-hidden="true"></i></a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>