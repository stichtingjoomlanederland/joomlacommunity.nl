<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access'); ?>

<div class="kmt-mod mod_komento_activities kmt-mod-activities<?php echo $params->get( 'moduleclass_sfx' ) ?>">
	<?php foreach ($activities as $activity) {
		$activity->comment = Komento::getComment($activity->comment_id, $process = true);

		$activity->comment->comment = strip_tags( $activity->comment->comment );

		// trim comment length
		if (JString::strlen($activity->comment->comment) > $params->get('maxcommentlength')) {
			$activity->comment->comment = JString::substr($activity->comment->comment, 0, $params->get('maxcommentlength')) . '...';
		}

		// trim title length
		if (JString::strlen($activity->comment->contenttitle) > $params->get('maxtitlelength')) {
			$activity->comment->contenttitle = JString::substr($activity->comment->contenttitle, 0, $params->get('maxtitlelength')) . '...';
		}

		$profile = Komento::getProfile($activity->uid);

		$config = Komento::getConfig($activity->comment->component);

		if ($activity->uid != 0 && $profile->id != $activity->uid && $config->get('enable_orphanitem_convert')) {
			$table = Komento::getTable('activities');
			$table->load($activity->id);
			$table->uid = $config->get('orphanitem_ownership');
			$table->store();

			$activity->uid = $config->get('orphanitem_ownership');
		}
	?>
	<div class="mod-item stream kmt-activity-<?php echo $activity->id; ?>">
		<div class="stream-head stream-<?php echo $activity->type; ?>">
			<i class="stream-type"></i>
			<?php if (!$profile->guest) { ?>
				<a href="<?php echo $profile->getProfileLink(); ?>">
			<?php } ?>
			
			<?php if (!empty($activity->comment->name)) {
				echo $activity->comment->name;
			} else {
				echo $profile->getName();
			} ?>

			<?php if (!$profile->guest) { ?></a><?php } ?> -
				<?php switch( $activity->type ) {
					case 'comment': ?>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_COMMENTED_ON' ); ?>
						<a href="<?php echo $activity->comment->pagelink; ?>"><?php echo $activity->comment->contenttitle; ?></a>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_COMMENTED_IN' ); ?>
						<?php echo $activity->comment->componenttitle; ?>
					<?php break;

					case 'reply': ?>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_REPLIED_TO' ); ?>
						<a href="<?php echo $activity->comment->parentlink; ?>" class="parentLink" parentid="<?php echo $activity->comment->parent_id; ?>"># <?php echo $activity->comment->parent_id; ?></a>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_REPLIED_ON' ); ?>
						<a href="<?php echo $activity->comment->pagelink; ?>"><?php echo $activity->comment->contenttitle; ?></a>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_REPLIED_IN' ); ?>
						<?php echo $activity->comment->componenttitle; ?>
					<?php break;

					case 'like': ?>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_LIKED_ON' ); ?>
						<a href="<?php echo $activity->comment->pagelink; ?>"><?php echo $activity->comment->contenttitle; ?></a>
						<?php echo JText::_( 'COM_KOMENTO_ACTIVITY_LIKED_IN' ); ?>
						<?php echo $activity->comment->componenttitle; ?>
					<?php break; ?>
				<?php } ?>
		</div>

		<?php if ($params->get('showcomment')) { ?>
			<div class="stream-body">
				<div class="mod-comment-text"><?php echo $activity->comment->comment; ?></div>
			</div>
		<?php } ?>

		<div class="stream-foot">
			<div class="mod-comment-meta small">
				<a href="<?php echo $activity->comment->permalink; ?>"><?php echo $activity->comment->created; ?></a>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
