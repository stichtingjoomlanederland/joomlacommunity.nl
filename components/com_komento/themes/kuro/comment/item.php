<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<?php // KURO THEME

Komento::trigger( 'onBeforeProcessComment', array( 'component' => $row->component, 'cid' => $row->cid, 'comment' => &$row ) );

// Process data
Komento::import( 'helper', 'comment' );
$row = KomentoCommentHelper::process( $row );

Komento::trigger( 'onAfterProcessComment', array( 'component' => $row->component, 'cid' => $row->cid, 'comment' => &$row ) );

// Construct classes for this row
$classes = array();

$classes[] = 'kmt-item';

// Usergroup CSS classes
if( $row->author->guest )
{
	$classes[] = $system->config->get( 'layout_css_public' );
}
else
{
	$classes[] = $system->config->get( 'layout_css_registered' );
}
if( $row->author->isAdmin() )
{
	$classes[] = $system->config->get( 'layout_css_admin' );
}
if( $row->created_by == $row->extension->getAuthorId() )
{
	$classes[] = $system->config->get( 'layout_css_author' );
}

$usergroups	= $row->author->getUsergroups();
if( is_array( $usergroups ) && !empty( $usergroups ) )
{
	foreach( $usergroups as $usergroup )
	{
		$classes[] = 'kmt-comment-item-usergroup-' . $usergroup;
	}
}

// Extended classes
$classes[] = 'kmt-' . $row->id;
$classes[] = 'kmt-child-' . $row->depth;
if($row->ratings)
{
	$classes[] = 'kmt-star-rated';
}

if( $row->sticked )
{
	$classes[] = 'kmt-sticked';
}

$classes[] = $row->published == 1 ? 'kmt-published' : 'kmt-unpublished';

$classes = implode( ' ', $classes );
?>

<li id="kmt-<?php echo $row->id; ?>" class="<?php echo $classes; ?>" parentid="kmt-<?php echo $row->parent_id; ?>" depth="<?php echo $row->depth; ?>" childs="<?php echo $row->childs; ?>" published="<?php echo $row->published; ?>"<?php if( $system->konfig->get( 'enable_schema' ) ) echo ' itemscope itemtype="http://schema.org/Comment"'; ?>>

<?php // depth and indentation calculation
	$css = '';
	if( $system->config->get( 'enable_threaded' ) )
	{
		$unit = $system->config->get('thread_indentation');
		$total = $unit * $row->depth;

		$css = 'style="margin-left: ' . $total . 'px !important"';

		// support for RTL sites
		// forcertl = 1 for dev purposes
		if( JFactory::getDocument()->direction == 'rtl' || JRequest::getInt( 'forcertl' ) == 1 )
		{
			$css = 'style="margin-right: ' . $total . 'px !important"';
		}
	}
?>
<div class="kmt-wrap" <?php echo $css; ?>>

	<!-- Avatar div.kmt-avatar -->
	<?php echo $this->fetch( 'comment/item/avatar.php' ); ?>

	<!-- User rank div.kmt-rank -->
	<?php if( $system->config->get( 'layout_avatar_enable' ) ) {
		echo $this->fetch( 'comment/item/rank.php' );
	} ?>

	<div class="kmt-content">

		<div class="kmt-head">
			<?php echo $this->fetch( 'comment/item/id.php' ); ?>

			<!-- Name span.kmt-author -->
			<?php echo $this->fetch( 'comment/item/author.php' ); ?>

			<!-- User rank div.kmt-rank -->
			<?php if( !$system->config->get( 'layout_avatar_enable' ) ) {
				echo $this->fetch( 'comment/item/rankauthor.php' );
			} ?>

			<!-- In reply to span.kmt-inreplyto -->
			<?php echo $this->fetch( 'comment/item/inreplyto.php' ); ?>

			<span class="kmt-option float-wrapper">
				<!-- Report Comment span.kmt-report-wrap -->
				<?php echo $this->fetch( 'comment/item/report.php' ); ?>

				<!-- Permalink span.kmt-permalink-wrap -->
				<?php echo $this->fetch( 'comment/item/permalink.php' ); ?>

				<!-- AdminTools span.kmt-admin-wrap -->
				<?php echo $this->fetch( 'comment/item/admintools.php' ); ?>
			</span>

			<!-- START: Pro Version Only -->
			<!-- Ratings -->
			<?php echo $this->fetch('comment/item/ratings.php'); ?>
			<!-- END: Pro Version Only -->
		</div>

		<div class="kmt-body">
			<i></i>

			<!-- Comment div.kmt-text -->
			<?php echo $this->fetch( 'comment/item/text.php' ); ?>

			<!-- START: Pro Version Only -->
			<!-- Attachment div.kmt-attachments -->
			<?php echo $this->fetch( 'comment/item/attachment.php' ); ?>

			<!-- END: Pro Version Only -->

			<!-- Info span.kmt-info -->
			<?php echo $this->fetch( 'comment/item/info.php' ); ?>
		</div>

		<div class="kmt-control">

			<div class="kmt-meta">
				<!-- Time span.kmt-time -->
				<?php echo $this->fetch( 'comment/item/time.php' ); ?>

				<!-- Location span.kmt-location -->
				<?php echo $this->fetch( 'comment/item/location.php' ); ?>
			</div>

			<div class="kmt-control-user float-wrapper">
				<!-- Likes span.kmt-like-wrap -->
				<?php echo $this->fetch( 'comment/item/likesbutton.php' ); ?>

				<!-- Share div.kmt-share-wrap -->
				<?php echo $this->fetch( 'comment/item/sharebutton.php' ); ?>

				<!-- Reply span.kmt-reply-wrap -->
				<?php echo $this->fetch( 'comment/item/replybutton.php' ); ?>
			</div>
		</div>
	</div>
</div>
</li>
