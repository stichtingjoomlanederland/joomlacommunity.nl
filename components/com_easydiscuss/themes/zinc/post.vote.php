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
EasyDiscuss
.require()
.script( 'votes' )
.done(function($){

	$( '.discussVote-<?php echo $post->id;?>' ).implement(
		EasyDiscuss.Controller.Votes ,
		{
			viewVotes : <?php echo !$system->config->get( 'main_allowguestview_whovoted' ) && !$system->my->id ? 'false' : 'true'; ?>
		}
	);
});
</script>
<div class="discuss-vote">
	<div class="discussVote-<?php echo $post->id;?>" data-postid="<?php echo $post->id;?>">
		<?php // if( $access->canVote() && !$post->isVoted ){ ?>
		<a href="javascript:void(0);" class="vote-up voteUp"><i class="i i-angle-up"></i></a>
		<?php // } ?>
		<div class="vote-points pos-r votePoints">
			<b><?php echo $post->sum_totalvote;?></b>
		</div>
		<?php // if( $access->canVote() && !$post->isVoted ){ ?>
		<a href="javascript:void(0);" class="vote-down voteDown"><i class="i i-angle-down"></i></a>
		<?php // } ?>
	</div>
</div>
