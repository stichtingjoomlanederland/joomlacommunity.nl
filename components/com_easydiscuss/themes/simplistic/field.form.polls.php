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

$pollQuestion	= false;
$pollMultiple 	= false;
$polls 			= false;

if( isset( $post ) )
{

	$poll = $post->getPollQuestion();

// 	$poll = DiscussHelper::getTable( 'PollQuestion' );
// 	$poll->loadByPost( $post->id );

	$pollQuestion	= $poll->title;
	$pollMultiple	= $poll->multiple;

	$polls 			= $post->getPolls();
}
?>

<script type="text/javascript">
EasyDiscuss
	.require()
	.script('polls')
	.done(function($){
		EasyDiscuss.module("<?php echo $composer->id; ?>")
			.done(function(){
				$('#pollsTab-<?php echo $composer->id; ?>').implement(EasyDiscuss.Controller.Polls.Form);
			});
	});
</script>

<div id="pollsTab-<?php echo $composer->id; ?>" class="tab-pane polls-tab">
	<div class="discussPolls field-polls">
		<p><?php echo JText::_( 'COM_EASYDISCUSS_POLLS_DESC' );?></p>


		<div class="pollForm">
			<div class="control-group">
				<input type="text" class="input-xlarge" name="poll_question" placeholder="<?php echo JText::_( 'COM_EASYDISCUSS_POLL_QUESTION_PLACEHOLDER' , true ); ?>" value="<?php echo $this->escape($pollQuestion ? $pollQuestion : '');?>" />
			</div>
			<div class="control-group">
				<label><?php echo JText::_( 'COM_EASYDISCUSS_POLL_ANSWERS' );?></label>

				<?php if( $system->config->get( 'main_polls_multiple' ) ){ ?>
				<label class="checkbox">
					<input type="checkbox" class="input float-l" name="multiplePolls" value="1"<?php echo $this->escape($pollMultiple) ? ' checked="checked"' : '';?>/>
					<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_MULTIPLE_POLL_VOTES' );?>
				</label>
				<?php } ?>
			</div>


			<ul class="polls-list unstyled mb-10 pollAnswersList">
				<?php if( $polls ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $polls as $poll ){ ?>
					<li class="pollAnswers mb-5">
						<div class="input-append">
							<input type="text" name="pollitems[]" class="input input-xlarge pollAnswerText" value="<?php echo $this->escape($poll->value); ?>" />
							<input type="hidden" name="pollitemsOri[]" value="<?php echo $this->escape($poll->value); ?>" />

						<?php if( $i != 0 ){ ?>
							<a href="javascript:void(0);" class="btn btn-danger remove-att removeItem" data-pollid="<?php echo $poll->id;?>"><i class="icon-remove"></i></a>
						<?php } ?>
						</div>
					</li>
						<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</ul>
			<a href="javascript:void(0);" class="btn btn-small btn-success insertPollAnswer">
				<i class="icon-plus"></i> <?php echo JText::_( 'COM_EASYDISCUSS_ADD_POLL_ITEM' );?>
			</a>
		</div>
		<input type="hidden" name="pollsremove" class="pollsremove" id="pollsremove" value="" />
	</div>
</div>
