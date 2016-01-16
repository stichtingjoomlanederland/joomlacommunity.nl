<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');



if( $isDiscussion && !$system->config->get( 'main_polls' ) )
{
	return;
}

if( !$isDiscussion && !$system->config->get( 'main_polls_replies' ) )
{
	return;
}

$pollQuestion 	= $post->getPollQuestion();
$polls 			= $post->getPolls();
$isPollLocked	= $post->isPollLocked();

// If there's no polls, just skip this.
if( !$polls )
{
	return;
}

$unique_poll_id = 'discuss-polls-' . rand();
?>
<script type="text/javascript">
EasyDiscuss
	.require()
	.script( 'polls' )
	.done(function($){
		$('.<?php echo $unique_poll_id; ?> .pollAnswer').implement( EasyDiscuss.Controller.Polls.Answers );
	});
</script>

<div class="discussPolls discuss-polls<?php echo $isPollLocked ? ' alert' : '';?> <?php echo $unique_poll_id; ?>">

	<!-- @php START polls new dom -->
	<?php if( $pollQuestion->title ) { ?>
	<div class="discuss-polls-hd">
		<h4 class="poll-title">
			<?php echo $this->escape( $pollQuestion->title ); ?>
		</h4>
	</div>
	<?php } ?>

	<div class="pa-10 poll-locked" id="poll_notice_<?php echo $post->id;?>" style="display:<?php echo ( $isPollLocked ) ? 'block' : 'none'; ?>">
		<i class="icon-lock"></i> <?php echo JText::_( 'COM_EASYDISCUSS_POLL_IS_LOCKED' ); ?>
	</div>


	<div class="discuss-polls-bd">
		<ul class="unstyled">
			<?php foreach ($polls as $poll) {
				$isVoted    = ( isset( $poll->meVoted ) ) ? $poll->meVoted : $poll->voted( $system->my->id );
				$totalVoted = ( isset( $poll->totalVoted ) ) ? $poll->totalVoted : null;
			?>
				<li class="pollAnswer pollAnswerItem-<?php echo $poll->id; ?>" data-id="<?php echo $poll->id; ?>">

					<label class="checkbox" for="poll-count-<?php echo $poll->id; ?>" title="<?php echo $this->escape($poll->get( 'value' )); ?>">
						<?php if( ($system->config->get( 'main_polls_guests' )) || ($system->my->id > 0) ){ ?>
						<input
							id="poll-count-<?php echo $poll->get( 'id'); ?>"
							class="votePoll"
							type="<?php echo ( $pollQuestion && $pollQuestion->multiple ) ? 'checkbox' : 'radio' ?>"
							name="poll"
							value="<?php echo $poll->get( 'id'); ?>"
							<?php echo ( $isVoted)  ? ' checked="true"' : '';?>
							<?php echo ( $isPollLocked ) ? ' disabled="disabled"' : '';?>
						>
						<?php } ?>
						<?php echo $this->escape($poll->get( 'value' )); ?> (<span class="pollPercentage"><?php echo $poll->getPercentage( $totalVoted ); ?></span>%)
					</label>

					<div class="progress progress-success progress-striped">
						<div style="width: <?php echo $poll->getPercentage( $totalVoted ); ?>%;" class="bar pollGraph"></div>
						<span class="poll-count">
							<?php if( $system->config->get( 'main_polls_avatars' ) ){ ?>
								<a class="voteCount" id="poll-count-<?php echo $poll->id;?>" href="javascript:void(0);"><?php echo $this->getNouns('COM_EASYDISCUSS_VOTE_COUNT' , $poll->count , true ); ?></a>
							<?php } else { ?>
								<span class="voteCount"><?php echo $this->getNouns('COM_EASYDISCUSS_VOTE_COUNT' , $poll->count , true ); ?></span>
							<?php } ?>
						</span><!-- when clicked show out the voters list at the <ul class="discuss-voters"> -->
					</div>
				</li>
			<?php } ?>
		</ul>
	</div>

	<?php if( $system->config->get( 'main_polls_avatars' ) ){ ?>
	<div class="discuss-polls-ft">
		<!-- total voters list -->
		<ul class="discuss-voters unstyled votersAvatar votersList">
			<?php $limit = 10; // May specify the limit here ?>
			<?php
				$voters = $pollQuestion->getVoters($limit);
				$votersCount = count($voters);
				if( $votersCount > 0 ) {
					foreach( $voters as $voter )
					{
						echo $this->loadTemplate( 'poll.voters.php' , array( 'voter' => $voter ) );
					}
				}
			?>
		</ul>
	</div>

	<?php
		$voterCnt   = $pollQuestion->getVotersCount();

		if( $votersCount && $votersCount < $voterCnt ) {
			$count = $voterCnt - $votersCount;
	?>
		<div>
			<?php echo $this->getNouns('COM_EASYDISCUSS_POLLS_VOTERS' , $count , true );?>
		</div>
	<?php } ?>

	<?php } ?>
	<!-- @php END polls new dom -->
</div>
