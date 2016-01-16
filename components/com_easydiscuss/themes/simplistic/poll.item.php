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
?>
<script type="text/javascript">
EasyDiscuss.require().script('newpost').done(function($){
	$('.discuss-vote-polls').implement('EasyDiscuss.Controller.DiscussNewPost',{

	});
});
</script>
<div class="discuss-vote-polls">
	<div class="clear"></div>

	<p class="poll-title" style="font-weight: 700;">
		<?php if( $pollQuestion ){ ?>
			<?php echo JText::_( $pollQuestion ); ?>
		<?php } else { ?>
			<?php echo JText::_( 'COM_EASYDISCUSS_SHARE_OPINION' );?>
		<?php } ?>
	</p>

<ul id="discuss-poll" class="discuss-polls reset-ul mt-10">
<?php foreach( $polls as $poll ){ ?>
<li class="discuss-poll">



	<?php if( $poll->get('multiple_polls') == null ){ ?>
		<?php if( $system->my->id > 0 ){ ?>
		<input type="radio" name="poll" id="poll-<?php echo $poll->get( 'id');?>" onchange="discuss.polls.vote( this );"<?php echo $poll->voted( $system->my->id ) ? ' checked="checked"' : '';?> value="<?php echo $poll->get( 'id');?>" class="poll-input" />
		<?php } ?>
	<?php } else if ( $poll->get('multiple_polls') == 1 ){ ?>
		<?php if( $system->my->id > 0 ){ ?>
		<input type="checkbox" name="poll" id="poll-<?php echo $poll->get( 'id');?>" onchange="discuss.polls.vote( this );"<?php echo $poll->voted( $system->my->id ) ? ' checked="checked"' : '';?> value="<?php echo $poll->get( 'id');?>" class="poll-input" />
		<?php } ?>
	<?php } ?>


<!--
	<?php if( $poll->get('multiple_polls') == null ){ ?>
		<?php if( $system->my->id > 0 ){ ?>
		<input type="radio" name="poll" id="<?php echo $poll->get( 'id' );?>" class="radioBtn"<?php echo $poll->voted( $system->my->id ) ? ' checked="checked"' : '';?> value="<?php echo $poll->get( 'id');?>" class="poll-input" />
		<?php } ?>
	<?php } else if ( $poll->get('multiple_polls') == 1 ){ ?>
		<?php if( $system->my->id > 0 ){ ?>
		<input type="checkbox" name="poll" id="<?php echo $poll->get( 'id' );?>" class="chkboxBtn"<?php echo $poll->voted( $system->my->id ) ? ' checked="checked"' : '';?> value="<?php echo $poll->get( 'id');?>" class="poll-input" />
		<?php } ?>
	<?php } ?>
-->



	<span id="poll-voters-<?php echo $poll->get( 'id');?>" class="poll-voters">
		<?php

		$this->set( 'percentage' , $poll->getPercentage() );
		$this->set( 'voters' , $poll->getVoters( 3 ) );
		echo $this->loadTemplate( 'poll.voters.php' );
		?>
	</span>


	<span class="poll-count">
		<a href="javascript:void(0);" onclick="discuss.polls.showVoters('<?php echo $poll->id;?>');" id="poll-count-<?php echo $poll->get( 'id' );?>">
			<?php echo $this->getNouns( 'COM_EASYDISCUSS_POLLS_VOTE' , $poll->get( 'count' ) , true ); ?>
		</a>
	</span>




	<label for="poll-<?php echo $poll->get( 'id' );?>" title="<?php echo $poll->get( 'value' );?>"><?php echo $poll->get( 'value' );?></label>
</li>
<?php } ?>
</ul>
<?php echo $this->loadTemplate( 'poll.unvote.php' ); ?>
</div>
