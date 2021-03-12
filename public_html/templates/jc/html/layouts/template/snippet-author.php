<?php

use Joomla\CMS\Layout\LayoutHelper;

defined('JPATH_BASE') or die;

// Load the profile data from the database.
// Required for use of the DiscussHelper
/*require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/easydiscuss.php');

$profile = DiscussHelper::getTable('Profile');
$profile->load($displayData);
$userparams        = DiscussHelper::getRegistry($profile->params);
$profile->twitter  = $userparams->get('twitter', '');
$profile->website  = $userparams->get('website', '');
$profile->facebook = $userparams->get('facebook', '');
$profile->linkedin = $userparams->get('linkedin', '');*/
?>

<div class="row articleinfo">
	<div class="col-sm-2 author-img">
		<?php echo LayoutHelper::render('template.easydiscuss.profile', ['id' => $displayData, 'type' => 'user.avatar', 'size' => 'lg']); ?>
	</div>
	<div class="col-sm-10">
		<h4><?php echo LayoutHelper::render('template.easydiscuss.profile', ['id' => $displayData, 'type' => 'custom', 'field' => 'nickname']); ?></h4>
		<p><?php echo LayoutHelper::render('template.easydiscuss.profile', ['id' => $displayData, 'type' => 'custom', 'field' => 'description']); ?></p>
		<?php echo LayoutHelper::render('template.easydiscuss.socialshare', ['id' => $displayData]); ?>
	</div>
</div>
